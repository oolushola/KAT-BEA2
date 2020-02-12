<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class truckAvailability extends Model
{
    //
    protected $table = 'tbl_kaya_truck_availabilities';
    protected $fillable = [
        'client_id',
        'loading_site_id',
        'truck_id',
        'transporter_id',
        'driver_id',
        'product_id',
        'destination_state_id',
        'exact_location_id',
        'truck_status',
        'reported_by',
        'dated'
    ];
}
