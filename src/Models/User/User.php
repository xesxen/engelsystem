<?php

namespace Engelsystem\Models\User;

use Engelsystem\Models\Auth\Role;
use Engelsystem\Models\BaseModel;
use Engelsystem\Models\Team;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

/**
 * @property integer                                                                       $id
 * @property string                                                                        $name
 * @property string                                                                        $email
 * @property string                                                                        $password
 * @property string                                                                        $api_key
 * @property \Carbon\Carbon|null                                                           $last_login_at
 * @property \Carbon\Carbon                                                                $created_at
 * @property \Carbon\Carbon                                                                $updated_at
 *
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\Contact      $contact
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\PersonalData $personalData
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\Settings     $settings
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\State        $state
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Auth\Permission[] $permissions
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Auth\Role[]       $roles
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Team[]            $supports
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Team[]            $teams
 *
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User   whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereApiKey($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereLastLoginAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereUpdatedAt($value)
 */
class User extends BaseModel
{
    /** @var bool enable timestamps */
    public $timestamps = true;

    /** The attributes that are mass assignable */
    protected $fillable = [
        'name',
        'password',
        'email',
        'api_key',
        'last_login_at',
    ];

    /** @var array The attributes that should be hidden for serialization */
    protected $hidden = [
        'api_key',
        'password',
    ];

    /** @var array The attributes that should be mutated to dates */
    protected $dates = [
        'last_login_at',
    ];

    /**
     * @return HasOne
     */
    public function contact()
    {
        return $this
            ->hasOne(Contact::class)
            ->withDefault();
    }

    /**
     * @return HasOne
     */
    public function personalData()
    {
        return $this
            ->hasOne(PersonalData::class)
            ->withDefault();
    }

    /**
     * The permissions that the user has
     *
     * @return HasManyDeep
     */
    public function permissions()
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role)->permissions());
    }

    /**
     * The roles that belong to the user
     *
     * @return HasManyDeep
     */
    public function roles()
    {
        return $this->hasManyDeepFromRelations($this->teams(), (new Team)->roles());
    }

    /**
     * The teams that are supported by the user
     *
     * @return BelongsToMany
     */
    public function supports()
    {
        return $this
            ->belongsToMany(Team::class, 'supporter_team')
            ->withTimestamps();
    }

    /**
     * The teams that belong to the user
     *
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this
            ->belongsToMany(Team::class)
            ->withPivot(['confirmed'])
            ->withTimestamps();
    }

    /**
     * @return HasOne
     */
    public function settings()
    {
        return $this
            ->hasOne(Settings::class)
            ->withDefault();
    }

    /**
     * @return HasOne
     */
    public function state()
    {
        return $this
            ->hasOne(State::class)
            ->withDefault();
    }
}
