<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\incentives;
use App\transporterRate;
use App\trip;
use App\Ago;

class incentivesController extends Controller
{
    public function index(){
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC '
            )
        );
        $incentiveLists = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.state FROM tbl_kaya_incentives a JOIN tbl_regional_state b ON a.state = b.regional_state_id'
            )
        );
        return view('finance.incentives.create', compact('states', 'incentiveLists'));
    }

    public function store(Request $request) {
        
        $validatedata = $this->validate($request, [
            'state' => 'required | integer',
            'exact_location_id' => 'required | string',
            'incentive_description' => 'required | string',
            'amount' => 'required'
        ]);
        
        $check = incentives::WHERE('state', $request->state)->WHERE('exact_location', $request->exact_location_id)->exists();
        if($check){
            return 'exists';
        } else {
            incentives::CREATE(['state'=>$request->state, 'exact_location' => $request->exact_location_id, 'incentive_description' => $request->incentive_description, 'amount' => $request->amount]);
            return 'saved';
        }
    }

    public function edit($id){
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC '
            )
        );
        $incentiveLists = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.state FROM tbl_kaya_incentives a JOIN tbl_regional_state b ON a.state = b.regional_state_id'
            )
        );
        $recid = incentives::findOrFail($id);
        $alldestionationsPerstate = transporterRate::WHERE('transporter_to_state_id', $recid->state)->GET();
        
        return view('finance.incentives.create', compact('states', 'incentiveLists', 'recid', 'alldestionationsPerstate', 'recid'));
    }

    public function update(Request $request, $id){
        $validatedata = $this->validate($request, [
            'state' => 'required | integer',
            'exact_location_id' => 'required | string',
            'incentive_description' => 'required | string',
            'amount' => 'required'
        ]);
        $check = incentives::WHERE('state', $request->state)->WHERE('exact_location', $request->exact_location_id)->WHERE('id', '<>', $request->id)->exists();
        if($check){
            return 'exists';
        } else {
            $recid = incentives::findOrFail($id);
            $recid->UPDATE(['state'=>$request->state, 'exact_location' => $request->exact_location_id, 'incentive_description' => $request->incentive_description, 'amount' => $request->amount]);
            return 'updated ';
        }
    }

    public function addAgo(Request $request) {
        $tripId = $request->trip_id;    
        $tripInfo = trip::WHERE('trip_id', $tripId)->GET()->FIRST();
        $newAgo = Ago::FIRSTORNEW(['trip_id' => $tripInfo->id]);
        $newAgo->amount = $request->amount;
        $newAgo->save();
        return 'saved';

    }
}
