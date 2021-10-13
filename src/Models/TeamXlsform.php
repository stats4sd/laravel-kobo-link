<?php


namespace Stats4sd\KoboLink\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamXlsform extends \Illuminate\Database\Eloquent\Model
{
    use CrudTrait;

    protected $guarded = [];
    protected $table = 'team_xlsform';

    protected $with = ['xlsform', 'team'];

    public $appends = [
        'title',
        'records',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getTitleAttribute()
    {
        return $this->team ? $this->team->name.' - '.$this->xlsform->title : '';
    }

    public function getRecordsAttribute()
    {
        return $this->submissions->count();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function xlsform(): BelongsTo
    {
        return $this->belongsTo(Xlsform::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
