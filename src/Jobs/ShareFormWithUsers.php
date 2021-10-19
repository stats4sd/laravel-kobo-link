<?php

namespace Stats4sd\KoboLink\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Stats4sd\KoboLink\Models\TeamXlsform;

class ShareFormWithUsers implements ShouldQueue
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
    public function __construct(public TeamXlsform $form)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws RequestException
     */
    public function handle(): void
    {
        $members = $this->form->team->users;

        $payload = [];

        $permissions = ['change_asset', 'add_submissions', 'change_submissions', 'validate_submissions'];

        foreach ($members as $member) {
            if ($member->kobo_username && $member->kobo_username !== "") {
                foreach ($permissions as $permission) {
                    $payload[] = [
                        'permission' => config('kobo-link.kobo.endpoint_v2').'/permissions/'.$permission.'/',
                        'user' => config('kobo-link.kobo.endpoint_v2').'/users/'.$member->kobo_username.'/',
                    ];
                }
            }
        }

        Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
        ->withHeaders(['Accept' => 'application/json'])
        ->post(config('kobo-link.kobo.endpoint_v2').'/assets/'.$this->form->kobo_id.'/permission-assignments/bulk/', $payload)
        ->throw()
        ->json();
    }
}
