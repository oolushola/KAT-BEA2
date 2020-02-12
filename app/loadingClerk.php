<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class loadingClerk extends Model
{
    protected $table = 'tbl_kaya_loading_clerks';
    protected $fillable =[
        'first_name',
        'last_name',
        'phone_no',
        'email',
        'email',
        'location_id',
        'field_ops_type',
        'address'
    ];
}
