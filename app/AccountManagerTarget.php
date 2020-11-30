<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountManagerTarget extends Model
{
    //
    protected $table = 'tbl_kaya_account_manager_targets';
    protected $fillable = [
        'current_year',
        'current_month',
        'client_id',
        'target'  
    ];
}
