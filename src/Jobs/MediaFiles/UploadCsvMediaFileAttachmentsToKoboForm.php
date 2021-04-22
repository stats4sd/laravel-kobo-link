<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Stats4sd\KoboLink\Models\XlsForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Version of the uploadMediaFileAttachments job that ONLY handles csv files. All non .csv files are ignored. Use this to avoid replacing lots of large image / multimedia files on Kobotools.
 * @param Xlsform $xlform
 */
class UploadCsvMediaFileAttachmentsToKoboForm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $xlform;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Xlsform $xlform)
    {
        $this->$xlform = $xlform;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // media upload still works on the OLD Kobo Api, so we need the OLD formid:

        $oldIdResponse = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->get(config('kobo-link.kobo.old_endpoint').'/api/v1/forms?id_string='.$this->xlform->kobo_id)
        ->throw()
        ->json();

        $koboform = $oldIdResponse[0];


        // delete any existing media from form to make way for fresh upload:
        foreach ($koboform['metadata'] as $metadata) {
            if ($metadata['data_type'] === "media" && $metadata['data_file_type'] === "text/csv") {
                Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
                ->delete(config('kobo-link.kobo.old_endpoint').'/api/v1/metadata/'.$metadata['id'])
                ->throw();
            }
        }

        foreach ($this->xlform->media as $media) {

            // if the file is not a csv, ignore it
            if (!Str::endsWith($media, 'csv')) {
                continue;
            }

            UploadFileToKoboForm::dispatch($media, $koboform);
        }

        foreach ($this->xlform->csv_lookups as $csvMedia) {
            UploadFileToKoboForm::dispatch($csvMedia['csv_name'].'.csv', $koboform);
        }
    }
}
