<?php

namespace Stats4sd\KoboLink\Jobs;

use Psy\Util\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportAttachmentFromKobo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $name;

    public $submission;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(String $name, array $submission)
    {
        //
        $this->name = $name;
        $this->submission = $submission;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // As of June 11 2020, downloadable filename is in the format:
        // kobo_username/attachments/submission['formhub/uuid']/submission['_uuid']/submission['photo_variable']

        $filename = config('kobo-link.kobo.username').'/attachments/'.$this->submission['formhub/uuid'].'/'.$this->submission['_uuid'].'/'.$this->name;

        $downloadUrl = config('kobo-link.kobo.old_endpoint').'/media/original?media_file='.$filename;


        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->get(urlencode($downloadUrl))
        ->throw();

        // store file in "attachments / _id / name"
        Storage::disk('kobomedia')->put($this->submission['_id'].'/'.$this->name, $response);
    }
}
