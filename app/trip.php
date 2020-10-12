<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class trip extends Model
{
    protected $table = "tbl_kaya_trips";
    protected $fillable = [
        'gate_in',
        'client_id',
        'loading_site_id',
        'transporter_id',
        'truck_id',
        'driver_id',
        'product_id',
        'destination_state_id',
        'exact_location_id',
        'account_officer',
        'arrival_at_loading_bay',
        'loading_start_time',
        'loading_end_time',
        'departure_date_time',
        'gated_out',
        'customers_name',
        'customer_no',
        'loaded_quantity',
        'loaded_weight',
        'customer_address',
        'tracker',
        'trip_status',
        'day',
        'month',
        'year',
        'user_id',
        'completed_trip_report',
        'trip_type',
        'last_known_location'
    ];
}
