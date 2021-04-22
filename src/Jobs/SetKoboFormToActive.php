<?php

namespace Stats4sd\KoboLink\Jobs;

use App\Models\User;
use Stats4sd\KoboLink\Models\XlsForm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Events\KoboDeploymentReturnedError;

class SetKoboFormToActive implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user;
    public $form;
    public $tries = 3;
    public $maxExceptions = 1;

    /**
     * Create a new job instance.
     * @param User $user
     * @param Xlsform $form
     * @return void
     */
    public function __construct(User $user, Xlsform $form)
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

        // Deployement already exists, so get new version_id to update deployment
        if ($this->form->kobo_version_id) {
            $getVersion = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(['Accept' => 'application/json'])
            ->get(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/')
            ->throw()
            ->json();
            \Log::info(json_encode($getVersion));
            $newVersionId = $getVersion['version_id'];

            // update deployment with new version
            $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
                ->withHeaders(['Accept' => 'application/json'])
                ->patch(config('kobo-link.kobo.endpoint_v2') . '/assets/' . $this->form->kobo_id . '/deployment/', [
                    'active' => true,
                    'version_id' => $newVersionId,
                ]);
        }

        // Deployment doesn't exist for this form, so POST;
        else {
            $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(['Accept' => 'application/json'])
            ->post(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/deployment/', [
                'active' => true,
            ]);
        }

        if ($response->failed()) {
            $this->form->update([
                'processing' => false,
            ]);
            event(new KoboDeploymentReturnedError($this->user, $this->form, 'Deployment Error', json_encode($response->json())));
            $this->fail();
        }

        $response = $response->json();

        $this->form->update([
            'kobo_version_id' => $response['version_id'],
            'enketo_url' => $response['asset']['deployment__links']['url'],
            'is_active' => true,
            'processing' => false,
        ]);
    }
}
