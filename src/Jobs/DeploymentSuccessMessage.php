<?php

namespace Stats4sd\KoboLink\Jobs;

;
use App\Models\TeamXlsform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Events\KoboDeploymentReturnedSuccess;

class DeploymentSuccessMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public TeamXlsform $form, public $user = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // emit Laravel event
        event(new KoboDeploymentReturnedSuccess($this->form, $this->user));
    }
}
