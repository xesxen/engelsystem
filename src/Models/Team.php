<?php

namespace Engelsystem\Models;

use Carbon\Carbon;
use Engelsystem\Models\Auth\Role;
use Engelsystem\Models\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property integer                             $id
 * @property string                              $name
 * @property string|null                         $description
 * @property string|null                         $contact_name
 * @property string|null                         $contact_dect
 * @property string|null                         $contact_email
 * @property bool                                $restricted  Join Needs additional confirmation
 * @property bool                                $self_signup Can signup for shifts
 * @property bool                                $requires_drivers_license
 * @property bool                                $show_on_frontend
 * @property bool                                $show_on_dashboard
 * @property Carbon                              $created_at
 * @property Carbon                              $updated_at
 *
 * @property-read QueryBuilder|Collection|Role[] $roles
 * @property-read QueryBuilder|Collection|User[] $supporters
 * @property-read QueryBuilder|Collection|User[] $users
 *
 * @method static QueryBuilder|Team              whereId($value)
 * @method static QueryBuilder|Collection|Team[] whereName($value)
 * @method static QueryBuilder|Collection|Team[] whereDescription($value)
 * @method static QueryBuilder|Collection|Team[] whereContactName($value)
 * @method static QueryBuilder|Collection|Team[] whereContactDect($value)
 * @method static QueryBuilder|Collection|Team[] whereContactEmail($value)
 * @method static QueryBuilder|Collection|Team[] whereRestricted($value)
 * @method static QueryBuilder|Collection|Team[] whereSelfSignup($value)
 * @method static QueryBuilder|Collection|Team[] whereRequiresDriversLicense($value)
 * @method static QueryBuilder|Collection|Team[] whereShowOnFrontend($value)
 * @method static QueryBuilder|Collection|Team[] whereShowOnDashboard($value)
 * @method static QueryBuilder|Collection|Team[] whereCreatedAt($value)
 * @method static QueryBuilder|Collection|Team[] whereUpdatedAt($value)
 */
class Team extends BaseModel
{
    /** @var bool enable timestamps */
    public $timestamps = true;

    /** The attributes that are mass assignable */
    protected $fillable = [
        'name',
        'description',
        'contact_name',
        'contact_dect',
        'contact_email',
        'restricted',
        'self_signup',
        'requires_drivers_license',
        'show_on_frontend',
        'show_on_dashboard',
    ];

    /**
     * The roles that belong to the team
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this
            ->belongsToMany(Role::class)
            ->withTimestamps();
    }

    /**
     * The users that that support to the team
     *
     * @return BelongsToMany
     */
    public function supporters()
    {
        return $this
            ->belongsToMany(User::class, 'supporter_team')
            ->withTimestamps();
    }

    /**
     * The users that that belong to the team
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this
            ->belongsToMany(User::class)
            ->withPivot(['confirmed'])
            ->withTimestamps();
    }
}
