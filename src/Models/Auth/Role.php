<?php

namespace Engelsystem\Models\Auth;

use Engelsystem\Models\BaseModel;
use Engelsystem\Models\Team;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

/**
 * @property integer                                                                       $id
 * @property string                                                                        $name
 * @property string|null                                                                   $description
 * @property \Carbon\Carbon                                                                $created_at
 * @property \Carbon\Carbon                                                                $updated_at
 *
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Auth\Permission[] $permissions
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\Team[]            $teams
 * @property-read \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[]       $users
 *
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User   whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Engelsystem\Models\User\User[] whereUpdatedAt($value)
 */
class Role extends BaseModel
{
    /** @var bool enable timestamps */
    public $timestamps = true;

    /** The attributes that are mass assignable */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The roles permissions
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this
            ->belongsToMany(Permission::class)
            ->withTimestamps();
    }

    /**
     * The teams that that have the role assigned
     *
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this
            ->belongsToMany(Team::class)
            ->withTimestamps();
    }

    /**
     * The users that that have the role assigned
     *
     * @return HasManyDeep
     */
    public function users()
    {
        return $this->hasManyDeepFromRelations($this->teams(), (new Team)->users());
    }
}
