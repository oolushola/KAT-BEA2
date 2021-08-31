<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentExpenseType extends Model
{
    protected $table = "tbl_kaya_department_expense_types";
    protected $fillable = [
        "department_id",
        "expense_type_id"
    ];
}
