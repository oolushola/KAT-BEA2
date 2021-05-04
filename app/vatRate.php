<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vatRate extends Model
{
    protected $table = 'tbl_kaya_vat_rates';
    protected $fillable = [
        'client_id',
        'withholding_tax',
        'vat_rate'
    ];
}
