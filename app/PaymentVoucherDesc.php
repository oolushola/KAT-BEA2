<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucherDesc extends Model
{
    protected $table = 'tbl_kaya_payment_voucher_descs';
    protected $fillable = [
        'payment_voucher_id',
        'description',
        'owner',
        'amount'
    ];
}
