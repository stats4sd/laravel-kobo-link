<?php


namespace Stats4sd\KoboLink\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Stats4sd\KoboLink\Models\Traits\HasUploadFields;

class Xlsform extends Model
{
    use CrudTrait;
    use HasUploadFields;

    protected $table = 'xlsforms';
    protected $guarded = ['id'];
    protected $casts = [
        'media' => 'array',
        'csv_lookups' => 'array',
    ];

    public function setXlsfileAttribute($value)
    {
        $this->uploadFileWithNames($value, 'xlsfile', config('kobo-link.xlsforms.storage_disk'), '');
    }

    public function setMediaAttribute($value)
    {
        $this->uploadMultipleFilesWithNames($value, 'media', config('kobo-link.xlsforms.storage_disk'), '');
    }

    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(Submission::class, TeamXlsform::class);
    }

    public function datamaps(): BelongsToMany
    {
        return $this->belongsToMany(Datamap::class);
    }

    public function privateTeam(): BelongsTo
    {
        return $this->belongsTo(config('kobo-link.models.team'), 'private_team_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(config('kobo-link.models.team'), 'team_xlsform');
    }

    public function teamXlsform(): HasMany
    {
        return $this->hasMany(TeamXlsform::class);
    }
}
