<?php

namespace Stats4sd\KoboLink\Jobs;

use App\Models\User;
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

    public TeamXlsform $form;
    public User $user;
    public int $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, TeamXlsform $form)
    {
        $this->user = $user;
        $this->form = $form;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->get(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/data/');


        if ($response->failed()) {
            if ($response->status() === 504) {
                $this->release(5);
            }
            event(new KoboGetDataReturnedError($this->user, $this->form, json_encode($response->json())));
            $this->fail();
        }

        $data = $response['results'] ?? null;
        $count = 0;

        if ($data) {
            //compare
            $submissions = Submission::where('team_xlsform_id', '=', $this->form->id)->get();

            foreach ($data as $newSubmission) {
                if (! in_array($newSubmission['_id'], $submissions->pluck('id')->toArray())) {
                    $submission = new Submission;

                    $submission->id = $newSubmission['_id'];
                    $submission->uuid = $newSubmission['_uuid'];
                    $submission->team_xlsform_id = $this->form->id;
                    $submission->content = json_encode($newSubmission);
                    $submission->submitted_at = $newSubmission['_submission_time'];

                    $submission->save();
                    $count++;

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
            $this->user,
            $this->form,
            $count
        ));
    }
}
