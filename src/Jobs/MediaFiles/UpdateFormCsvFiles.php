<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Jobs\SetKoboFormToActive;
use Stats4sd\KoboLink\Models\TeamXlsform;

/**
 * Call this class to update the csv lookup files on the passed TeamXlsform.
 * This method handles calling the right jobs to generate the new csv files from the database and push them up to Kobotoolbox
 * @param TeamXlsform $form
 */
class UpdateFormCsvFiles implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $form;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TeamXlsform $form)
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
        GenerateCsvLookupFiles::withChain(
            [
                new UploadCsvMediaFileAttachmentsToKoboForm($this->form),
                new SetKoboFormToActive($this->form),
            ]
        )->dispatch($this->form);
    }
}
