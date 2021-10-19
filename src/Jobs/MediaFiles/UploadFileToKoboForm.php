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
use Illuminate\Support\Str;
use Stats4sd\KoboLink\Models\TeamXlsform;

/**
 * Uploads an individual file to Kobotoolbox
 * @param String $media - the path to the media file to upload
 * @param Array $koboform - the KoboForm object returned from querying the OLD Kobotoolbox API (https://kc.kobotoolbox/api/v1)
 */
class UploadFileToKoboForm implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $media;
    public $form;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(String $media, TeamXlsform $form)
    {
        $this->media = $media;
        $this->form = $form;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filename = Arr::last(explode('/', $this->media));

        $file = Storage::disk(config('kobo-link.xlsforms.storage_disk'))->get($this->media);
        $mime = Storage::disk(config('kobo-link.xlsforms.storage_disk'))->mimeType($this->media);


        // csv files often give back "text/plain" as mime-type for some reason...
        switch (Str::before($mime, '/')) {
            case 'text':
                $type = 'text/csv';

                break;
            default:
                $type = $mime;

                break;
        }

        $base64 = 'data:'.$type.';base64,'.base64_encode($file);

        $upload = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
            ->withHeaders(['Accept' => 'application/json'])
            ->post(config('kobo-link.kobo.endpoint_v2') . '/assets/' . $this->form->kobo_id .'/files/', [
                'user' => config('kobo-link.kobo.endpoint_v2') . '/users/' . config('kobo-link.kobo.username') .'/',
                'asset' => config('kobo-link.kobo.endpoint_v2') . '/assets/' . $this->form->kobo_id .'/',
                'description' => $filename . 'uploaded from ' . config('app.name') . ' (' . config('app.url') . ').',
                'file_type' => 'form_media',
                'base64Encoded' => $base64,
                'metadata' => [
                    'filename' => $filename,
                ],
            ])
            ->throw()
            ->json();
    }
}
