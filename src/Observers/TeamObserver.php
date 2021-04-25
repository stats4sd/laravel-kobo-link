<?php


namespace Stats4sd\KoboLink\Observers;

use \App\Models\Team;
use Stats4sd\KoboLink\Models\Xlsform;

class TeamObserver
{
    /**
         * Handle the xlsform "created" event.
         *
         * @param  Team $team
         * @return void
         */
    public function created(Team $team)
    {
        $this->syncTeamWithForms($team);
    }

    /**
     * Handle the xlsform "updated" event.
     *
     * @param  Team $team
     * @return void
     */
    public function updated(Team $team)
    {
        $this->syncTeamWithForms($team);
    }


    public function syncTeamWithForms(Team $team)
    {
        $forms = Xlsform::where('available')->get();
        $privateForms = Xlsform::where('private_team_id', $team->id)->get();

        $formIds =  $forms->merge($privateForms)->pluck('id')->toArray();

        if (count($formIds) > 0) {
            $team->xlsforms->sync($formIds);
        }
    }
}
