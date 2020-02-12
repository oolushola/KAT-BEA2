<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class target extends Model
{
    protected $table = 'tbl_kaya_targets';
    protected $fillable = [
        'current_month',
        'current_year',
        'target'
    ];
}
