<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invoiceClientRename extends Model
{
    protected $table = 'tbl_kaya_invoice_biller';
    protected $fillable = [
        'invoice_no',
        'client_name',
        'client_address'
    ];
}
