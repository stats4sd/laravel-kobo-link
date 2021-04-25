<?php


namespace Stats4sd\KoboLink\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Carbon\Carbon;
use Stats4sd\KoboLink\Exports\FormSubmissionsExport;
use Stats4sd\KoboLink\Jobs\ArchiveKoboForm;
use Stats4sd\KoboLink\Jobs\DeployFormToKobo;
use Stats4sd\KoboLink\Jobs\GetDataFromKobo;
use Stats4sd\KoboLink\Models\TeamXlsform;

/**
 * Class XlsformCrudController
 * @package \Stats4SD\KoboLink\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamXlsformCrudController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(TeamXlsform::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/teamxlsform');
        CRUD::setEntityNameStrings('Team XLS Form', 'Team XLS Form');
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
        CRUD::column('xlsform')->type('relationship')->attribute('title');
        CRUD::column('team')->type('relationship')->attribute('name');

        CRUD::column('kobo_id')->label('Kobo Form ID')->wrapper([
            'href' => function ($crud, $column, $entry) {
                if ($entry->kobo_id) {
                    return 'https://kf.kobotoolbox.org/#/forms/'.$entry->kobo_id;
                }

                return '#';
            },
        ]);
        CRUD::column('is_active')->type('boolean')->label('Form active on Kobo?');
    }

    public function setupUpdateOperation()
    {
        CRUD::field('warning')->type('custom_html')->value('<div class="alert alert-info">This page is only for site administrators. Be careful modifying any of these properties for forms that are currently live, as it may interfere with ongoing data collection.</div>');
    }

    public function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);


        $this->setupListOperation();

        CRUD::setHeading(CRUD::getCurrentEntry()->title);


        Crud::button('deploy')
        ->stack('line')
        ->view('kobo-link::crud.buttons.xlsforms.deploy');

        Crud::button('sync')
        ->stack('line')
        ->view('kobo-link::crud.buttons.xlsforms.sync');

        Crud::button('archive')
        ->stack('line')
        ->view('kobo-link::crud.buttons.xlsforms.archive');

        $form = $this->crud->getCurrentEntry();

        Widget::add([
            'type' => 'view',
            'view' => 'kobo-link::crud.widgets.xlsforms.kobo-info',
            'form' => $form,
        ])->to('after_content');
    }

    public function deployToKobo(TeamXlsform $form)
    {
        DeployFormToKobo::dispatchSync(backpack_auth()->user(), $form);

        return response()->json([
            'title' => $form->title,
            'user' => backpack_auth()->user()->email,
        ]);
    }

    public function syncData(TeamXlsform $form)
    {
        GetDataFromKobo::dispatchSync(backpack_auth()->user(), $form);

        $submissions = $form->submissions;

        return $submissions->toJson();
    }

    public function downloadSubmissions(TeamXlsform $form)
    {
        $date = Carbon::now()->toDateTimeString();

        return (new FormSubmissionsExport)->forForm($form)->download($form->title . '-raw-submissions-' . $date . ".xlsx");
    }

    public function archiveOnKobo(TeamXlsform $form)
    {
        ArchiveKoboForm::dispatch(backpack_auth()->user(), $form);

        return response()->json([
            'title' => $form->title,
            'user' => backpack_auth()->user()->email,
        ]);
    }
}
