<?php


namespace Stats4sd\KoboLink\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use Stats4sd\KoboLink\Http\Requests\XlsformRequest;
use Stats4sd\KoboLink\Models\Xlsform;

/**
 * Class XlsformCrudController
 * @package \Stats4SD\KoboLink\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class XlsformCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(Xlsform::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/xlsform');
        CRUD::setEntityNameStrings('xlsform', 'xlsforms');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('title');
        CRUD::column('xlsfile')->type('upload')->wrapper([
            'href' => function ($crud, $column, $entry) {
                if ($entry->xlsfile) {
                    return Storage::disk(config('kobo-link.xlsforms.storage_disk'))->url($entry->xlsfile);
                }

                return '#';
            },
        ]);
        CRUD::column('media')->type('upload_multiple')->disk(config('kobo-link.xlsforms.storage_disk'));
        CRUD::column('csv_lookups')->type('table')->columns([
            'mysql_name' => 'MySQL Table/View',
            'csv_name' => 'CSV File Name',
        ]);
        CRUD::column('available')->type('boolean')->label('Is the form available for live use?');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(XlsformRequest::class);

        CRUD::field('title');
        CRUD::field('xlsfile')->type('upload')->upload(true);
        CRUD::field('description')->type('textarea');
        CRUD::field('media')->type('upload_multiple')->label('Add any static files that should be pushed to KoboToolBox as media attachments for this form')->upload(true);
        CRUD::field('csv_lookups')->type('repeatable')->fields([
            [
                'name' => 'mysql_name',
                'label' => 'MySQL Table Name',
            ],
            [
                'name' => 'csv_name',
                'label' => 'CSV File Name',
            ],
            [
                'name' => 'per_team',
                'type' => 'checkbox',
                'label' => 'Should this csv file be filtered by "team_id"?',
            ],
        ])->label('<h4>Add Lookups from the Database</h4>
        <br/><div class="bd-callout bd-callout-info font-weight-normal">
        You should add the name of the MySQL Table or View, and the required name of the resulting CSV file. Every time you deploy this form, the platform will create a new version of the csv file using the data from the MySQL table or view you specify. This file will be uploaded to KoboToolBox as a form media attachment.
        <br/><br/>
        For example, if the form requires a csv lookup file called "households.csv", and the data is available in a view called "households_csv", then you should an entry like this:
            <ul>
                <li>MySQL Table Name = housholds_csv</li>
                <li>CSV File Name = households</li>
            </ul>
        CSV files can optionally be filtered to only show team-specific records. Use this for data that each team can customise themselves, or for data that should be filtered to a team\'s local context. For this to work, the MySQL table or view <b>must</b> have a "team_id" field to filter by.
        </div>')->entity_singular('CSV Lookup reference');
        CRUD::field('available')->label('If this form should be available to all teams, tick this box')->type('checkbox');
        CRUD::field('privateTeam')->label('If this form should be available to a <b>single</b> team, select the team here.')->type('relationship')->attribute('name');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->setupListOperation();
    }
}
