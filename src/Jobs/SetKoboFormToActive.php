<?php

namespace Stats4sd\KoboLink\Jobs;

;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Events\KoboDeploymentReturnedError;
use Stats4sd\KoboLink\Models\TeamXlsform;

class SetKoboFormToActive implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $maxExceptions = 1;

    /**
     * Create a new job instance.
     * @param $user
     * @param TeamXlsform $form
     * @return void
     */
    public function __construct(public TeamXlsform $form, public $user = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws RequestException
     * @throws \JsonException
     */
    public function handle(): void
    {
        // Deployment already exists, so get new version_id to update deployment
        if ($this->form->kobo_version_id) {
            $getVersion = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(['Accept' => 'application/json'])
            ->get(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/')
            ->throw()
            ->json();

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

        // on failure, finish processing and send error event;
        if ($response->failed()) {
            $this->form->update([
                'processing' => false,
            ]);
            event(new KoboDeploymentReturnedError($this->form, 'Deployment Error', json_encode($response->json(), JSON_THROW_ON_ERROR), $this->user));
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
