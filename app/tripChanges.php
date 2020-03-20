<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tripChanges extends Model
{
    protected $table = 'tbl_kaya_trip_changes';
    protected $fillable = [
        'trip_id',
        'user_id',
        'changed_keys',
        'changed_values'
    ];
}
