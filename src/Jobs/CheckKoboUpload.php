<?php

namespace Stats4sd\KoboLink\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Events\KoboUploadReturnedError;
use Stats4sd\KoboLink\Events\KoboUploadReturnedSuccess;
use Stats4sd\KoboLink\Jobs\MediaFiles\GenerateCsvLookupFiles;
use Stats4sd\KoboLink\Jobs\MediaFiles\UploadMediaFileAttachmentsToKoboForm;
use Stats4sd\KoboLink\Models\XlsForm;

class CheckKoboUpload implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user;
    public $form;
    public $importUid;

    public $tries = 50;
    public $maxExceptions = 1;

    /**
     * Create a new job instance.
     * @param User $user
     * @param Xlsform $form
     * @param String $importUid
     * @return void
     */
    public function __construct(User $user, Xlsform $form, String $importUid)
    {
        $this->user = $user;
        $this->form = $form;
        $this->importUid = $importUid;
    }

    /**
     * Execute the job.
     *
     * @return void
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


        \Log::info("importCheck");
        \Log::info($importCheck);

        $importStatus = $importCheck['status'];

        if ($importStatus === "processing") {
            $this->release('5');
        }

        // Failed import still returns 200, so check for import status:
        if ($importStatus === 'error') {
            \Log::error("Kobo Upload Error: Type = " . $importCheck['messages']['error_type']);
            \Log::error("Error Message = " . $importCheck['messages']['error']);

            event(new KoboUploadReturnedError(
                $this->user,
                $this->form,
                $importCheck['messages']['error_type'],
                $importCheck['messages']['error']
            ));

            $this->form->update([
                'processing' => false,
            ]);

            $this->fail();
        }

        if ($importStatus == "complete") {
            event(new KoboUploadReturnedSuccess(
                $this->user,
                $this->form
            ));

            // run other actions on Kobo that required a successfully imported form:
            Bus::chain([
                new UpdateFormNameOnKobo($this->form),
                new SetKoboFormToActive($this->user, $this->form),
                new GenerateCsvLookupFiles($this->form),
                new UploadMediaFileAttachmentsToKoboForm($this->form),
                new ShareFormWithUsers($this->form),
                new DeploymentSuccessMessage($this->user, $this->form),
            ])->dispatch($this->form);
        }
    }
}
