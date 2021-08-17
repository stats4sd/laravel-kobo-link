<?php

namespace Stats4sd\KoboLink\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Events\KoboArchiveRequestReturnedError;
use Stats4sd\KoboLink\Events\KoboArchiveRequestReturnedSuccess;
use Stats4sd\KoboLink\Models\TeamXlsForm;

class ArchiveKoboForm implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user;
    public TeamXlsForm $form;

    /**
     * Create a new job instance.
     * @param $user, -- should be an instance of your app's User model
     * @param TeamXlsForm $form
     * @return void
     */
    public function __construct($user = null, TeamXlsForm $form)
    {
        //
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
        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->patch(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/deployment/', [
            'active' => false,
        ])->throw(function ($response, $e) {
            event(new KoboArchiveRequestReturnedError($this->user, $this->form, 'Archive Error', json_encode($response->json())));
        });

        $this->form->update([
            'enketo_url' => null,
            'is_active' => false,
        ]);

        event(new KoboArchiveRequestReturnedSuccess($this->user, $this->form));
    }
}
