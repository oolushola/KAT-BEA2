<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = "tbl_kaya_departments";
    protected $fillable = [
        'head_of_department',
        'department'
    ];
}
