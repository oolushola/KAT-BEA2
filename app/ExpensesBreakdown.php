<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpensesBreakdown extends Model
{
    protected $table = 'tbl_kaya_expenses_breakdowns';
    protected $fillable = [
        'current_year', 'current_month', 'category', 'amount'
    ];
}
