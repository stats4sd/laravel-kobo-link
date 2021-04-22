<?php

namespace App\Jobs\MediaFiles;

use App\Models\Xlsform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Call this class to update the csv lookup files on the passed xlsform.
 * This handles calling the right jobs to generate the new csv files from the database and push them up to Kobotoolbox
 * @param Xlsform $team_xls_form
 */
class UpdateFormCsvFiles implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $team_xls_form;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Xlsform $team_xls_form)
    {
        $this->team_xls_form = $team_xls_form;
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
                new UploadCsvMediaFileAttachmentsToKoboForm($this->team_xls_form),
            ]
        )->dispatch($this->team_xls_form);
    }
}
