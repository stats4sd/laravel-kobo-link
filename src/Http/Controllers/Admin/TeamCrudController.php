<?php

namespace Stats4sd\KoboLink\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Exception;
use Illuminate\Support\Facades\Auth;
use Stats4sd\KoboLink\Http\Requests\TeamRequest;
use App\Models\Team;


/**
 * Class TeamCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TeamCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     * @throws Exception
     */
    public function setup(): void
    {
        CRUD::setModel(Team::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/team');
        CRUD::setEntityNameStrings('team', 'teams');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation(): void
    {

        CRUD::setResponsiveTable(false);

        CRUD::column('avatar')
            ->type('image')
            ->disk(config('filesystems.default'));
        CRUD::column('name');
        CRUD::column('status')
            ->type('boolean')
            ->label('active?');
        CRUD::column('users_count')
            ->label('No. of users')
            ->suffix(' users');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(TeamRequest::class);

        CRUD::field('name')->label('Enter the team name');
        CRUD::field('description')->type('textarea')->label('Enter a brief description of the team');
        CRUD::field('avatar')->type('image')->crop(true)->upload(true)->disk('team')->label('Upload a cover image for the main team page');

        CRUD::field('creator_id')->type('hidden')->value(Auth::user()->id);
        CRUD::field('status')->type('hidden')->value(1);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation(): void
    {

        CRUD::setValidation(TeamRequest::class);

        CRUD::field('name')->label('Enter the team name');
        CRUD::field('description')->type('textarea')->label('Enter a brief description of the team');
        CRUD::field('avatar')->type('image')->crop(true)->upload(true)->disk('team')->label('Upload a cover image for the main team page');

        CRUD::field('status')->type('radio')->label('
                Is this case study active currently?')->options([
            1 => 'Active',
            0 => 'Inactive',
        ]);
    }

//    public function show(Team $id): Factory|View|Application
//    {
//        // TODO: create teams.show view or view component... (component better to let devs drop it into where-ever on each platform)
//        return view('teams.show', ['team' => $id]);
//    }

    public function getForms($team)
    {
        return $team->team_xlsforms->toJson();
    }
}
