<?php

namespace Engelsystem\Models\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property string $language
 * @property int    $theme
 * @property bool   $email_human
 * @property bool   $email_shiftinfo
 *
 * @method static QueryBuilder|Collection|Settings[] whereLanguage($value)
 * @method static QueryBuilder|Collection|Settings[] whereTheme($value)
 * @method static QueryBuilder|Collection|Settings[] whereEmailHuman($value)
 * @method static QueryBuilder|Collection|Settings[] whereEmailShiftinfo($value)
 */
class Settings extends HasUserModel
{
    /** @var string The table associated with the model */
    protected $table = 'users_settings';

    /** The attributes that are mass assignable */
    protected $fillable = [
        'user_id',
        'language',
        'theme',
        'email_human',
        'email_shiftinfo',
    ];
}
