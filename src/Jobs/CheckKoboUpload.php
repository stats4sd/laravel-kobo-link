<?php

namespace Stats4sd\KoboLink\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Log;
use Stats4sd\KoboLink\Events\KoboUploadReturnedError;
use Stats4sd\KoboLink\Events\KoboUploadReturnedSuccess;
use Stats4sd\KoboLink\Jobs\MediaFiles\GenerateCsvLookupFiles;
use Stats4sd\KoboLink\Jobs\MediaFiles\UploadMediaFileAttachmentsToKoboForm;
use Stats4sd\KoboLink\Models\TeamXlsform;

class CheckKoboUpload implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 50;
    public int $maxExceptions = 1;

    /**
     * Create a new job instance.
     * @param $user
     * @param TeamXlsform $form
     * @param String $importUid
     * @return void
     */
    public function __construct(public TeamXlsform $form, public String $importUid, public $user = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws RequestException
     */
    public function handle()
    {
        $importCheck = Http::withBasicAuth(
            config('kobo-link.kobo.username'),
            config('kobo-link.kobo.password')
        )
        ->withHeaders(["Accept" => "application/json"])
        ->get(config('kobo-link.kobo.endpoint') . '/imports/' . $this->importUid . '/')
        ->throw()
        ->json();


        $importStatus = $importCheck['status'];

        if ($importStatus === "processing") {
            $this->release(5);
        }

        // Failed import still returns 200, so check for import status:
        if ($importStatus === 'error') {
            Log::error("Kobo Upload Error: Type = " . $importCheck['messages']['error_type']);
            Log::error("Error Message = " . $importCheck['messages']['error']);

            event(new KoboUploadReturnedError(
                $this->form,
                $importCheck['messages']['error_type'],
                $importCheck['messages']['error'],
                $this->user,
            ));

            $this->form->update([
                'processing' => false,
            ]);

            $this->fail();
        }

        if ($importStatus == "complete") {
            event(new KoboUploadReturnedSuccess(
                $this->form,
                $this->user
            ));


            // run other actions on Kobo that required a successfully imported form:
            Bus::chain([
                    new UpdateFormNameOnKobo($this->form),
                    new SetKoboFormToActive($this->form, $this->user),
                    new GenerateCsvLookupFiles($this->form),
                    new UploadMediaFileAttachmentsToKoboForm($this->form),
                    new ShareFormWithUsers($this->form),

                    new SetKoboFormToActive($this->form, $this->user),
                    new DeploymentSuccessMessage($this->form, $this->user),
                ])->dispatch($this->form);
        }
    }
}
