<?php

namespace Stats4sd\KoboLink\Observers;

use App\Models\User;
use App\Models\RoleInvite;
use Illuminate\Support\Str;
use Stats4sd\KoboLink\Models\Invite;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // if the user was invited to a team, add them to that team and confirm the invite
        $invites = Invite::where('email', '=', $user->email)->get();
        foreach ($invites as $invite) {
            $user->teams()->syncWithoutDetaching($invite->team->id);

            $invite->confirm();
        }
    }
}
