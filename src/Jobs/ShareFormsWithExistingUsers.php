<?php

namespace App\Jobs;

use App\Models\Team;
use App\Models\User;
use App\Models\Xlsform;
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $forms = Xlsform::all();
        $users = User::all();

        \Log::info("sharing forms with new Users");

        $permissions = ['change_asset', 'add_submissions', 'change_submissions', 'validate_submissions'];

        foreach ($forms as $form) {
            \Log::info("sharing form " . $form->title);

            if ($form->is_active && $form->kobo_version_id) {
                $payload = [];

                foreach ($users as $user) {
                    if ($user->kobo_username) {
                        foreach ($permissions as $permission) {
                            $payload[] = [
                                'permission' => config('services.kobo.endpoint_v2') . '/permissions/' . $permission . '/',
                                'user' => config('services.kobo.endpoint_v2') . '/users/' . $user->kobo_username . '/',
                            ];
                        }
                    }
                }

                $response = Http::withBasicAuth(config('services.kobo.username'), config('services.kobo.password'))
                ->withHeaders(['Accept' => 'application/json'])
                ->post(config('services.kobo.endpoint_v2') . '/assets/' . $form->kobo_id . '/permission-assignments/bulk/', $payload)
                ->throw()
                ->json();

                \Log::info("new team member assigned to form");
                \Log::info(json_encode($response));
            }
        }
    }
}
