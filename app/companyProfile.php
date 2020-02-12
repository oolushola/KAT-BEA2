<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class companyProfile extends Model
{
    protected $table = 'tbl_kaya_company_profiles';
    protected $fillable = [
        'company_name',
        'company_email',
        'website',
        'company_phone_no',
        'address',
        'company_logo',
        'bank_name',
        'account_name',
        'account_no',
        'tin',
        'authorized_user_id',
        'signatory'
    ];
}
