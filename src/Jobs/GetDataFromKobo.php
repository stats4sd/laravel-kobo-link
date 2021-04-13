<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Sample;
use App\Models\DataMap;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\Xlsform;
use App\Models\Submission;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Events\GetDataFromKoboFailed;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Events\KoboGetDataReturnedError;
use Illuminate\Queue\InteractsWithQueue;
use Stats4sd\KoboLink\Events\KoboGetDataReturnedSuccess;
use App\Helpers\GenericHelper;
use App\Http\Controllers\DataMapController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class GetDataFromKobo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $form;
    public $user;
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Xlsform $form)
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
        $response = Http::withBasicAuth(config('services.kobo.username'), config('services.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->get(config('services.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/data/');


        if ($response->failed()) {
            if ($response->status() === 504) {
                $this->release('5');
            }
            event(new KoboGetDataReturnedError($this->user, $this->form, json_encode($response->json())));
            $this->fail();
        }

        $data = $response['results'];

        //compare
        $submissions = Submission::where('team_xls_form_id', '=', $this->form->id)->get();

        foreach ($data as $newSubmission) {
            if (!in_array($newSubmission['_id'], $submissions->pluck('id')->toArray())) {
                $submission = new Submission;

                $submission->id = $newSubmission['_id'];
                $submission->uuid = $newSubmission['_uuid'];
                $submission->team_xls_form_id = $this->form->id;
                $submission->content = json_encode($newSubmission);
                $submission->submitted_at = $newSubmission['_submission_time'];

                $submission->save();

                // $dataMaps = $this->form->xls_form->data_maps;
                // if ($dataMaps->count() > 0) {
                //     $submissionId = $newSubmission['_id'];
                //     $teamId = $this->form->team->id;
                //     $data = $newSubmission;

                //     // $newSubmission = GenericHelper::remove_group_names_from_kobo_data($newSubmission);
                //     foreach ($dataMaps as $dataMap) {
                //         // DataMapController::newRecord($dataMap, $newSubmission, $teamId);
                //     }
                // }
            }
        }

        event(new KoboGetDataReturnedSuccess(
            $this->user,
            $this->form
        ));
    }
}
