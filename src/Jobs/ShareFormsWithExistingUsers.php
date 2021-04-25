<?php

namespace Stats4sd\KoboLink\Jobs;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

/**
 * This job sends a post request to the permission-assignments/bulk endpoint.
 * It updates the permissions of all team forms so that ALL current members have access, and no-one else.
 * This should be run every time a new member is added to a team, and also when a member is removed from the team, to ensure permissions are up to date.
 */
class ShareFormsWithExistingUsers implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public Team $team;

    /**
     * Create a new job instance.
     * @param Team $team
     * @return void
     */
    public function __construct(Team $team)
    {
        $this->team = $form;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $forms = $this->team->teamXlsForms;
        $users = $this->team->users;

        $permissions = ['change_asset', 'add_submissions', 'change_submissions', 'validate_submissions'];

        foreach ($forms as $form) {
            if ($form->is_active && $form->kobo_version_id) {
                $payload = [];

                foreach ($users as $user) {
                    if ($user->kobo_username) {
                        foreach ($permissions as $permission) {
                            $payload[] = [
                                'permission' => config('kobo-link.kobo.endpoint_v2') . '/permissions/' . $permission . '/',
                                'user' => config('kobo-link.kobo.endpoint_v2') . '/users/' . $user->kobo_username . '/',
                            ];
                        }
                    }
                }

                $response = Http::withBasicAuth(config('kobo-link.kobo.username'), config('kobo-link.kobo.password'))
                ->withHeaders(['Accept' => 'application/json'])
                ->post(config('kobo-link.kobo.endpoint_v2') . '/assets/' . $form->kobo_id . '/permission-assignments/bulk/', $payload)
                ->throw()
                ->json();
            }
        }
    }
}
