<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    protected $table = 'tbl_kaya_clients';
    protected $fillable =[
        'parent_company_status',
        'parent_company_id',
        'client_status',
        'company_name',
        'person_of_contact',
        'phone_no',
        'email',
        'country_id',
        'state_id',
        'address'
    ];
}
