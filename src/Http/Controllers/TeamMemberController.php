<?php

namespace Stats4sd\KoboLink\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Stats4sd\KoboLink\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\TeamMemberStoreRequest;
use App\Http\Requests\TeamMemberUpdateRequest;

class TeamMemberController extends Controller
{
    public function create(Team $team)
    {
        $users = User::whereDoesntHave('teams', function (Builder $query) use ($team) {
            $query->where('teams.id', '=', $team->id);
        })->get();

        return view('teams.create-members', ['team' => $team, 'users' => $users]);
    }


    /**
     * Attach users to the team, or send email invites to non-users.
     * New Members are automatically not admins.
     *
     * @param TeamMemberStoreRequest $request
     * @param Team $team
     * @return void
     */

    public function store(TeamMemberStoreRequest $request, Team $team)
    {
        //$this->authorize('update', $team);

        $data = $request->validated();

        if (isset($data['users'])) {
            $team->users()->syncWithoutDetaching($data['users']);
        }

        if (isset($data['emails']) && count(array_filter($data['emails'])) > 0) {
            $team->sendInvites($data['emails']);            
        }

        return redirect()->route('team.show', ['id' => $team->id]);
    }

    public function edit(Team $team, User $user)
    {

        //use the relationship to get the pivot attributes for user
        $user = $team->users->find($user->id);

        return view('teams.edit-members', ['team' => $team, 'user' => $user]);
    }


    /**
     * Update the access level for existing team member
     *
     * @param TeamMemberUpdateRequest $request
     * @param Team $team
     * @param User $user
     * @return void
     */

    public function update(TeamMemberUpdateRequest $request, Team $team, User $user)
    {
        $data = $request->validated();

        $team->users()->syncWithoutDetaching([$user->id => ['is_admin' => $data['is_admin']]]);

        return redirect()->route('team.show', ['id' => $team->id]);
    }

    /**
     * Remove a user from the team.
     *
     * @param Team $team
     * @param User $user
     * @return void
     */
    public function destroy(Team $team, User $user)
    {
        $this->authorize('update', $team);

        $admins = $team->admins()->get();
        // if the $user is a $team admin AND is the ONLY team admin... prevent
        if ($admins->contains($user) && $admins->count() == 1) {
            \Alert::add('error', 'User not removed - you must keep at least one team admin to manage your team')->flash();
        } else {
            $team->users()->detach($user->id);
            //ShareFormsWithExistingTeamMembers::dispatch($team);
            \Alert::add('success', 'User ' . $user->name . ' successfully removed from the team')->flash();
        }

        return redirect()->route('team.show', [$team, 'members']);
    }
}
