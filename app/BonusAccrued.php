<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusAccrued extends Model
{
    protected $table = 'tbl_kaya_bonus_accrueds';
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'bonus_accrued'
    ];
}
