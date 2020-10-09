<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffDependants extends Model
{
    protected $table = 'tbl_kaya_staff_dependants';
    protected $fillable = [
        'user_id',
        'dependant_full_name',
        'dependant_type',
        'dependant_dob',
        'dependant_address',
    ];
}
