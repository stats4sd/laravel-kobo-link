<?php

namespace Stats4sd\KoboLink\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Stats4sd\KoboLink\Models\Team;
use Stats4sd\KoboLink\Models\Invite;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\TeamMemberStoreRequest;
use App\Http\Requests\TeamMemberUpdateRequest;

class RegisteredUserController extends Controller
{
    public function create(Team $team)
    {
        $invite = null;
        $inviteMessage = null;
        if (request()->has('token')) {
            $invite = Invite::where('token', '=', request()->token)->first();
        }

        if (!request()->has('token') || $invite == null) {
            abort(403, 'No valid invite is found');
        }

        $messageStub = $invite->team ? $invite->team->name : "Case Studies Platform";

        $inviteMessage = "You have been invited to join the " . $messageStub . ".";

        return view('auth.register', compact('invite', 'inviteMessage'));
    }

}
