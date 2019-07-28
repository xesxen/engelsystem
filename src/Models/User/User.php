<?php

namespace Engelsystem\Models\User;

use Carbon\Carbon;
use Engelsystem\Models\Auth\Permission;
use Engelsystem\Models\Auth\Role;
use Engelsystem\Models\BaseModel;
use Engelsystem\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

/**
 * @property integer                                   $id
 * @property string                                    $name
 * @property string                                    $email
 * @property string                                    $password
 * @property string                                    $api_key
 * @property Carbon|null                               $last_login_at
 * @property Carbon                                    $created_at
 * @property Carbon                                    $updated_at
 *
 * @property-read QueryBuilder|Contact                 $contact
 * @property-read QueryBuilder|PersonalData            $personalData
 * @property-read QueryBuilder|Settings                $settings
 * @property-read QueryBuilder|State                   $state
 * @property-read QueryBuilder|Collection|Permission[] $permissions
 * @property-read QueryBuilder|Collection|Role[]       $roles
 * @property-read QueryBuilder|Collection|Team[]       $supports
 * @property-read QueryBuilder|Collection|Team[]       $teams
 *
 * @method static QueryBuilder|User              whereId($value)
 * @method static QueryBuilder|Collection|User[] whereName($value)
 * @method static QueryBuilder|Collection|User[] whereEmail($value)
 * @method static QueryBuilder|Collection|User[] wherePassword($value)
 * @method static QueryBuilder|Collection|User[] whereApiKey($value)
 * @method static QueryBuilder|Collection|User[] whereLastLoginAt($value)
 * @method static QueryBuilder|Collection|User[] whereCreatedAt($value)
 * @method static QueryBuilder|Collection|User[] whereUpdatedAt($value)
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
