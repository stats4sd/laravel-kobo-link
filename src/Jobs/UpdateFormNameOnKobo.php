<?php

namespace Stats4sd\KoboLink\Jobs;

use Illuminate\Bus\Queueable;
use Stats4sd\KoboLink\Models\XlsForm;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateFormNameOnKobo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $form;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Xlsform $form)
    {
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
        ->withHeaders(["Accept" => "application/json"])
        ->patch(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/', [
            'name' => $this->form->title,
        ])
        ->throw()
        ->json();

        \Log::info("form name updated");
        \Log::info($response);
    }
}
