<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transporter extends Model
{
    protected $table = 'tbl_kaya_transporters';
    protected $fillable = [
        'assign_user_id',
        'transporter_name',
        'email',
        'phone_no',
        'address',
        'bank_name',
        'account_name',
        'account_number',
        'guarantor_name',
        'guarantor_address',
        'guarantor_phone_no',
        'next_of_kin_name',
        'next_of_kin_address',
        'next_of_kin_phone_no',
        'next_of_kin_relationship'
    ];
}
