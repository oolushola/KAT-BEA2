<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    protected $table = 'tbl_kaya_payment_vouchers';
    protected $fillable = [
        'requested_by',
        'request_timestamps',
        'check_status',
        'checked_by',
        'checked_timestamps',
        'approved_status',
        'approval_timestamps',
        'approved_by',
        'upload_status',
        'upload_timestamps',
        'uploaded_by',
        'voucher_status',
        'hod'
    ];
}
