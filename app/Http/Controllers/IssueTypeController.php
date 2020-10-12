<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IssueType;
use Illuminate\Support\Facades\DB;
use App\trip;


class IssueTypeController extends Controller
{
    public function index() {
        $issueTypes = IssueType::ALL();
        return view('transportation.issues.issue-type', compact('issueTypes'));
    }

    public function store(Request $request) {
        $validator = $this->validate($request, [
            'issue_category' => 'required',
            'issue_type' => 'required'
        ]);
        if($validator) {
            $checker = IssueType::WHERE('issue_category', $request->issue_category)->WHERE('issue_type', $request->issue_type)->exists();
            if($checker) {
                return 'exists';
            }
            else {
                IssueType::CREATE($request->all());
                return 'saved';
            }
        }
        else{
            return $validator;
        }
    }

    public function edit($id) {
        $recid = IssueType::findOrFail($id);
        $issueTypes = IssueType::ALL();
        return view('transportation.issues.issue-type', compact('issueTypes', 'recid'));
    }

    public function update(Request $request, $id) {
        $validator = $this->validate($request, [
            'issue_category' => 'required',
            'issue_type' => 'required'
        ]);
        if($validator) {
            $checker = IssueType::WHERE('issue_category', $request->issue_category)->WHERE('issue_type', $request->issue_type)->WHERE('id', '!=', $id)->exists();
            if($checker) {
                return 'exists';
            }
            else {
                $issueType = IssueType::findOrFail($id);
                $issueType->UPDATE($request->all());
                $issueType->save();
                return 'updated';
            }
        }
        else{
            return $validator;
        }
    }

    public function delete() {

    }

    public function completedNotDropOff() {
        $semicompletedTrips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, a.exact_location_id, a.loaded_weight, a.last_known_location, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN users h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.user_id = h.id WHERE a.trip_status = \'1\' AND tracker = \'8\' AND trip_type = \'2\' ORDER BY a.trip_id DESC '
            )
        );
        return view('orders.semi-completed', compact('semicompletedTrips'));
    }

    public function updateSemitripLocation(Request $request) {
        $trip = trip::findOrFail($request->trip_id);
        $trip->last_known_location = $request->location;
        $trip->update();
        return 'updated';
    }

    public function dropOffCompleted(Request $request) {
        $trip = trip::findOrFail($request->id);
        $trip->trip_type = 1;
        $trip->last_known_location = NULL;
        $trip->UPDATE();
        return 'delivered';
    }
}
