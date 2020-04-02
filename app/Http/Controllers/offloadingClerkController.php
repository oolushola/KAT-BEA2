<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\driversRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\loadingSite;
use App\transporter;
use App\truckType;
use App\trucks;
use App\drivers;
use App\product;
use App\trip;
use App\tripEvent;
use App\tripWaybill;
use App\tripWaybillStatus;
use App\client;
use App\clientProduct;
use App\completeInvoice;
use App\tripChanges;
use App\User;
use App\AdhocStaffAssignRegion;
use App\transporterRate;
use App\offloadWaybillRemark;
use Mail;

class offloadingClerkController extends Controller
{
    public function showTripsForOffload() {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        $loadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        $onJourneyTrips = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.first_name, h.last_name FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN users h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.user_id = h.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND a.tracker BETWEEN \'5\' AND \'7\' ORDER BY a.trip_id DESC '
            )
        );
        
        $tripWaybills = tripWaybill::GET();
        $waybillstatuses = tripWaybillStatus::GET();
        $products = product::SELECT('id', 'product')->ORDERBY('product')->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );

        $adHocStaffList = DB::SELECT(
            DB::RAW(
                'SELECT a.first_name, a.last_name, b.exact_location FROM users a INNER JOIN tbl_kaya_adhoc_staff_assign_regions b ON a.id = b.user_id'
            )
        );

        return view('orders.offloading-clerk-trips', compact('onJourneyTrips', 'tripWaybills', 'waybillstatuses', 'clients', 'loadingSites', 'transporters', 'products', 'states', 'adHocStaffList'));
    }

    public function assignaddHocStaffToRegion() {
        $users = User::WHERE('role_id', 7)->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $exactDestionations = transporterRate::SELECT('transporter_to_state_id', 'transporter_destination')->ORDERBY('transporter_destination', 'ASC')->DISTINCT()->GET();
        return view('authentication.assign-adhoc-staff-to-region', compact('users', 'states', 'exactDestionations'));
    }

    public function getUserAssignedRegion(Request $request){
        return $this->assignedLocations($request->user_id, $request->regional_state_id);
    }

    public function addRegionToUsers(Request $request) {
        $user_id = $request->user_id;
        $regional_state_id = $request->regional_state_id;
        $unassignedLocations = $request->exactLocationUnassigned;
        foreach($unassignedLocations as $specificLocation){
            AdhocStaffAssignRegion::CREATE(['user_id' => $user_id, 'regional_state_id' => $regional_state_id, 'exact_location' => $specificLocation]);
        }
        
        return $this->assignedLocations($user_id, $regional_state_id);
    }

    public function removeRegionFromUsers(Request $request) {
        $user_id = $request->user_id;
        $regional_state_id = $request->regional_state_id;
        $unassignedLocations = $request->exactLocationRight;
        foreach($unassignedLocations as $specificLocation){
            $specificRow = AdhocStaffAssignRegion::WHERE('user_id', $user_id)->WHERE('regional_state_id', $regional_state_id)->WHERE('exact_location', $specificLocation)->GET()->FIRST();
            $specificRow->DELETE();
        }
        return $this->assignedLocations($request->user_id, $regional_state_id);
    }

    public function assignedLocations($userId, $regionalState) {
        $result = ' <div class="row">
        <div class="col-md-5">
        &nbsp;';

            $result.='<div class="card" >
                <div class="table-responsive" style="max-height:450px">
                    <table class="table table-bordered">
                        <tbody style="font-size:10px;">
                            <tr>
                                <td class="table-primary font-weight-semibold" colspan="3">
                                    Assign Ad-hoc to Region
                                </td>
                            </tr>
                            <tr>
                                <td class="table-info" width="10%">
                                    <input type="checkbox" id="selectAllLeft">
                                </td>
                                <td class="table-info font-weight-semibold" colspan="2" id="selectAllLeftText">
                                    Select all available states
                                </td>
                            </tr>';
                            $specificLocationLeft = DB::SELECT(
                                DB::RAW(
                                    'SELECT DISTINCT transporter_destination from tbl_kaya_transporter_rates WHERE transporter_to_state_id = "'.$regionalState.'" AND transporter_destination NOT IN ( SELECT exact_location FROM tbl_kaya_adhoc_staff_assign_regions WHERE user_id = "'.$userId.'" AND regional_state_id = "'.$regionalState.'") ORDER BY transporter_destination ASC'
                                )
                            );
                            if(count($specificLocationLeft)){
                                $counter = 0;
                                foreach($specificLocationLeft as $locationNameLeft){
                                $counter++;
                                $counter % 2 == 0 ? $css = '' : $css='table-success';
                                
                                $result.='<tr class="'.$css.'" style="font-size:10px">
                                    <td>
                                        <input type="checkbox" class="exactLocationLeft" name="exactLocationUnassigned[]" value="'.$locationNameLeft->transporter_destination.'">
                                    </td>
                                    <td>'.strtoupper($locationNameLeft->transporter_destination).'</td>
                                </tr>';
                                }
                            }
                            else{
                                $result.='<tr>
                                    <td colspan="2">No loading site available to assign</td>
                                </tr>';
                            }
                            
                            
                        $result.='</tbody>
                    </table>
                </div>
            </div>

        </div>';

        

        $result.='<div class="col-md-2">
        &nbsp;
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-primary" id="assignLocations">Assign
                    <i class="icon-point-right ml-2"></i>
                </button>
                <br /><br />
                <button type="submit" class="btn btn-danger" id="removeAssignedLocations">Remove <i class="icon-point-left ml-2"></i></button>
            </div>
        </div>';

        $result.='<div class="col-md-5">
        &nbsp;

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody style="font-size:10px;">
                            <tr>
                                <td class="table-primary font-weight-semibold" colspan="3">Assigned Users to Region</td>
                            </tr>
                            <tr>
                                <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                <td class="table-info font-weight-semibold" colspan="2" id="assignedRightText">Select all assigned loading sites</td>
                            </tr>';
                            $specificLocationRight = DB::SELECT(
                                DB::RAW(
                                    'SELECT DISTINCT transporter_destination from tbl_kaya_transporter_rates WHERE transporter_to_state_id = "'.$regionalState.'" AND transporter_destination IN ( SELECT exact_location FROM tbl_kaya_adhoc_staff_assign_regions WHERE user_id = "'.$userId.'" AND regional_state_id = "'.$regionalState.'") ORDER BY transporter_destination ASC'
                                )
                            );
                            

                            if(count($specificLocationRight)){
                                $counter = 0;
                                foreach($specificLocationRight as $exactLocationRight){
                                $counter++;
                                $counter % 2 == 0 ? $css = '' : $css='table-success';
                                
                                $result.='<tr class="'.$css.'" style="font-size:10px">
                                    <td>
                                        <input type="checkbox" class="exactLocationRight" name="exactLocationRight[]" value="'.$exactLocationRight->transporter_destination.'">
                                    </td>
                                    <td>'.strtoupper($exactLocationRight->transporter_destination).'</td>
                                </tr>';
                                }
                            }
                            else{
                                $result.='<tr>
                                    <td colspan="2">You\'ve not assigned any loading site for this client</td>
                                </tr>';
                            }
                            
                        $result.='</tbody>
                    </table>
                </div>
            </div>


        </div>

        
    </div>';

    return $result;
    }

    public function offloadingClerkEventUpdate(Request $request) {
        $validateData = $this->validate($request, [
            'trip_id' => 'required | integer',
            'time_arrived_destination' => 'required | string',
            'time_offloading_started' => 'required | string',
            'time_offloading_end' => 'required | string',
            'offloaded_location' => 'required | string',
        ]);

        $currentDate = date('Y-m-d');

        $checkThisTripLastEvent = tripEvent::WHERE('trip_id', $request->trip_id)->GET()->LAST();
        if($checkThisTripLastEvent){
            if($checkThisTripLastEvent->current_date == $currentDate){
                $checkThisTripLastEvent->destination_status = TRUE;
                $checkThisTripLastEvent->time_arrived_destination = $request->time_arrived_destination;
                $checkThisTripLastEvent->offloading_status = TRUE;
                $checkThisTripLastEvent->offload_start_time = $request->time_offloading_started;
                $checkThisTripLastEvent->offload_end_time = $request->time_offloading_end;
                $checkThisTripLastEvent->offloaded_location = $request->offloaded_location;
                $checkThisTripLastEvent->save();
            } else {
                $createNewRecord = tripEvent::CREATE(['trip_id' => $request->trip_id, 'current_date' => $currentDate, 'journey_status' => 1, 'location_check_one' => $checkThisTripLastEvent->location_check_one, 'location_one_comment' => $checkThisTripLastEvent->location_one_comment, 'location_check_two' => $checkThisTripLastEvent->location_check_two, 'location_two_comment' => $checkThisTripLastEvent->location_two_comment, 'destination_status' => TRUE, 'time_arrived_destination' => $request->time_arrived_destination, 'offloading_status' => 1, 'offload_start_time' => $request->time_offloading_started, 'offload_end_time' => $request->time_offloading_end, 'offloaded_location' => $request->offloaded_location]);
            }
        } else{
            $createNewRecord = tripEvent::CREATE(['trip_id' => $request->trip_id, 'current_date' => $currentDate, 'journey_status' => 1, 'location_check_one' => '', 'location_one_comment' => '', 'location_check_two' => '', 'location_two_comment' => '', 'destination_status' => TRUE, 'time_arrived_destination' => $request->time_arrived_destination, 'offloading_status' => 1, 'offload_start_time' => $request->time_offloading_started, 'offload_end_time' => $request->time_offloading_end, 'offloaded_location' => $request->offloaded_location]);
        }
        $recid = trip::findOrFail($request->trip_id);
        $recid->tracker = 8; // implies offloaded.
        $recid->save();

        if($request->hasFile('recieved_waybill')) {
        $signedWaybill = $request->file('recieved_waybill');
        $name = 'signed-waybill-'.$request->trip_id.'.'.$signedWaybill->getClientOriginalExtension();
        $destination_path = public_path('assets/img/signedwaybills/');
        $waybillPath = $destination_path."/".$name;
        $signedWaybill->move($destination_path, $name);
        offloadWaybillRemark::CREATE(['trip_id' => $request->trip_id, 'waybill_collected_status' => TRUE, 'received_waybill' => $name]);
        } else {
            if($request->waybill_not_collected){
                offloadWaybillRemark::CREATE(['trip_id' => $request->trip_id, 'waybill_collected_status' => FALSE, 'waybill_remark' => $request->waybill_not_collected]);
            }
        }
        return 'updated';
    }

    public function offloadingClerkNotification(Request $request) {
        $trip_id = $request->trip_id;
        $exact_location = $request->exact_location;

        $specificTripNotification = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.id = "'.$trip_id.'" '
            )
        );

        $getWaybillInformation = tripWaybill::WHERE('trip_id', $trip_id)->GET();

        $notifier = User::findOrFail($request->user_id);
        $notifierEmail = $notifier->email;
        

        $getEmailsOfUsersToSendItTo = AdhocStaffAssignRegion::SELECT('user_id')->WHERE('exact_location', $exact_location)->GET();
        if(count($getEmailsOfUsersToSendItTo) <= 0 ){
            return 'no_user_found';
        }
        else{
            $totalCount = count($getEmailsOfUsersToSendItTo);
            $count = 0;
            foreach($getEmailsOfUsersToSendItTo as $specificUserEmail){
                $count++;
                $adHocStaffEmail = User::SELECT('email')->WHERE('id', $specificUserEmail->user_id)->GET()->FIRST();
                
                    $userTobeNotified[]=$adHocStaffEmail->email;

            }
        }

        try {
            Mail::send('notify-offload-clerk', array(
                'trip_id' => $specificTripNotification[0]->trip_id,
                'exact_location_id' => $specificTripNotification[0]->exact_location_id,
                'gated_out' => $specificTripNotification[0]->gated_out,
                'truck_no' => $specificTripNotification[0]->truck_no,
                'tonnage' => $specificTripNotification[0]->tonnage,
                'product' => $specificTripNotification[0]->product,
                'customers_name' => $specificTripNotification[0]->customers_name,
                'customer_no' => $specificTripNotification[0]->customer_no,
                'waybillDetails' => $getWaybillInformation,
                'driver_first_name' => $specificTripNotification[0]->driver_first_name,
                'driver_last_name' => $specificTripNotification[0]->driver_last_name,
                'driver_phone_number' => $specificTripNotification[0]->driver_phone_number,
                'motor_boy_first_name' => $specificTripNotification[0]->motor_boy_first_name,
                'motor_boy_last_name' => $specificTripNotification[0]->motor_boy_last_name,
                'motor_boy_phone_no' => $specificTripNotification[0]->motor_boy_phone_no,
                'customers_address' => $specificTripNotification[0]->customer_address
    
            ), function($message) use ($request, $trip_id, $notifierEmail, $userTobeNotified) {
                $message->from('no-reply@kayaafrica.co', 'KAYA-VISIBILITY');
                $message->to($notifierEmail, 'ON-JOURNEY TRIPS')->subject('NEW TRIP NOTIFICATION: '.$trip_id)->cc($userTobeNotified);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
        
        return 'sent';
    
    }
}
