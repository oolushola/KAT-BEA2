<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invoiceSpecialRemark extends Model
{
    protected $table = 'tbl_kaya_invoice_special_remarks';
    protected $fillable = [
        'condition',
        'invoice_no',
        'description',
        'amount',
    ];
}


