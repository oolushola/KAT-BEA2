<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cargoAvailability extends Model
{
    protected $table = 'tbl_kaya_cargo_availabilities';
    protected $fillable = [
        'current_year',
        'current_month',
        'client_id',
        'available_order'
    ];
}
