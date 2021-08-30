<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExpenseType;


class ExpenseTypeController extends Controller
{
    public function index() {
        $expenseTypes = ExpenseType::GET();
        return view('finance.financials.expense-type', compact('expenseTypes')); 
    }

    public function store(Request $request) {
        $checker = ExpenseType::WHERE('expense_type', $request->expense_type)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            $expenseType = ExpenseType::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $recid = ExpenseType::findOrFail($id);
        $expenseTypes = ExpenseType::GET();
        return view('finance.financials.expense-type', compact('recid', 'expenseTypes')); 
    }

    public function update(Request $request, $id) {
        $checker = ExpenseType::WHERE('expense_type', $request->expense_type)->WHERE('id', '!=', $id)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            $recid = ExpenseType::findOrFail($id);
            $recid->expense_type = $request->expense_type;
            $recid->update();
            return 'updated';
        }
    }
}
