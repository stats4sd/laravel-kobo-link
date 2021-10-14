<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\TeamXlsform;

/**
 * Job to handle replacing / uploading ALL media files for the passed TeamXlsform.
 *  - Deletes the old versions off Kobotoolbox
 *  - Takes the media and csv_lookup properties of the passed TeamXlsform, and passes each file to the uploader
 */
class UploadMediaFileAttachmentsToKoboForm implements ShouldQueue
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
        //
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

        \Log::info($mediaFiles);

        // delete any existing media from form to make way for fresh upload:
        foreach ($mediaFiles['results'] as $key => $file) {
            \Log::info('deleting existing file...');
            \Log::info($file);
            if ($file['file_type'] === "form_media") {
                Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
                ->delete($file['url'])
                ->throw();
            }
        }

        $mediaCollection = $this->form->xlsform->media;
        $csvLookups = $this->form->xlsform->csv_lookups;

        if ($mediaCollection && is_countable($mediaCollection)) {
            foreach ($mediaCollection as $media) {
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
