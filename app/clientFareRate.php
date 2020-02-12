<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class clientFareRate extends Model
{
    protected $table = 'tbl_kaya_client_fare_rates';
    protected $fillable = [
        'client_id',
        'from_state_id',
        'to_state_id',
        'destination',
        'exception',
        'exception_amount',
        'tonnage',
        'amount_rate'
    ];
}
