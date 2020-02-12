<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tripWaybillStatus extends Model
{
    protected $table = 'tbl_kaya_trip_waybill_statuses';
    protected $fillable = [
        'trip_id',
        'waybill_status',
        'comment'
    ];
}
