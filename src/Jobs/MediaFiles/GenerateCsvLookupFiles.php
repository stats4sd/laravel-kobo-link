<?php

namespace Stats4sd\KoboLink\Jobs\MediaFiles;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Stats4sd\KoboLink\Exports\SqlViewExporter;
use Stats4sd\KoboLink\Models\XlsForm;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Job to generate the csv files from the specified mysql tables/views. Generates all the csv files required for the xlsform passed to it, as defined in the xlsform->csv_lookups property
 * @param Xlsform $xlsform
 */
class GenerateCsvLookupFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $mediaToGenerate = $this->xlsform->csv_lookups;

        foreach ($mediaToGenerate as $media) {

            // if the media file should be filtered by the team that owns the xlsform, use a customised script...
//            if ($media['per_team']) {
//                $scriptPath = base_path().'/scripts/save_table_for_team.py';
//            } else {
//            $scriptPath = base_path().'/scripts/save_table.py';
//            }

            Excel::store(
                new SqlViewExporter($media['mysql_name']),
                $media['csv_name'] . '.csv',
                config('kobo-link.xlsforms.storage_disk'),
            );
        }
    }
}
