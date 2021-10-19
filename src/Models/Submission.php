<?php


namespace Stats4sd\KoboLink\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use CrudTrait;

    protected $table = 'submissions';
    protected $guarded = ['id'];
    protected $casts = [
        'content' => 'array',
        'errors' => 'array',
        'entries' => 'array',
    ];

    public function addEntry(String $model, array $ids)
    {
        $value = $this->entries;

        if ($value && array_key_exists($model, $value)) {
            $value[$model] = array_merge($value[$model], $ids);
        } else {
            $value[$model] = $ids;
        }

        $this->entries = $value;
        $this->save();
    }

    public function teamXlsform(): BelongsTo
    {
        return $this->belongsTo(TeamXlsform::class);
    }
}
