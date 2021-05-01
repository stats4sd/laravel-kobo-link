<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Stats4sd\KoboLink\Exports\SqlViewExport;
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
                $filePath = $media['csv_name'];
                $team = null;

                if ($media['per_team'] === "1") {
                    $team = $this->form->team;
                    $filePath = $team->id . '/' . $media['csv_name'];
                }

                Excel::store(
                    new SqlViewExport($media['mysql_name'], $team),
                    $filePath . '.csv',
                    config('kobo-link.TeamXlsforms.storage_disk'),
                );
            }
        }
    }
}
