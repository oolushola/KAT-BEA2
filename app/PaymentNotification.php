<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentNotification extends Model
{
    protected $table = 'tbl_kaya_payment_notifications';
    protected $fillable = [
        'trip_id', 'amount', 'payment_for', 'uploaded_by', 'uploaded_at', 'paid_status', 'paid_time_stamps'
    ];
    protected $dates = [
        'created_at'
    ];
}
