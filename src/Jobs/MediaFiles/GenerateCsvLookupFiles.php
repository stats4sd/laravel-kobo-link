<?php

namespace App\Jobs\MediaFiles;

use App\Models\Xlsform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Job to generate the csv files from the specified mysql tables/views. Generates all the csv files required for the xlsform passed to it, as defined in the xlsform->csv_lookups property
 * @param Xlsform $team_xls_form
 */
class GenerateCsvLookupFiles implements ShouldQueue
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
        $mediaToGenerate = $this->team_xls_form->xls_form->csv_lookups;
        $team_id = $this->team_xls_form->team->id;

        foreach ($mediaToGenerate as $media) {

            // if the media file should be filtered by the team that owns the team_xls_form, use a customised script...
            if ($media['per_team']) {
                $scriptPath = base_path().'/scripts/save_table_for_team.py';
            } else {
                $scriptPath = base_path().'/scripts/save_table.py';
            }

            if (config('app.env') != 'local') {
                putenv("PATH=" . getenv('PATH').':/home/forge/.local/bin');
            }

            $process = new Process(['pipenv', 'run', 'python3', $scriptPath, $media['mysql_view'],  $media['csv_file'], $team_id]);
            $process = $process->setWorkingDirectory(base_path());

            $process->run();
            Log::info('generating file: '.$media['csv_file'].' from mysql view: '.$media['mysql_view']);
            Log::info($process->getOutput());

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }
    }
}
