<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class offloadWaybillRemark extends Model
{
    protected $table = 'tbl_kaya_offload_waybill_remarks';
    protected $fillable = [
       'trip_id', 
       'waybill_collected_status',
       'received_waybill',
       'waybill_remark'
    ];
}
