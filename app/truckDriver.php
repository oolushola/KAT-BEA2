<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class truckDriver extends Model
{
    protected $table = 'tbl_kaya_truck_drivers';
    protected $fillable = [
        'truck_id',
        'driver_id'
    ];
}
