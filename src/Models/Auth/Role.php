<?php

namespace Engelsystem\Models\Auth;

use Carbon\Carbon;
use Engelsystem\Models\BaseModel;
use Engelsystem\Models\Team;
use Engelsystem\Models\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

/**
 * @property integer                                   $id
 * @property string                                    $name
 * @property string|null                               $description
 * @property Carbon                                    $created_at
 * @property Carbon                                    $updated_at
 *
 * @property-read QueryBuilder|Collection|Permission[] $permissions
 * @property-read QueryBuilder|Collection|Team[]       $teams
 * @property-read QueryBuilder|Collection|User[]       $users
 *
 * @method static QueryBuilder|Role              whereId($value)
 * @method static QueryBuilder|Collection|Role[] whereName($value)
 * @method static QueryBuilder|Collection|Role[] whereDescription($value)
 * @method static QueryBuilder|Collection|Role[] whereCreatedAt($value)
 * @method static QueryBuilder|Collection|Role[] whereUpdatedAt($value)
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
