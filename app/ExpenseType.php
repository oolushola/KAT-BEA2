<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    protected $table = 'tbl_kaya_expense_types';
    protected $fillable = [
        'expense_type'
    ];
}
