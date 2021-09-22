<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientArrangement extends Model
{
    protected $table = 'tbl_kaya_pay_client_arrangements';
    protected $fillable = [
        'client_id',
        'payback_in',
        'interest_rate',
        'overdue_charge'
    ];
}
