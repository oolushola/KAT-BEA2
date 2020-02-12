<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class incentives extends Model
{
    protected $table = 'tbl_kaya_incentives';
    protected $fillable = [
        'state',
        'exact_location',
        'incentive_description',
        'amount'
    ];
}
