<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class completeInvoice extends Model
{
    protected $table = 'tbl_kaya_complete_invoices';
    protected $fillable = [
        'trip_id',
        'invoice_no',
        'completed_invoice_no',
        'vat_used',
        'withholding_tax_used'
    ];
}
