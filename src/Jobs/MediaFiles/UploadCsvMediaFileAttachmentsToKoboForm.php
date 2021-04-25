<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Stats4sd\KoboLink\Models\TeamXlsform;

/**
 * Version of the uploadMediaFileAttachments job that ONLY handles csv files. All non .csv files are ignored. Use this to avoid replacing lots of large image / multimedia files on Kobotools.
 * @param TeamXlsform $xlform
 */
class UploadCsvMediaFileAttachmentsToKoboForm implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public TeamXlsform $form;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TeamXlsform $form)
    {
        $this->$form = $form;
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
        ->get(config('kobo-link.kobo.old_endpoint').'/api/v1/forms?id_string='.$this->form->kobo_id)
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

        $mediaCollection = $this->form->xlsform->media;
        $csvLookups = $this->form->xlsform->csv_lookups;
        if ($mediaCollection && is_countable($mediaCollection)) {
            foreach ($mediaCollection as $media) {

            // if the file is not a csv, ignore it
                if (! Str::endsWith($media, 'csv')) {
                    continue;
                }

                UploadFileToKoboForm::dispatch($media, $koboform);
            }
        }

        if ($csvLookups && is_countable($csvLookups)) {
            foreach ($csvLookups as $csvMedia) {
                UploadFileToKoboForm::dispatch($csvMedia['csv_name'] . '.csv', $koboform);
            }
        }
    }
}
