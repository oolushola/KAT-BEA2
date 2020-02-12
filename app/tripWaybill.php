<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tripWaybill extends Model
{
    protected $table = 'tbl_kaya_trip_waybills';
    protected $fillable = [
        'trip_id',
        'waybill_status',
        'sales_order_no',
        'remark',
        'photo',
        'approve_waybill',
        'moment_uploaded',
        'moment_approved',
        'invoice_status',
        'invoice_number',
        'date_invoiced'
    ];
}
