<?php

namespace Stats4sd\KoboLink\Http\Controllers\Admin;

//use App\Http\Controllers\Admin\Fgds\FgdCrudController;
//use App\Models\Team;
use Illuminate\Support\Str;
//use App\Exports\TeamsExport;
//use App\Exports\TeamSitesExport;
//use App\Http\Requests\TeamRequest;
use Stats4sd\KoboLink\Http\Requests\TeamRequest;
//use Carbon\Carbon;
use Prologue\Alerts\Facades\Alert;
use Stats4sd\KoboLink\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
//use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InterviewWorkbookExport;
use Illuminate\Support\Facades\Redirect;
use Stats4sd\KoboLink\Jobs\GetDataFromKobo;
//use \App\Http\Controllers\Operations\ExportOperation;
use App\Exports\Survey\FarmerSurveyWorkbookExport;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TeamCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation {index as traitIndex;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {destroy as traitDestroy;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    //use ExportOperation;
    use AuthorizesRequests;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Team::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/team');
        CRUD::setEntityNameStrings('team', 'teams');
        //CRUD::set('export.exporter', TeamsExport::class);

        CRUD::denyAccess('delete');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::setResponsiveTable(false);

        //$this->authorize('viewAny', Team::class);

        if (request()->input('iframe') == 'y') {
            CRUD::setListView('dashboard.methods-group');
            CRUD::denyAccess(['create', 'delete', 'edit', 'show']);
        }

        //$this->crud->query->withCount(['users', 'sites', 'farmSurveys']);

        /*
        CRUD::column('avatar')
            ->type('image')
            ->disk('team');
        */

        CRUD::column('name');

        CRUD::column('description');

        CRUD::column('slug');
        
        CRUD::column('status')        
            ->type('boolean')
            ->label('active?');

        /*
        CRUD::column('users_count')
            ->label('No. of users')
            ->suffix(' users');
        */

        /*
        CRUD::column('sites_count')
            ->label('No. of sites')
            ->suffix(' sites');
        CRUD::column('ki_interviews_count')
            ->label('No. of KI interviews')
            ->suffix(' interviews');
        CRUD::column('farm_surveys_count')
            ->label('No. of Farm / Household Surveys')
            ->suffix(' surveys');
        */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        //$this->authorize('create', Team::class);

        CRUD::setValidation(TeamRequest::class);

        CRUD::field('name')->label('Enter the case study name');
        CRUD::field('description')->type('textarea')->label('Enter a brief description of the case study');
        //CRUD::field('avatar')->type('image')->crop(true)->upload(true)->disk('team')->label('Upload a cover image for the main team page');


        // TODO: get user id of logined user
        // hardcode it to 1 for testing temporary
        //CRUD::field('creator_id')->type('hidden')->value(Auth::user()->id);
        CRUD::field('creator_id')->type('hidden')->value("1");

        CRUD::field('status')->type('hidden')->value(1);

        CRUD::field('slug')->label('Enter the slug');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        // comment this line to avoid error 403
        //$this->authorize('update', CRUD::getCurrentEntry());

        CRUD::setValidation(TeamRequest::class);

        CRUD::field('name')->label('Enter the case study name');
        CRUD::field('description')->type('textarea')->label('Enter a brief description of the case study');

        /*
        CRUD::field('avatar')->type('image')->crop(true)->upload(true)->disk('team')->label('Upload a cover image for the main team page');
        CRUD::field('countries')->type('relationship')->label('Select the countries where this case study operates  ');
        CRUD::field('language_note')->type('section-title')->title('languages')->content('Every team has access to French and English for the Step 4 survey. To enable extra languages, add them here.<br/><br/><b>NOTE:</b> If you need an additional language for the ODK form, please contact the methods group or platform support (support@stats4sd.org) to discuss how to get the survey translated.');

        CRUD::field('languages')->type('relationship')->label('Select language(s)');
        CRUD::field('status')->type('radio')->label('Is this case study active currently?<br/><span class="font-weight-normal">This should be left as active for the duration of the TPP Viability Case Study project.</span>')->options([
            1 => 'Active',
            0 => 'Inactive',
        ]);
        */        
    }

    public function show()
    {
        //$team = CRUD::getCurrentEntry()->load('languages');
        $team = CRUD::getCurrentEntry();

        // hardcode to get Team 1 for testing
        //$team = Team::where('id', '1')->get()->first();

        // to avoid error 403 for testing
        $this->authorize('view', $team);

        return view('teams.show', ['team' => $team]);
    }

    /*
    public function exportSites(Team $team)
    {
        $this->authorize('view', $team);

        return Excel::download(new TeamSitesExport($team->sites), $team->name . '-sites-'.now()->toDateTimeString().'.xlsx');
    }

    public function exportInterviews(Team $team)
    {
        $this->authorize('view', $team);

        return Excel::download(new InterviewWorkbookExport($team), $team->name . '-step2-interviews-'.now()->toDateTimeString().'.xlsx');
    }

    public function exportSurveys(Team $team, $booleans = 0)
    {
        $this->authorize('view', $team);

        foreach ($team->team_xlsforms as $form) {
            GetDataFromKobo::dispatchSync($form, backpack_auth()->user());
        }

        return Excel::download(new FarmerSurveyWorkbookExport($team, false, $booleans), $team->name . '-step4-surveys-'.now()->toDateTimeString().'.xlsx');
    }


    public function exportPreviewSurveys(Team $team, $booleans = 0)
    {
        $this->authorize('view', $team);

        foreach ($team->team_xlsforms as $form) {
            GetDataFromKobo::dispatchSync($form, backpack_auth()->user());
        }

        return Excel::download(new FarmerSurveyWorkbookExport($team, true, $booleans), $team->name . '-step4-PREVIEW-surveys-'.now()->toDateTimeString().'.xlsx');
    }
    */

    public function showDashboard()
    {
        if (request()->input('iframe') == 'y') {
            $response = Gate::inspect('viewAny', Team::class);
            if (! $response->allowed()) {
                Alert::add('error', $response->message())->flash();
                return Redirect::back();
            }

            CRUD::setDefaultPageLength(20);
            CRUD::setListView('dashboard.methods-group');
            CRUD::denyAccess(['create', 'delete', 'edit', 'show']);
        }


        $this->crud->setOperation('list');
        $this->setupListOperation();
        $this->setupListDefaults();

        $this->crud->setPageLengthMenu([20, 50, 100, -1]);


        return $this->traitIndex();

        // $this->crud->hasAccessOrFail('list');

        // $this->data['crud'] = $this->crud;
        // $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);

        // // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        // return view('dashboard.methods-group', $this->data);
    }

    public function getForms(Team $team)
    {
        return $team->team_xlsforms->toJson();
    }

    public function destroy($id)
    {
        if ($this->authorize('delete', CRUD::getCurrentEntry())) {
            CRUD::allowAccess('delete');
            return $this->traitDestroy($id);
        }
    }

    /*
    public function exportAllFgds(Team $team)
    {
        // zip up all documents
        $zipName = Storage::disk('local')->path('fgd/exports/'.$team->id.'/fgds-team-'.$team->id.'-'.Carbon::now()->format('Ymd_His').'.zip');

        $zip = new \ZipArchive();
        $zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($team->fgds as $fgd){
            $path = (new FgdCrudController())->generateIndividualDoc($fgd);
            $filePathArray = explode('/', $path);
            $filename = $filePathArray[count($filePathArray)-1];
            $zip->addFile(Storage::disk('local')->path($path),'team-'.$team->id.'/'.Str::of($team->name)->slug() .'/'.$filename);
        }

        $zip->close();

        return response()->download($zipName);

    }
    */
}
