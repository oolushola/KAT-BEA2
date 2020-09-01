<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tripEvent extends Model
{
    protected $table = 'tbl_kaya_trip_events';
    protected $fillable = [
        'trip_id',
        'current_date',
        'journey_status',
        'location_check_one',
        'location_one_comment',
        'location_check_two',
        'location_two_comment',
        'destination_status',
        'time_arrived_destination',
        'gate_in_time_destination',
        'offloading_status',
        'offload_start_time',
        'offload_end_time',
        'offloaded_location',
        'morning_lga',
        'morning_issue_type',
        'afternoon_lga',
        'afternoon_issue_type',
        'offload_issue_type',
        
    ];
}
