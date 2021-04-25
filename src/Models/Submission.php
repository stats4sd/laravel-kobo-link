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

    public function teamXlsform(): BelongsTo
    {
        return $this->belongsTo(TeamXlsform::class);
    }
}
