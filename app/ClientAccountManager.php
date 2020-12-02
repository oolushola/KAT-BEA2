<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientAccountManager extends Model
{
    protected $table = 'tbl_kaya_client_account_manager';
    protected $fillable = [
        'client_id',
        'user_id'
    ];
}
