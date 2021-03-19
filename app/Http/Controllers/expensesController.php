<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\expenses;
use App\ExpensesCategory;
use App\ExpensesBreakdown;

class expensesController extends Controller
{
    public function index() {
        $expenses = expenses::WHERE('year', date('Y'))->GET();
        $expensesCategories = ExpensesCategory::GET();
        return view('finance.financials.expenses', compact('expenses', 'expensesCategories')); 
    }

    public function store(Request $request) {
        $expensesDescriptions = $request->expenses_description;
        $amounts = $request->amount;
        $totalAmount = 0;
        foreach($expensesDescriptions as $key => $expenseDesc) {
            $totalAmount = $amounts[$key] + $totalAmount;
            ExpensesBreakdown::CREATE(
                [
                    'current_year' => $request->year,
                    'current_month' => $request->month,
                    'category' => $expenseDesc,
                    'amount' => $amounts[$key]
                ]
            );
        }
        
        $checker = expenses::WHERE('year', $request->year)->WHERE('month', $request->month)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            expenses::CREATE([ 'year' => $request->year, 'month' => $request->month, 'expenses' => $totalAmount ]);
            return 'saved';
        }
    }

    public function edit($id) {
        $recid = expenses::findOrFail($id);
        $expenses = expenses::WHERE('year', date('Y'))->GET();
        $expensesCategories = ExpensesBreakdown::WHERE('current_year', $recid->year)->WHERE('current_month', $recid->month)->GET();
        return view('finance.financials.expenses', compact('expenses', 'recid', 'expensesCategories')); 
    }

    public function update(Request $request, $id) {
        $expensesDescriptions = $request->expenses_description;
        $amounts = $request->amount;
        $totalAmount = 0;
        
        foreach($expensesDescriptions as $key => $expenseDesc) {
            $totalAmount = $amounts[$key] + $totalAmount;
            $expensesBreakdown = ExpensesBreakdown::firstOrNew(
                [
                    'current_year' => $request->year, 
                    'current_month' => $request->month, 
                    'category' => $expenseDesc
                ]
            );
            $expensesBreakdown->amount = $amounts[$key];
            $expensesBreakdown->save();
        }

        $checker = expenses::WHERE('year', $request->year)->WHERE('month', $request->month)->WHERE('id', '!=', $id)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            $recid = expenses::FIND($id);
            $expenses = $recid->UPDATE([
                'year' => $request->year, 
                'month' => $request->month, 
                'expenses' => $totalAmount
            ]);
            return 'updated';
        }
    }

    public function showOpex() {
        $currentYear = date('Y');
        return $this->opexListings(date('Y'));
    }

    public function opex(Request $request) {
        $currentYear = $request->opexYear;
        $currentMonth = $request->opexMonth;
        $opex = $request->opexValue;

        $operatingExpenses = expenses::firstOrNew(['year'=>$currentYear, 'month' => $currentMonth]);
        $operatingExpenses->opex = $opex;
        $operatingExpenses->save();
        return $this->opexListings(date('Y'));
    }

    function opexListings($year) {
       $opexListings = expenses::WHERE('year', $year)->GET();
        $response = '
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Year</th>
                        <th>Month</th>
                        <th>Operating Expense</th>
                    </tr>
                </thead>
                <tbody>';
        
        if(count($opexListings) > 0) {
            $count = 0;
            foreach($opexListings as $opex) {
                $count++;
                $response.='
                    <tr class="text-center">
                        <td>'.$count.'</td>
                        <td>'.$opex->year.'</td>
                        <td>'.date('F', mktime(0,0,0,$opex->month, 1, date($year))).'</td>
                        <td>'.number_format($opex->opex, 2).'</td>
                    </tr>
                ';
            }
        }
        else{
            $response.= '
                <tr>
                    <td colspan="4">You have not added any operating expense for the year</td>
                </tr>
            ';
        }
        $response.='
            <tbody>
            </table>
        ';

        return $response;
        
    }
}
