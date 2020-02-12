<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bulkPaymentHistory extends Model
{
    protected $table = 'tbl_kaya_bulk_payment_histories';
    protected $fillable = [
        'bulk_payment_id',
        'transporter_id',
        'amount_credited',
        'date_uploaded',
    ];
}
