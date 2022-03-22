<?php

namespace Stats4sd\KoboLink\Http\Controllers;

use Stats4sd\KoboLink\Models\Invite;
use Stats4sd\KoboLink\Models\Team;

class RegisteredUserController extends Controller
{
    public function create(Team $team)
    {
        $invite = null;
        $inviteMessage = null;
        if (request()->has('token')) {
            $invite = Invite::where('token', '=', request()->token)->first();
        }

        if (! request()->has('token') || $invite == null) {
            abort(403, 'No valid invite is found');
        }

        $messageStub = $invite->team ? $invite->team->name : "Case Studies Platform";

        $inviteMessage = "You have been invited to join the " . $messageStub . ".";

        return view('kobo::auth.register', compact('invite', 'inviteMessage'));
    }
}
