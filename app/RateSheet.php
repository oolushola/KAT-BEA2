<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RateSheet extends Model
{
    protected $table="tbl_kaya_rate_sheets";
    protected $fillable = [
        'client_id',
        'state',
        'exact_location',
        'client_rate',
        'transporter_rate',
        'tonnage'
    ];
}
