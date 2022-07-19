<?php

namespace Stats4sd\KoboLink\Jobs;

;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Events\KoboGetDataReturnedError;
use Stats4sd\KoboLink\Events\KoboGetDataReturnedSuccess;
use Stats4sd\KoboLink\Models\Submission;
use Stats4sd\KoboLink\Models\TeamXlsform;

class GetDataFromKobo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public TeamXlsform $form, public $user = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \JsonException
     */
    public function handle(): void
    {
        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->get(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/data/');


        if ($response->failed()) {
            if ($response->status() === 504) {
                $this->release(5);
            }
            event(new KoboGetDataReturnedError($this->form, json_encode($response->json()), $this->user));
            $this->fail();
        }

        $data = $response['results'] ?? null;
        $count = 0;

        $existingSubmissionIdArray = $submissions->pluck('id')->toArray()

        if ($data) {
            //compare
            $submissions = Submission::where('team_xlsform_id', '=', $this->form->id)->get();

            // put all submissions ID into an array for existence check
            $existingSubmissionIdArray = $submissions->pluck('id')->toArray();

            foreach ($data as $newSubmission) {
                if (! in_array($newSubmission['_id'], $existingSubmissionIdArray, true)) {
                    $submission = new Submission;

                    $submission->id = $newSubmission['_id'];
                    $submission->uuid = $newSubmission['_uuid'];
                    $submission->team_xlsform_id = $this->form->id;
                    $submission->content = json_encode($newSubmission, JSON_THROW_ON_ERROR);
                    $submission->submitted_at = $newSubmission['_submission_time'];

                    $submission->save();
                    $count++;

                    $submission = Submission::find($newSubmission['_id']);

                    $dataMaps = $this->form->xlsform->datamaps;
                    if ($dataMaps->count() > 0) {
                        foreach ($dataMaps as $dataMap) {
                            $dataMap->process($submission);
                        }

                        $submission->processed = 1;
                        $submission->save();
                    }
                }
            }
        }

        event(new KoboGetDataReturnedSuccess(
            $this->form,
            $count,
            $this->user
        ));
    }
}
