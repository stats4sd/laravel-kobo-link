<?php /** @noinspection PsalmGlobal */

namespace Stats4sd\KoboLink\Models;

use App\Models\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Team extends Model
{
    use CrudTrait;

    protected $table = 'teams';
    protected $guarded = ['id'];

    /**
     * Generate an invitation to join this team for each of the provided email addresses
     * @param array $emails
     */
    public function sendInvites(array $emails): void
    {
        foreach ($emails as $email) {
            $this->invites()->create([
                'email' => $email,
                'inviter_id' => auth()->id(),
                'token' => Str::random(24),
            ]);
        }
    }

    public function xlsforms(): BelongsToMany
    {
        return $this->belongsToMany(Xlsform::class)
            ->withPivot([
                'kobo_id',
                'kobo_version_id',
                'enketo_url',
                'processing',
                'is_active',
            ]);
    }

    public function teamXlsforms(): HasMany
    {
        return $this->hasMany(TeamXlsform::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('is_admin');
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('is_admin')
            ->wherePivot('is_admin', 1);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('is_admin')
            ->wherePivot('is_admin', 0);
    }

    // May not need?
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }


}
