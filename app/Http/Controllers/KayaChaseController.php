<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KayaChase;
use Illuminate\Support\Facades\DB;
use App\trip;
use Auth;

class KayaChaseController extends Controller
{
    public function displayFollowUpDetails(Request $request, $tripId) 
    {
        $tripInfo = trip::findorFail($tripId);
        $previousFollowUps = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_kaya_chases a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters c JOIN tbl_kaya_drivers d ON a.truck_id = "'.$tripInfo->truck_id.'" AND a.transporter_id = c.id AND a.driver_id = d.id WHERE a.truck_id = "'.$tripInfo->truck_id.'"'
            )
        );
        return view('kaya-chase.offload-follow-up', 
            compact(
                'tripInfo',
                'previousFollowUps'
            )
        );
    }

    public function createFollowUp(Request $request, $tripId)
    {
        $getLastFollowUpId = KayaChase::SELECT('chase_id')->LATEST()->FIRST();
        $lastFollowUpId = str_replace('KFU', '', $getLastFollowUpId->follow_up_id);
        $counter = intval('0000') + intval($lastFollowUpId) + 1;
        $followUpId = 'KAID'.sprintf('%04d', $counter);

        $tripInfo = trip::WHERE('trip_id', $tripId)->GET()->LAST();

        $checkFollowUpRecord = KayaChase::WHERE('truck_id', $tripInfo->truck_id)->WHERE('active_status', TRUE)->exists();
        if($checkFollowUpRecord) 
        {
            return 'exists';
        }

        $createFollow = KayaChase::CREATE([
            'chase_id' => $followUpId,
            'truck_id' => $truck_id,
            'transporter_id' => $transporter_id,
            'driver_id' => $driver_id,
            'chase_start_date' => $request->chase_start_date,
            'eta' => $request->eta,
            'preffered_loading_site' => $request->preffered_loading_site,
            'preffered_destination' => $request->preffered_destination,
            'remark' => $request->remark,
            'push_status' => FALSE,
            'pop_status' => FALSE,
            'profiled_by' => Auth::user()->first_name.' '.Auth::user()->last_name,
            'last_updated_by' => Auth::user()->first_name.' '.Auth::user()->last_name
        ]);
    }
}
