<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class buhMonthlyTarget extends Model
{
    protected $table = 'tbl_kaya_buh_monthly_targets';
    protected $fillable = [
        'current_year',
        'current_month',
        'target',
        'user_id',
        'average_rating'
    ];
}
