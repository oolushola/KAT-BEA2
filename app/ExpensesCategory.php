<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpensesCategory extends Model
{
    protected $table = 'tbl_kaya_expenses_categories';
    protected $fillable = [
        'category'
    ];
}
