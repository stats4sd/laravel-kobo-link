<?php

namespace Stats4sd\KoboLink\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Models\XlsForm;

class ShareFormWithUsers implements ShouldQueue
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
    public function __construct(Xlsform $form)
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
        $members = User::all();

        $payload = [];

        $permissions = ['change_asset', 'add_submissions', 'change_submissions', 'validate_submissions'];

        foreach ($members as $member) {
            if ($member->kobo_id) {
                foreach ($permissions as $permission) {
                    $payload[] = [
                        'permission' => config('kobo-link.kobo.endpoint_v2').'/permissions/'.$permission.'/',
                        'user' => config('kobo-link.kobo.endpoint_v2').'/users/'.$member->kobo_id.'/',
                    ];
                }
            }
        }

        $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->post(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/permission-assignments/bulk/', $payload)
        ->throw()
        ->json();
    }
}
