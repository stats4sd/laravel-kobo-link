<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Uploads an individual file to Kobotoolbox
 * @param String $media - the path to the media file to upload
 * @param Array $koboform - the KoboForm object returned from querying the OLD Kobotoolbox API (kc.kobotoolbox/api/v1)
 */
class UploadFileToKoboForm implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $media;
    public $koboform;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(String $media, array $koboform)
    {
        $this->media = $media;
        $this->koboform = $koboform;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filename = Arr::last(explode('/', $this->media));

        $upload = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(['Accept' => 'application/json'])
            ->attach(
                'data_file',
                Storage::disk(config('kobo-link.TeamXlsforms.storage_disk'))->get($this->media),
                $filename
            )
            ->post(config('kobo-link.kobo.old_endpoint') . '/api/v1/metadata', [
                'xform' => $this->koboform['formid'],
                'data_type' => 'media',
                'data_value' => $filename,
            ])
            ->throw()
            ->json();

    }
}
