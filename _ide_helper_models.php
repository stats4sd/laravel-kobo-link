<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Datamap
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Xlsform[] $xlsforms
 * @property-read int|null $xlsforms_count
 * @method static \Illuminate\Database\Eloquent\Builder|Datamap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Datamap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Datamap query()
 */
	class Datamap extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $kobo_username
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereKoboUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace Stats4sd\KoboLink\Models{
/**
 * Stats4sd\KoboLink\Models\Datamap
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Xlsform[] $xlsforms
 * @property-read int|null $xlsforms_count
 * @method static \Illuminate\Database\Eloquent\Builder|Datamap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Datamap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Datamap query()
 */
	class Datamap extends \Eloquent {}
}

namespace Stats4sd\KoboLink\Models{
/**
 * Stats4sd\KoboLink\Models\Invite
 *
 * @property-read \App\Models\User $inviter
 * @property-read \Stats4sd\KoboLink\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite query()
 */
	class Invite extends \Eloquent {}
}

namespace Stats4sd\KoboLink\Models{
/**
 * Stats4sd\KoboLink\Models\Submission
 *
 * @property int $id
 * @property int $team_xlsform_id
 * @property string $uuid
 * @property string $submitted_at
 * @property string|null $submitted_by
 * @property array $content
 * @property array|null $errors
 * @property int $processed
 * @property array|null $entries
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Stats4sd\KoboLink\Models\TeamXlsform $teamXlsform
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereEntries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereSubmittedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereTeamXlsformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUuid($value)
 */
	class Submission extends \Eloquent {}
}

namespace Stats4sd\KoboLink\Models{
/**
 * Stats4sd\KoboLink\Models\Team
 *
 * @property int $id
 * @property int $creator_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $status
 * @property string|null $avatar
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $admins
 * @property-read int|null $admins_count
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Invite[] $invites
 * @property-read int|null $invites_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\TeamXlsform[] $teamXlsforms
 * @property-read int|null $team_xlsforms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Xlsform[] $xlsforms
 * @property-read int|null $xlsforms_count
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 */
	class Team extends \Eloquent {}
}

namespace Stats4sd\KoboLink\Models{
/**
 * Stats4sd\KoboLink\Models\TeamXlsform
 *
 * @property int $id
 * @property int $team_id
 * @property int $xlsform_id
 * @property string|null $kobo_id The unique ID of the form on Kobotools. If null, the form has not yet been pushed to Kobo.
 * @property string|null $kobo_version_id current or most recently deployed version on Kobotools. If null, the form has not yet been deployed on Kobo.
 * @property string|null $is_active is the form active on Kobotools?
 * @property string|null $enketo_url url to the enketo version - pulled from Kobotools
 * @property int $processing Is the form currently being processed by a Kobo Job?
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $records
 * @property-read string $title
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Submission[] $submissions
 * @property-read int|null $submissions_count
 * @property-read \Stats4sd\KoboLink\Models\Team $team
 * @property-read \Stats4sd\KoboLink\Models\Xlsform $xlsform
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereEnketoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereKoboId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereKoboVersionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereProcessing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamXlsform whereXlsformId($value)
 */
	class TeamXlsform extends \Eloquent {}
}

namespace Stats4sd\KoboLink\Models{
/**
 * Stats4sd\KoboLink\Models\Xlsform
 *
 * @property int $id
 * @property string $title
 * @property string $xlsfile
 * @property string|null $description
 * @property array|null $media links to stored files that should be added as media attachments to the ODK form
 * @property array|null $csv_lookups information to enable mysql tables or views to be converted to csv files and added as additional media attachments to the ODK form
 * @property string|null $available Available to all users? If false, the form is only available to testers or admins.
 * @property int|null $private_team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Datamap[] $datamaps
 * @property-read int|null $datamaps_count
 * @property-read \Stats4sd\KoboLink\Models\Team|null $privateTeam
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Submission[] $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\TeamXlsform[] $teamXlsform
 * @property-read int|null $team_xlsform_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stats4sd\KoboLink\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform query()
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereCsvLookups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform wherePrivateTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Xlsform whereXlsfile($value)
 */
	class Xlsform extends \Eloquent {}
}

