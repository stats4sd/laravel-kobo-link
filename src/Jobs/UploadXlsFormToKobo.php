<?php

namespace Stats4sd\KoboLink\Jobs;

;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stats4sd\KoboLink\Models\TeamXlsform;

class UploadXlsFormToKobo implements ShouldQueue
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
    public function __construct(public TeamXlsform $form, public mixed $user = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws RequestException|FileNotFoundException
     */
    public function handle(): void
    {
        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(["Accept" => "application/json"])
            ->attach(
                'file',
                Storage::disk(config('kobo-link.xlsforms.storage_driver'))->get($this->form->xlsform->xlsfile),
                Str::slug($this->form->title)
            )
            ->post(config('kobo-link.kobo.endpoint').'/imports/', [
                'destination' => config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/',
                'assetUid' => $this->form->kobo_id,
                'name' => $this->form->title,
            ])
            ->throw()
            ->json();

        $importUid = $response['uid'];

        // dispatch next step in the process
        CheckKoboUpload::dispatch($this->form, $importUid, $this->user);
    }
}
