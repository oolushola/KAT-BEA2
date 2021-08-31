<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Department;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index() {
        $users = User::SELECT('id', 'first_name', 'last_name')->WHERE('status', TRUE)->WHERE('role_id', '<=', 4)->ORWHERE('role_id', '>= 7')->ORDERBY('first_name', 'ASC')->GET();
        $departments = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name FROM tbl_kaya_departments a JOIN users b ON a.head_of_department = b.id '
            )
        );
        return view('departments.department', compact('users', 'departments'));
    }

    public function store(Request $request) {
        $checker = Department::WHERE('head_of_department', $request->head_of_department)->WHERE('department', $request->department)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            Department::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $recid = Department::findOrFail($id);
        $users = User::SELECT('id', 'first_name', 'last_name')->WHERE('status', TRUE)->WHERE('role_id', '<=', 4)->ORWHERE('role_id', '>= 7')->ORDERBY('first_name', 'ASC')->GET();
        $departments = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name FROM tbl_kaya_departments a JOIN users b ON a.head_of_department = b.id '
            )
        );
        return view('departments.department', compact('users', 'departments', 'recid'));
    }

    public function update(Request $request, $id) {
        $checker = Department::WHERE('head_of_department', $request->head_of_department)->WHERE('department', $request->department)->WHERE('id', '!=', $id)->exists();
        if($checker) {
            return 'exists';
        }
        else {
            $recid = Department::findOrFail($id);
            $recid->head_of_department = $request->head_of_department;
            $recid->department = $request->department;
            $recid->save();
            return 'updated';
        }
    }
}
