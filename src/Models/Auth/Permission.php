<?php

namespace Engelsystem\Models\Auth;

use Carbon\Carbon;
use Engelsystem\Models\BaseModel;
use Engelsystem\Models\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

/**
 * @property integer                             $id
 * @property string                              $name
 * @property string|null                         $description
 * @property Carbon                              $created_at
 * @property Carbon                              $updated_at
 *
 * @property-read QueryBuilder|Collection|Role[] $roles
 * @property-read QueryBuilder|Collection|User[] $users
 *
 * @method static QueryBuilder|Permission              whereId($value)
 * @method static QueryBuilder|Collection|Permission[] whereName($value)
 * @method static QueryBuilder|Collection|Permission[] whereDescription($value)
 * @method static QueryBuilder|Collection|Permission[] whereCreatedAt($value)
 * @method static QueryBuilder|Collection|Permission[] whereUpdatedAt($value)
 */
class Permission extends BaseModel
{
    /** @var bool enable timestamps */
    public $timestamps = true;

    /** The attributes that are mass assignable */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The roles that have this permission
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
     * The users that that have the permission
     *
     * @return HasManyDeep
     */
    public function users()
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role)->users());
    }
}
