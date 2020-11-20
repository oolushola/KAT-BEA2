<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IssueType;
use Illuminate\Support\Facades\DB;
use App\trip;
use App\truckType;
use App\trucks;
use App\drivers;
use App\Transloader;

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

    public function exactTrip(Request $request) {
        $tripInfo = DB::SELECT(
            DB::RAW(
                'SELECT a.id, driver_first_name, driver_last_name, driver_phone_number, motor_boy_first_name, motor_boy_last_name, motor_boy_phone_no, truck_no, truck_type, tonnage, b.id as truck_id,   d.id as driver_id FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_truck_types c JOIN tbl_kaya_drivers d ON a.truck_id = b.id AND b.truck_type_id = c.id AND a.driver_id = d.id WHERE trip_id = "'.$request->trip_id.'"'
            )
        );

        $transloader = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, b.created_at, c.truck_no as transloaded_from, d.truck_no as transloaded_to FROM tbl_kaya_trips a JOIN tbl_kaya_trip_transloaders b JOIN tbl_kaya_trucks c ON a.id = b.trip_id AND b.previous_truck_id = c.id LEFT JOIN tbl_kaya_trucks d ON b.transloaded_truck_id = d.id WHERE a.id = '.$tripInfo[0]->id.' '
            )
        );

        $preview = '
        <table class="table tablestriped">
            <tbody>
                <tr>
                    <td class="text-center">TRANSLOADED FROM</td>
                    <td class="text-center font-weight-bold">TRANSLOADED TO</td>
                    <td class="text-center font-weight-bold">DATE</td>
                </tr>';
                if(count($transloader) > 0) {
                    $preview.='
                    <tr>
                        <td class="text-center">'.$transloader[0]->transloaded_from.'</td>
                        <td class="text-center font-weight-bold">'.$transloader[0]->transloaded_to.'</td>
                        <td class="text-center font-weight-bold">'.$transloader[0]->created_at.'</td>
                    </tr>';
                }
                else {
                    $preview.='
                        <tr>
                            <td colspan="3" class="text-center">No transload record found</td>
                        </tr>';
                }
            $preview.='
            </tbody>
        </table>';
        
        return array(
            'tripInfo' => $tripInfo,
            'preview' => $preview
        );

    }

    public function transloadTruck(Request $request) {
        $transporter_id = $request->transporter_id;
        $checkTruckNumber = trucks::WHERE('truck_no', $request->truck_no)->GET()->LAST();
        if($checkTruckNumber) {
            $truckId = $checkTruckNumber->id;
            $isTruckNoInTripPipeline = trip::WHERE('truck_id', $truckId)->WHERE('tracker', '>= 1 AND <= 7')->WHERE('trip_status', TRUE)->GET();
            if(count($isTruckNoInTripPipeline) > 0) {
                return 'truckInPipeline';
            }
            elseif($truckId == $request->current_truck_id) {
                return 'sameTruckNo';
            }
        }
        else{
           $getTruckTypeId = truckType::WHERE('tonnage', $request->tonnage)->WHERE('truck_type', $request->truck_type)->GET()->LAST();
           $truckTypeId = $getTruckTypeId->id;
           $truckNo = $request->truck_no;
           $trucks = trucks::CREATE(['transporter_id' => $transporter_id, 'truck_no' => $truckNo, 'truck_type_id' => $truckTypeId ]);
           $truckId = $trucks->id;
        }

        if(!$request->sameDriverChecker) {
            $driverInfo = drivers::CREATE(['driver_first_name' => $request->driverName, 'driver_phone_number' => $request->driverNo, 'motor_boy_first_name' => $request->motorboyInfo]);
            $driverId = $driverInfo->id;
        }
        else{
            $driverId = $request->current_driver_id;
        }

        $transloader = Transloader::firstOrNew([ 'trip_id' => $request->transload_trip_id ]);
        $transloader->previous_truck_id = $request->current_truck_id;
        $transloader->previous_driver_id = $request->current_driver_id;
        $transloader->transloaded_truck_id = $truckId;
        $transloader->transloaded_driver_id = $driverId;
        $transloader->reason_for_transloading = $request->transloading_comment;
        $transloader->save();

        return 'transloadingCompleted';
    }

    public function changeDriverInfo(Request $request) {
        $recid = drivers::findOrFail($request->driver);
        $recid->driver_first_name = $request->name;
        $recid->driver_phone_number = $request->phoneNo;
        $recid->motor_boy_first_name = $request->motorBoy;
        $recid->save();
        return 'updated';
    }
}
