<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExpenseType;
use App\Department;
use Illuminate\Support\Facades\DB;
use App\DepartmentExpenseType;

class ExpenseTypeController extends Controller
{
    public function index() {
        $expenseTypes = ExpenseType::ORDERBY('expense_type', 'ASC')->GET();
        $departments = Department::ORDERBY('department', 'ASC')->GET();
        return view('finance.financials.expense-type', compact('expenseTypes', 'departments')); 
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
        $departments = Department::GET();
        return view('finance.financials.expense-type', compact('recid', 'expenseTypes', 'departments')); 
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

    public function departmentExpenseType(Request $request)
    {
        return $this->responseLogger($request->department_id);
    }

    public function assignDepartmentExpenseType(Request $request) {
        foreach($request->expenseTypes as $key => $expense_type_id) {
            DepartmentExpenseType::CREATE([
                'department_id' => $request->department_id,
                'expense_type_id' => $expense_type_id
            ]);
        }
        return $this->responseLogger($request->department_id);
    }

    public function removeDepartmentExpenseType(Request $request) 
    {
        foreach($request->assignedExpenseTypes as $key => $expense_type_id) {
           $recid = DepartmentExpenseType::WHERE('department_id', $request->department_id)->WHERE('expense_type_id', $expense_type_id)->GET()->FIRST();
           $recid->DELETE();
        }
        return $this->responseLogger($request->department_id);
    }

    function responseLogger($department)
    {
        $availableExpenseTypes = DB::SELECT(
            DB::RAW(
                'SELECT id, expense_type FROM tbl_kaya_expense_types WHERE id NOT IN (SELECT expense_type_id FROM tbl_kaya_department_expense_types WHERE department_id = "'.$department.'") ORDER BY expense_type ASC'
            )
        );
        $response = '
        <div class="row">
            <div class="col-md-5">
                &nbsp;

                <div class="card" >
                    <div class="table-responsive" style="max-height:1050px">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="5">Expense Type</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllLeftText">
                                        Select all expense types
                                    </td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="assignClient">';
                                if(count($availableExpenseTypes)) {
                                    $count = 0;
                                    foreach($availableExpenseTypes as $key => $expenseType) {
                                        $count++; 
                                        if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } 
                                        $response.='<tr class="'.$cssStyle.'">
                                            <td>
                                                <input type="checkbox" value="'.$expenseType->id.'" class="availableExpenseType" name="expenseTypes[]" />
                                            </td>
                                            <td>'.$expenseType->expense_type.'</td>
                                        </tr>';
                                    }
                                }
                                else {
                                    $response.='
                                    <tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You do not have any expense type yet</td>
                                    </tr>';
                                }
                            $response.='</tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                &nbsp;
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="assignExpenseType">ASSIGN
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeExpenseType">REMOVE <i class="icon-point-left ml-2"></i></button>
                </div>
            </div>';

            $assignedExpenseTypeDepartments = DB::SELECT(
                DB::RAW(
                    'SELECT id, expense_type FROM tbl_kaya_expense_types WHERE id IN (SELECT expense_type_id FROM tbl_kaya_department_expense_types WHERE department_id = "'.$department.'") ORDER BY expense_type ASC'
                )
            );

            $response.='
            <div class="col-md-5">
                &nbsp;
                <!-- Contextual classes -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="4">Assigned Expense Types</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all assigned clients</td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;">';
                                if(count($assignedExpenseTypeDepartments)) {
                                    $count = 0;
                                    foreach($assignedExpenseTypeDepartments as $key => $assignedExpenseType) {
                                        $count++; 
                                        if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } 
                                        $response.='<tr class="'.$cssStyle.'">
                                            <td>
                                                <input type="checkbox" value="'.$assignedExpenseType->id.'" class="assignedExpenseType" name="assignedExpenseTypes[]" />
                                            </td>
                                            <td>'.$assignedExpenseType->expense_type.'</td>
                                        </tr>';
                                    }
                                }
                                else{
                                    $response.='
                                    <tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You\'ve not assigned any expense type yet.</td>
                                    </tr>';
                                }
                            $response.='
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /contextual classes -->
            </div>
        </div>';

        return $response;
    }
}
