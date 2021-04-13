<?php


namespace Stats4sd\KoboLink\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stats4sd\KoboLink\Models\Traits\HasUploadFields;

class Xlsform extends Model
{
    use CrudTrait, HasUploadFields;

    protected $table = 'xlsforms';
    protected $guarded = ['id'];
    protected $casts = [
        'media' => 'array',
        'csv_lookups' => 'array',
    ];

    public function setXlsfileAttribute($value)
    {
        $this->uploadFileWithNames($value, 'xlsfile', 'xlsforms', '');
    }

    public function setMediaAttribute($value)
    {
        $this->uploadMultipleFilesWithNames($value, 'media', 'xlsforms', '');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
