<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    protected $table = 'tbl_kaya_payment_histories';
    protected $fillable = [
        'trip_id',
        'amount',
        'payment_mode',
    ];
}
