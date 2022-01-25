<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KayaChase extends Model
{
    protected $table = 'tbl_kaya_chases';
    protected $fillable = [
        'chase_id',
        'truck_id',
        'transporter_id',
        'driver_id',
        'chase_start_date',
        'eta',
        'preffered_loading_site',
        'preffered_destination',
        'remark',
        'push_status',
        'pop_status',
        'profiled_by',
        'last_updated_by'
    ];

   
}
