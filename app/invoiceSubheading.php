<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invoiceSubheading extends Model
{
    protected $table = 'tbl_kaya_invoice_subheadings';
    protected $fillable = [
        'client_id',
        'sales_order_no_header',
        'invoice_no_header'
    ];
}
