<?php

namespace Stats4sd\KoboLink\Jobs;

;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Events\KoboDeploymentReturnedSuccess;
use Stats4sd\KoboLink\Models\TeamXlsform;

class DeploymentSuccessMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user;
    public TeamXlsform $form;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, TeamXlsform $form)
    {
        $this->user = $user;
        $this->form = $form;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // emit Laravel event
        event(new KoboDeploymentReturnedSuccess($this->user, $this->form));
    }
}
