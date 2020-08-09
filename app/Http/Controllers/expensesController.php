<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\expenses;

class expensesController extends Controller
{
    public function index() {
        $expenses = expenses::GET();
        return view('finance.financials.expenses', compact('expenses')); 
    }

    public function store(Request $request) {
        $checker = expenses::WHERE('year', $request->year)->WHERE('month', $request->month)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            expenses::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $recid = expenses::findOrFail($id);
        $expenses = expenses::GET();
        return view('finance.financials.expenses', compact('expenses', 'recid')); 
    }

    public function update(Request $request, $id) {
        $checker = expenses::WHERE('year', $request->year)->WHERE('month', $request->month)->WHERE('id', '!=', $id)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            $recid = expenses::FIND($id);
            $expenses = $recid->UPDATE($request->all());
            return 'updated';
        }
    }
}
