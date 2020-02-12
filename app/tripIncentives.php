<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tripIncentives extends Model
{
    protected $table = 'tbl_kaya_trip_incentives';
    protected $fillable = [
        'trip_id',
        'incentive_description',
        'amount'

    ];
}
