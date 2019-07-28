<?php

namespace Engelsystem\Models\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property string|null $dect
 * @property string|null $email
 * @property string|null $mobile
 *
 * @method static QueryBuilder|Collection|Contact[] whereDect($value)
 * @method static QueryBuilder|Collection|Contact[] whereEmail($value)
 * @method static QueryBuilder|Collection|Contact[] whereMobile($value)
 */
class Contact extends HasUserModel
{
    /** @var string The table associated with the model */
    protected $table = 'users_contact';

    /** The attributes that are mass assignable */
    protected $fillable = [
        'user_id',
        'dect',
        'email',
        'mobile',
    ];
}
