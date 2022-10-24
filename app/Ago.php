<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ago extends Model
{
    protected $table = 'tbl_kaya_agos';
    protected $fillable = [
        'trip_id',
        'amount'
    ];
}
