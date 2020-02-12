<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class drivers extends Model
{
    protected $table='tbl_kaya_drivers';
    protected $fillable = [
        'licence_no',
        'driver_first_name',
        'driver_last_name',
        'driver_phone_number',
        'motor_boy_first_name',
        'motor_boy_last_name',
        'motor_boy_phone_no'
    ];
}
