<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tripPayment extends Model
{
    protected $table = 'tbl_kaya_trip_payments';
    protected $fillable = [
        'client_id',
        'trip_id',
        'transporter_rate_id',
        'advance',
        'balance',
        'advance_status',
        'balance_status',
        'remark'
    ];
}
