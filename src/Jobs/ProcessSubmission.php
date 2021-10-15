<?php

namespace Stats4sd\KoboLink\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Models\Submission;

class ProcessSubmission implements ShouldQueue
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
    public function __construct(private Submission $submission)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->submission->processed = 0;
        $this->submission->errors = null;

        // check any existing database entries from this submission have been deleted
        $entries = $this->submission->entries;

        if ($entries) {
            foreach ($entries as $model => $ids) {
                $model::destroy($ids);
            }
        }

        $this->submission->entries = null;
        $this->submission->save();

        // find correct data map(s);
        $datamaps = $this->submission->teamXlsform->xlsform->datamaps;


        foreach ($datamaps as $datamap) {
            $datamap->process($this->submission);
        }

        $this->submission->processed = 1;
        $this->submission->save();
    }
}
