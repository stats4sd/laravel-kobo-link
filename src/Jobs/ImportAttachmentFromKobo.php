<?php

namespace Stats4sd\KoboLink\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImportAttachmentFromKobo implements ShouldQueue
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
    public function __construct(public  String $name, public array $submission)
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
        // As of June 11 2020, downloadable filename is in the format:
        // kobo_username/attachments/submission['formhub/uuid']/submission['_uuid']/submission['photo_variable']

        $filename = config('kobo-link.kobo.username').'/attachments/'.$this->submission['formhub/uuid'].'/'.$this->submission['_uuid'].'/'.$this->name;

        $downloadUrl = config('kobo-link.kobo.old_endpoint').'/media/original?media_file='.$filename;


        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->get(urlencode($downloadUrl))
        ->throw();

        // store file in "attachments / _id / name"
        Storage::disk(config('kobo-link.media.storage_disk'))->put($this->submission['_id'].'/'.$this->name, $response);
    }
}
