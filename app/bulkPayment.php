<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bulkPayment extends Model
{
    protected $table = 'tbl_kaya_bulk_payments';
    protected $fillable = [
        'transporter_id',
        'balance',
        'amount_credited',
        'date_uploaded',
        'date_approved',
        'approval_status',
        'remark'
    ];
}
