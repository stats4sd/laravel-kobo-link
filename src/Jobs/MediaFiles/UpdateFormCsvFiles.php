<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Stats4sd\KoboLink\Models\XlsForm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Call this class to update the csv lookup files on the passed xlsform.
 * This handles calling the right jobs to generate the new csv files from the database and push them up to Kobotoolbox
 * @param Xlsform $xlsform
 */
class UpdateFormCsvFiles implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $xlsform;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Xlsform $xlsform)
    {
        $this->xlsform = $xlsform;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        GenerateCsvLookupFiles::withChain(
            [
                new UploadCsvMediaFileAttachmentsToKoboForm($this->xlsform),
            ]
        )->dispatch($this->xlsform);
    }
}
