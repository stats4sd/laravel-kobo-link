<?php

namespace Stats4sd\KoboLink\Jobs;

use App\Models\TeamXlsform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeployFormToKobo implements ShouldQueue
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
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(): void
    {

        //if form is not already on Kobo, create asset...
        if (! $this->form->kobo_id) {
            // Create new Kobo Asset
            $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(["Accept" => "application/json"])
            ->post(config('kobo-link.kobo.endpoint')."/api/v2/assets/", [
                "name" => $this->form->title,
                "asset_type" => "survey",
            ])
            ->throw() // throw error and halt if 4** or 5**
            ->json();

            $this->form->update([
                'kobo_id' => $response['uid'],
            ]);
        }

        // Dispatch next job in sequence
        UploadXlsFormToKobo::dispatch($this->form, $this->user);
    }
}
