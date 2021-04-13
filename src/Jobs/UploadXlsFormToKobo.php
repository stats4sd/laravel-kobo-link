<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Xlsform;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Events\KoboUploadReturnedError;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadXlsFormToKobo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $form;

    /**
     * Create a new job instance.
     *
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
        $response = Http::withBasicAuth(config('services.kobo.username'), config('services.kobo.password'))
            ->withHeaders(["Accept" => "application/json"])
            ->attach(
                'file',
                Storage::disk('xlsforms')->get($this->form->xlsfile),
                Str::slug($this->form->title)
            )
            ->post(config('services.kobo.endpoint').'/imports/', [
                'destination' => config('services.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/',
                'assetUid' => $this->form->kobo_id,
                'name' => $this->form->title,
            ])
            ->throw()
            ->json();

        \Log::info("importing");
        \Log::info($response);

        $importUid = $response['uid'];

        CheckKoboUpload::dispatch($this->user, $this->form, $importUid);
    }
}
