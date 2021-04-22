<?php

namespace Stats4sd\KoboLink\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stats4sd\KoboLink\Models\XlsForm;

class UploadXlsFormToKobo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(["Accept" => "application/json"])
            ->attach(
                'file',
                Storage::disk(config('kobo-link.xlsforms.storage_driver'))->get($this->form->xlsfile),
                Str::slug($this->form->title)
            )
            ->post(config('kobo-link.kobo.endpoint').'/imports/', [
                'destination' => config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/',
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
