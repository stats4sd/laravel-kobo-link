<?php


namespace Stats4sd\KoboLink\Models;

use Stats4sd\KoboLink\Models\Xlsform;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        $this->{$this->id}($submission);
    }

    public function removeGroupNames(array $record): array
    {
        // go through record variables and remove any group names
        foreach ($record as $key => $value) {

            // Keep this as it forms part of the media download url
            if ($key == 'formhub/uuid') {
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
