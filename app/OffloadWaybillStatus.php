<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OffloadWaybillStatus extends Model
{
    protected $table = 'tbl_kaya_offload_waybill_statuses';
    protected $fillable = [
        'trip_id',
        'has_eir',
        'date_offloaded'
    ];
}
