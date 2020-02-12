<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class trucks extends Model
{
    protected $table = 'tbl_kaya_trucks';
    protected $fillable = [
        'transporter_id',
        'truck_type_id',
        'truck_no'
    ];
}
