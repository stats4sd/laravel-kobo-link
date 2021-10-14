<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\TeamXlsform;

/**
 * Version of the uploadMediaFileAttachments job that ONLY handles csv files. All non .csv files are ignored. Use this to avoid replacing lots of large image / multimedia files on Kobotoolbox.
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
        $this->form = $form;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mediaFiles = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->get(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/files/')
        ->throw()
        ->json();


        // delete any existing media from form to make way for fresh upload:
        foreach ($mediaFiles as $file) {
            if ($file['file_type'] === "form_media" && $file['metadata']['mimetype'] === "text/csv") {
                Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
                ->delete(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/'.$file['uid'])
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

                UploadFileToKoboForm::dispatch($media, $this->form);
            }
        }

        if ($csvLookups && is_countable($csvLookups)) {
            foreach ($csvLookups as $csvMedia) {
                $filePath = $csvMedia['per_team'] === "1" ? $this->form->team->id.'/'.$this->form->xlsform->id.'/'.$csvMedia['csv_name'] : $this->form->xlsform->id.'/'.$csvMedia['csv_name'];

                UploadFileToKoboForm::dispatch($filePath . '.csv', $this->form);
            }
        }
    }
}
