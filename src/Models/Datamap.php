<?php


namespace Stats4sd\KoboLink\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Str;

class Datamap extends \Illuminate\Database\Eloquent\Model
{
    use CrudTrait;
    use ValidatesRequests;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'datamaps';
    protected $guarded = [];
    public $incrementing = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function xlsforms(): BelongsToMany
    {
        return $this->belongsToMany(Xlsform::class);
    }

    public function process(Submission $submission)
    {
        $service = app(config('kobo-link.process_scripts_class'));

        // TODO: figure out why on earth we're using the id to determine the method name!! (or rather - how to do it more logically...)
        $service->{$this->id}($submission);
    }

    public function removeGroupNames(array $record): array
    {
        // go through record variables and remove any group names
        foreach ($record as $key => $value) {

            // Keep this as it forms part of the media download url
            if ($key === 'formhub/uuid') {
                continue;
            }

            if (Str::contains($key, '/')) {
                // e.g. replace $record['groupname/subgroup/name'] with $record['name']
                unset($record[$key]);
                $key = explode('/', $key);
                $key = end($key);
                $record[$key] = $value;
            }
        }

        return $record;
    }
}
