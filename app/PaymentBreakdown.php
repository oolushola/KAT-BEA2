<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentBreakdown extends Model
{
    protected $table = 'tbl_kaya_trip_payment_breakdowns';
    protected $fillable = [
        'trip_id',
        'invoice_no',
        'date_paid',
        'amount'
    ];
}
