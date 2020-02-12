<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transporterRate extends Model
{
    protected $table = 'tbl_kaya_transporter_rates';
    protected $fillable = [
        'transporter_from_state_id',
        'transporter_to_state_id',
        'transporter_destination',
        'transporter_tonnage',
        'transporter_amount_rate'
    ];
}
