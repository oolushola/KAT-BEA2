<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffExperiences extends Model
{
    protected $table= 'tbl_kaya_staff_experiences';
    protected $fillable = [
        'user_id', 'company_name', 'position_held', 'company_from', 'company_to', 'company_address'
    ];
}
