<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffEducation extends Model
{
    protected $table = 'tbl_kaya_staff_educations';
    protected $fillable = [
        'user_id',
        'school_name',
        'school_address',
        'sch_start_from',
        'sch_end',
        'qualification_obtained',
        'specialization' 
    ];
}
