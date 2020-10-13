<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badging extends Model
{
    protected $table = 'tbl_kaya_trip_badgings';
    protected $fillable = [
        'client_id', 'trip_id'
    ];
}
