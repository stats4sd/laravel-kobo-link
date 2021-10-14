<?php


namespace Stats4sd\KoboLink\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use JsonException;
use Stats4sd\KoboLink\Jobs\ProcessSubmission;
use App\Models\Submission;
use App\Models\TeamXlsform;

/**
 * Class SubmissionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SubmissionCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     * @throws Exception
     */
    public function setup(): void
    {
        CRUD::setModel(Submission::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/submission');
        CRUD::setEntityNameStrings('submission', 'submissions');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation(): void
    {
        CRUD::column('teamXlsform')->label('XLS Form')->type('relationship')->attribute('title');
        CRUD::column('id')->label('Submission ID<br/> (from Kobo)');
        CRUD::column('submitted_at')->type('datetime')->format('YYYY-MM-DD HH:mm:ss');
        CRUD::column('processed')->label('Processed?')->type('boolean');
        CRUD::column('errors')->label('Validation errors')->type('submission_errors')->view_namespace('kobo-link::crud.columns');
        CRUD::column('entries')->label('Db Entries created')->type('submission_entries')->view_namespace('kobo-link::crud.columns');

        CRUD::filter('xlsform')
        ->type('select2')
        ->label('Filter by Team form')
        ->values(function () {
            return TeamXlsform::get()->pluck('title', 'id')->toArray();
        })
        ->whenActive(function ($value) {
            CRUD::addClause('where', 'team_xlsform_id', $value);
        });

        CRUD::filter('errors')
        ->type('simple')
        ->label('Show submissions with errors')
        ->whenActive(function () {
            CRUD::addClause('where', 'errors', '!=', null);
        });

        Crud::button('reprocess')
        ->stack('line')
        ->view('kobo-link::crud.buttons.submissions.reprocess');
    }

    public function setupShowOperation(): void
    {
        $this->setupListOperation();

        CRUD::column('content')->type('closure')->function(/**
         * @throws JsonException
         */ function ($entry) {
            $content = json_decode($entry->content, true, 512, JSON_THROW_ON_ERROR);


            $output = '
            <table class="table table-striped">
            <tr>
            <th>Variable Name</th>
            <th>Value</th>
            </tr>
            ';

            foreach ($content as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value, JSON_THROW_ON_ERROR);
                }

                $output .= '
                <tr>
                    <td>'.$key.'</td>
                    <td>'.$value.'</td>
                </tr>
                ';
            }

            $output .= '</table>';

            return $output;
        });
    }

    /**
     * @throws JsonException
     */
    public function setupUpdateOperation(): void
    {
        $content = json_decode(CRUD::getCurrentEntry()->content, true, 512, JSON_THROW_ON_ERROR);

        CRUD::setTitle('Edit Submission with ID: ' . $content['_id']);

        CRUD::field('_main_title')->type('custom_html')->value('<div class="alert alert-danger">NOTE: This page is only for administrators! It is intended as a helper while developing the data maps, or to make **very careful** edits. This page should not be accessible by most users, and should ideally never be used on live data.</div>');

        //manually specify variables that cannot be changed:
        $immutable = [
            'end',
            'start',
            'today',
            'meta/instanceID',
            'meta/deprecatedID',
            'formhub/uuid',
        ];

        foreach ($content as $key => $value) {

            // TODO: fix hack to quietly ignore arrays / repeat groups...
            if (is_array($value)) {
                $value = json_encode($value, JSON_THROW_ON_ERROR);
            }


            // Do not allow immutable variables to be edited;
            if (in_array($key, $immutable, true) || Str::startsWith($key, '_')) {
                continue;
            }

            CRUD::field('_title'.$key)->type('custom_html')->value("<h5>{$key}</h5>");
            CRUD::field('_label'.$key)->type('submission_variable')->value((string) $key)->fake(true);
            CRUD::field($key)->type('submission_value')->value((string) $value)->fake(true);
            CRUD::field('_end'.$key)->type('custom_html')->value('<hr/>');
        }
    }

    /** Totally override default update functionality
     * @throws JsonException
     */
    public function update(): Redirector|Application|RedirectResponse
    {
        $submission = CRUD::getCurrentEntry();

        $content = json_decode($submission->content, true, 512, JSON_THROW_ON_ERROR);

        $request = $this->crud->getStrippedSaveRequest();

        foreach ($request as $key => $value) {

            //handle value updates
            if (! Str::startsWith($key, '_label')) {
                $content[$key] = $value;
            }
        }

        foreach ($request as $key => $value) {
            //handle variable name updates
            if (Str::startsWith($key, '_label')) {
                $key = Str::replaceFirst('_label', '', $key);

                if ($key !== $value) {
                    $content[$value] = $content[$key];
                    unset($content[$key]);
                }
            }
        }

        $submission->content = json_encode($content, JSON_THROW_ON_ERROR);
        $submission->save();

        return redirect(CRUD::getRoute());
    }

    public function reprocessSubmission(Submission $submission): void
    {
        ProcessSubmission::dispatchSync($submission, auth()->user());
    }
}
