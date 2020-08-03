<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\buhMonthlyTarget;
use App\User;

class buhTargetMonthlyController extends Controller
{
    public function index() {
        $buhs = User::ORDERBY('first_name', 'ASC')->WHERE('role_id', 4)->orWhere('role_id', 6)->orWHERE('email', 'timi@kayaafrica.co')->GET();
        $buhTargets = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name FROM tbl_kaya_buh_monthly_targets a JOIN users b ON a.user_id = b.id ORDER BY a.current_month, a.target ASC'
            )
        );
        return view('performance-metric.business-unit-monthly-target', compact('buhTargets', 'buhs'));
    }

    public function store(Request $request) {
        $validateData = $this->validate($request, [
            'user_id' => 'required | integer',
            'target' => 'required | integer'
        ]);

        $checkUserValidity = buhMonthlyTarget::WHERE('current_year', $request->current_year)->WHERE('current_month', $request->current_month)->WHERE('user_id', $request->user_id)->exists();
        if($checkUserValidity){
            return 'exists';

        } else {
            buhMonthlyTarget::CREATE($request->all());
            return 'saved';
        }
        
    }

    public function edit(Request $request, $id) {
        $recid = buhMonthlyTarget::findOrFail($id);
        $buhs = User::ORDERBY('first_name', 'ASC')->WHERE('role_id', 4)->orWhere('role_id', 6)->GET();
        $buhTargets = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name FROM tbl_kaya_buh_monthly_targets a JOIN users b ON a.user_id = b.id ORDER BY a.current_month, a.target ASC'
            )
        );
        return view('performance-metric.business-unit-monthly-target', compact('buhTargets', 'buhs', 'recid'));
    }

    public function update(Request $request, $id) {
        $checkUserValidity = buhMonthlyTarget::WHERE('current_year', $request->current_year)->WHERE('current_month', $request->current_month)->WHERE('user_id', $request->user_id)->WHERE('id', '!=', $id)->exists();
        if($checkUserValidity) {
            return 'exists';
        }
        else{
            $recid = buhMonthlyTarget::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }

}
