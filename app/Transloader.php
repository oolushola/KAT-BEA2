<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transloader extends Model
{
    protected $table = 'tbl_kaya_trip_transloaders';
    protected $fillable = [
        'trip_id', 'previous_truck_id', 'previous_driver_id', 'transloaded_truck_id', 
        'transloaded_driver_id', 'reason_for_transloading'
    ];
}
