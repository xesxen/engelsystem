<?php

namespace Engelsystem\Models\User;

use Engelsystem\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property integer                $user_id
 *
 * @property-read QueryBuilder|User $user
 *
 * @method static QueryBuilder|static whereUserId($value)
 */
abstract class HasUserModel extends BaseModel
{
    /** @var string The primary key for the model */
    protected $primaryKey = 'user_id';

    /** The attributes that are mass assignable */
    protected $fillable = [
        'user_id',
    ];

    /** The relationships that should be touched on save */
    protected $touches = ['user'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
