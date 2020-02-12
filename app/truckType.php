<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class truckType extends Model
{
    protected $table = 'tbl_kaya_truck_types';
    protected $fillable = [
        'truck_type_code',
        'truck_type',
        'tonnage'
    ];
}
