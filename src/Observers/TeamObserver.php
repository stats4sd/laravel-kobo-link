<?php


namespace Stats4sd\KoboLink\Observers;

use App\Models\Xlsform;

class TeamObserver
{
    /**
         * Handle the xlsform "created" event.f
         *
         * @param  $team
         * @return void
         */
    public function created($team)
    {
        $this->syncTeamWithForms($team);
    }

    /**
     * Handle the xlsform "updated" event.
     *
     * @param  $team
     * @return void
     */
    public function updated($team)
    {
        $this->syncTeamWithForms($team);
    }

    public function syncTeamWithForms($team)
    {
        $forms = Xlsform::where('available')->get();
        $privateForms = Xlsform::where('private_team_id', $team->id)->get();

        $formIds = $forms->merge($privateForms)->pluck('id')->toArray();

        if (count($formIds) > 0) {
            $team->xlsforms->sync($formIds);
        }
    }
}
