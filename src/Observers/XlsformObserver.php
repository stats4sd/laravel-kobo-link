<?php


namespace Stats4sd\KoboLink\Observers;

use \App\Models\Team;
use \Stats4sd\KoboLink\Models\XlsForm;

class XlsformObserver
{
    /**
     * Handle the xlsform "created" event.
     *
     * @param  Xlsform $xlsform
     * @return void
     */
    public function created(XlsForm $xlsform)
    {
        $this->syncFormWithTeams($xlsform);
    }

    /**
     * Handle the xlsform "updated" event.
     *
     * @param  Xlsform $xlsform
     * @return void
     */
    public function updated(XlsForm $xlsform)
    {
        $this->syncFormWithTeams($xlsform);
    }

    public function syncFormWithTeams(XlsForm $xlsform)
    {
        if ($xlsform->available) {
            $teams = Team::all()->pluck('id')->toArray();
            $xlsform->teams()->sync($teams);
        } else {
            $xlsform->teams()->sync($xlsform->private_team_id);
        }
    }
}
