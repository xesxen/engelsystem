<?php

namespace Engelsystem\Models;

use Engelsystem\Models\Auth\Role;
use Engelsystem\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer                                                                 $id
 * @property string                                                                  $name
 * @property string|null                                                             $description
 * @property string|null                                                             $contact_name
 * @property string|null                                                             $contact_dect
 * @property string|null                                                             $contact_email
 * @property bool                                                                    $restricted  Join Needs additional
 *           confirmation
 * @property bool                                                                    $self_signup Can signup for shifts
 * @property bool                                                                    $requires_drivers_license
 * @property bool                                                                    $show_on_frontend
 * @property bool                                                                    $show_on_dashboard
 * @property \Carbon\Carbon                                                          $created_at
 * @property \Carbon\Carbon                                                          $updated_at
 *
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Auth\Role[] $roles
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] $supporters
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] $users
 *
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User   whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereContactDect($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereRestricted($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereSelfSignup($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[]
 *         whereRequiresDriversLicense($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereShowOnFrontend($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereShowOnDashboard($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereUpdatedAt($value)
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
