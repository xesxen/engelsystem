<?php

namespace Engelsystem\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property bool        $arrived
 * @property Carbon|null $arrival_date
 * @property bool        $active
 * @property bool        $force_active
 * @property bool        $got_shirt
 * @property int         $got_voucher
 *
 * @method static QueryBuilder|Collection|State[] whereArrived($value)
 * @method static QueryBuilder|Collection|State[] whereArrivalDate($value)
 * @method static QueryBuilder|Collection|State[] whereActive($value)
 * @method static QueryBuilder|Collection|State[] whereForceActive($value)
 * @method static QueryBuilder|Collection|State[] whereGotShirt($value)
 * @method static QueryBuilder|Collection|State[] whereGotVoucher($value)
 */
class State extends HasUserModel
{
    /** @var string The table associated with the model */
    protected $table = 'users_state';

    /** @var array The attributes that should be mutated to dates */
    protected $dates = [
        'arrival_date',
    ];

    /** The attributes that are mass assignable */
    protected $fillable = [
        'user_id',
        'arrived',
        'arrival_date',
        'active',
        'force_active',
        'got_shirt',
        'got_voucher',
    ];
}
