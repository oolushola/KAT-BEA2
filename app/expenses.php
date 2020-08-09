<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class expenses extends Model
{
    protected $table = 'tbl_kaya_expenses';
    protected $fillable = [
        'year',
        'month',
        'expenses'
    ];
}
