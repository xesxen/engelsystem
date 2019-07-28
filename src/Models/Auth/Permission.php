<?php

namespace Engelsystem\Models\Auth;

use Engelsystem\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

/**
 * @property integer                                                                 $id
 * @property string                                                                  $name
 * @property string|null                                                             $description
 * @property \Carbon\Carbon                                                          $created_at
 * @property \Carbon\Carbon                                                          $updated_at
 *
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Auth\Role[] $roles
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] $users
 *
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User   whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereUpdatedAt($value)
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
