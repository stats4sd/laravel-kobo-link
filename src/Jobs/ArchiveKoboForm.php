<?php

namespace Stats4sd\KoboLink\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Log;
use Stats4sd\KoboLink\Events\KoboArchiveRequestReturnedError;
use Stats4sd\KoboLink\Events\KoboArchiveRequestReturnedSuccess;
use App\Models\TeamXlsform;

class ArchiveKoboForm implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     * @param $user , -- should be an instance of your app's User model
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
     */
    public function handle(): void
    {
        Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(['Accept' => 'application/json'])
            ->patch(config('kobo-link.kobo.endpoint_v2') . '/assets/' . $this->form->kobo_id . '/deployment/', [
                'active' => false,
            ])->throw(function ($response) {
                Log::error('Archive Error');
                Log::error($response->json());
                if ($this->user) {
                    event(new KoboArchiveRequestReturnedError($this->form, 'Archive Error', json_encode($response->json(), JSON_THROW_ON_ERROR), $this->user));
                }
            });

        $this->form->update([
            'enketo_url' => null,
            'is_active' => false,
        ]);


        event(new KoboArchiveRequestReturnedSuccess($this->form, $this->user));

    }
}
