<?php

namespace Stats4sd\KoboLink\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Stats4sd\KoboLink\Models\Invite;

/**
 * Class InviteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InviteCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Invite::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/invite');
        CRUD::setEntityNameStrings('invite', 'invites');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('email')->label('Sent to');
        CRUD::column('team')->type('relationship')->label('Team invited to');
        CRUD::column('inviter')->type('relationship')->label('Invited by');
        CRUD::column('created_at_day')->type('date')->label('Invite sent on');
        CRUD::column('is_confirmed')->type('boolean')->label('Invite Accepted?');
    }

    // An invite should never be manually created, so there is no setupCreateOperation or setupUpdateOperation.
}
