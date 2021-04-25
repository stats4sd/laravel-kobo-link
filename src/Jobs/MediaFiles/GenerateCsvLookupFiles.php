<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Stats4sd\KoboLink\Exports\SqlViewExporter;
use Stats4sd\KoboLink\Models\TeamXlsform;

/**
 * Job to generate the csv files from the specified mysql tables/views. Generates all the csv files required for the TeamXlsform passed to it, as defined in the TeamXlsform->csv_lookups property
 * @param TeamXlsform $TeamXlsform
 */
class GenerateCsvLookupFiles implements ShouldQueue
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
        $mediaToGenerate = $this->form->xlsform->csv_lookups;

        if ($mediaToGenerate && is_countable($mediaToGenerate)) {
            foreach ($mediaToGenerate as $media) {

                // if the media file should be filtered by the team that owns the TeamXlsform, use a customised script...
                //            if ($media['per_team']) {
                //                $scriptPath = base_path().'/scripts/save_table_for_team.py';
                //            } else {
                //            $scriptPath = base_path().'/scripts/save_table.py';
                //            }

                Excel::store(
                    new SqlViewExporter($media['mysql_name']),
                    $media['csv_name'] . '.csv',
                    config('kobo-link.TeamXlsforms.storage_disk'),
                );
            }
        }
    }
}
