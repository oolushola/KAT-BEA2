<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffExtras extends Model
{
    protected $table = 'tbl_kaya_staff_extras';
    protected $fillable = [
        'user_id',
        'guarantor_full_name',
        'guarantor_phone_no',
        'guarantor_address',
        'nok_full_name',
        'nok_phone_no',
        'nok_address'
    ];
}
