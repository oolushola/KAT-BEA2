<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;
use App\trip;
use Illuminate\Support\Facades\DB;
use App\truckAvailability;
use App\loadingSite;
use App\target;
use App\tripEvent;

class dashboardController extends Controller
{
    public function uploadProfilePhoto(Request $request) {
        $recid = User::findOrFail(base64_decode($request->user));
        if($request->hasFile('file')){
            $photo = $request->file('file');
            $name = str_slug($request->fullname).$request->user.'.'.$photo->getClientOriginalExtension();
            $destination_path = public_path('assets/img/users/');
            $profilePhotoPath = $destination_path."/".$name;
            $photo->move($destination_path, $name);
            $recid->photo = $name;
            $recid->save();
            return 'uploaded';
        }
    }

    public function changePassword(Request $request) {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required_with:confirm_new_password|min:6',
            'confirm_new_password' => 'required'
        ]);
        $user = User::findOrFail(base64_decode($request->userIdentification));
        if(Hash::check($request->old_password, $user->password)) {
            $newPassword = Hash::make($request->new_password);
            $user->password = $newPassword;
            $user->save();
            return 'changed';
        }
        else{
            return 'wrongpass';
        }
    }

    public function lastTripId(Request $request) {
        return $lastTripId = trip::SELECT('trip_id')->GET()->LAST();
    }

    public function statusChecker($tracker) { 
        if($tracker == 1){ $current_stage = 'GATED IN';}
        if($tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
        if($tracker == 3){ $current_stage = 'LOADING';}
        if($tracker == 4){ $current_stage = 'DEPARTURE';}
        if($tracker == 5){ $current_stage = 'GATED OUT';}
        if($tracker == 6){ $current_stage = 'ON JOURNEY';}
        if($tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
        if($tracker == 8){ $current_stage = 'OFFLOADED';}
        return $current_stage;
    }

    public function tripFinders(Request $request) {
        $rangeFrom = $request->rangeFrom;
        $rangeTo = $request->rangeTo;
        
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT trip_id, loading_site, truck_no, transporter_name, exact_location_id, gated_out, tracker FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_trucks c  JOIN tbl_kaya_transporters d ON a.loading_site_id = b.id AND a.truck_id = c.id AND a.transporter_id = d.id WHERE trip_id BETWEEN "'.$rangeFrom.'" AND "'.$rangeTo.'" '
            )
        );
        return $res = $this->finderResponse($trips);
    }

    public function searchTripFinder(Request $request) {
        $checker = $request->checker;
        if($checker == 1) {
            $trips = DB::SELECT(
                DB::RAW(
                    'SELECT a.id, trip_id, `exact_location_id`, truck_no, transporter_name, loading_site, gated_out, tracker
                    FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_transporters c JOIN tbl_kaya_loading_sites d ON a.transporter_id = c.id AND a.truck_id = b.id AND a.loading_site_id = d.id WHERE (MATCH(trip_id, transporter_name) AGAINST("+'.$request->search.'" IN BOOLEAN MODE)) OR (truck_no LIKE "'.$request->search.'%") '
                )
            );
            return $res = $this->finderResponse($trips, $checker);
        }
        else {
            $waybills = DB::SELECT(
                DB::RAW(
                    'SELECT a.id, a.trip_id, `exact_location_id`, truck_no, transporter_name, loading_site, gated_out, tracker, sales_order_no, invoice_no FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_transporters c JOIN tbl_kaya_loading_sites d JOIN tbl_kaya_trip_waybills e ON a.transporter_id = c.id AND a.truck_id = b.id AND a.loading_site_id = d.id AND e.trip_id = a.id WHERE (MATCH(sales_order_no, invoice_no) AGAINST("+'.$request->search.'" IN BOOLEAN MODE))'
                )
            );
            return $this->finderResponse($waybills, $checker);
        }
        
    }


    function finderResponse($tripLog, $tracker) {
        $response = '<table class="table table-bordered" id="exportTableDataFinder">
            <thead style="font-size:11px; background:#000; color:#fff">
                <tr>
                    <th>KAID</th>
                    <th>LOADING SITE</th>
                    <th>TRUCK NO</th>
                    <th>TRANSPORTER</th>
                    <th>DESTINATION</th>
                    <th class="text-center">GATE OUT</th>';
                    if($tracker == 2) {
                        $response.='
                            <th colspan="2">WAYBILL INFO</th>
                        ';
                    }
                    $response.='<th>CURRENT STAGE</th>
                </tr>
            </thead>
            <tbody style="font-size:10px" class="font-weight-semibold">';
            if(count($tripLog)){
                $counter = 0;
                foreach($tripLog as $key=> $trip) {
                    $counter++;
                    $counter % 2 == 0 ? $css = ' table-success ' : $css = ' ';
                    $response.='
                    
                    <tr class="'.$css.'">
                        <td><a href="/trip-overview/'.$trip->trip_id.'" target="_new">'.$trip->trip_id.'</a></td>
                        <td>'.strtoupper($trip->loading_site).'</td>
                        <td>'.strtoupper($trip->truck_no).'</td>
                        <td>'.$trip->transporter_name.'</td>
                        <td>'.$trip->exact_location_id.'</td>
                        <td class="text-center">';
                            if(isset($trip->gated_out)) {
                                $response.= date('d/m/Y H:i:s', strtotime($trip->gated_out));
                            }
                            else{
                                $response.= 'Yet to gate out';
                            }
                            if($tracker == 2) {
                                $response.='
                                    <td colspan="2">'.strtoupper($trip->sales_order_no).', '.strtoupper($trip->invoice_no).'</td>
                                ';
                            }
                        $response.='</td>
                        <td>'.$this->statusChecker($trip->tracker).'</td>
                    </tr>';
                }
            }
            else{
                $response.='<tr>
                    <td colspan="7" class="font-weight-bold text-danger">Oops! we can\'t find any trip that matches your search</td>
                </tr>';
            }
                
            $response.='</tbody>
        </table>';
        return $response;
    }

    public function realTimeNotification(Request $request) {
        $current_date = date('Y-m-d');
        $totalGateOutCount = trip::WHERE('trip_status', 1)->WHERE('tracker', '>=', 5)->GET()->COUNT();
        $availableTrucks = truckAvailability::WHERE('status', !TRUE)->GET()->COUNT();
        $highValueTrips = DB::SELECT(
            DB::RAW(
                'SELECT COUNT(*) as currentMonthGateOut FROM tbl_kaya_trips WHERE MONTH(gated_out)=MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE()) AND trip_status = 1 and tracker >= 5 AND client_id != "1" AND transporter_id != "141"'
            )
        );

        $gateIn = trip::WHERE('tracker', 1)->WHERE('trip_status', '!=', 0)->GET()->COUNT();
        $loadingBay = trip::WHERE('tracker', '>=', 2)->WHERE('tracker', '<=', '3')->WHERE('trip_status', '!=', 0)->GET()->COUNT();
        $departedLoadingBay = trip::WHERE('tracker', 4)->WHERE('trip_status', '!=', 0)->GET()->COUNT();
        $onJourney = trip::WHERE('tracker', '>=', '5')->WHERE('tracker', '<=', 6)->WHERE('trip_status', '!=', 0)->GET()->COUNT();
        $atDestination = trip::WHERE('tracker', 7)->GET()->COUNT();
        $offloadedTrips = DB::SELECT(
            DB::RAW(
                'SELECT COUNT(*) as offloadedTrips FROM tbl_kaya_trips a JOIN tbl_kaya_trip_events b ON a.id = b.trip_id WHERE offloading_status = TRUE AND DATE(offload_start_time) = "'.$current_date.'" AND a.tracker = \'8\''
            )
        );
        $numberofdailygatedout = trip::WHEREDATE('gated_out', date('Y-m-d'))->GET()->COUNT();
        $todaysDate = date('d');
        $count=1;
        $dateYearAndMonth = date('Y-m');
        do{
            $newDate = $dateYearAndMonth.'-'.$count;
            $count++;
            $noOfTripsPerDay[] = trip::whereDATE('gated_out',  $newDate)->WHERE('client_id', '!=', 1)->WHERE('transporter_id', '!=', 141)->GET()->COUNT();
        }
        while($count <= $todaysDate);
        $getGatedOutByMonth = DB::SELECT(
            DB::RAW(
                'SELECT COUNT(*) as currentMonthGateOut FROM tbl_kaya_trips WHERE MONTH(gated_out)=MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE()) AND trip_status = 1 and tracker >= 5 AND client_id != "1" AND transporter_id != "141"'
            )
        );
        $lastOneWeek = date('Y-m-d', strtotime('last sunday'));
        $currentDate = date('Y-m-d');
        $noOfGatedOutTripForCurrentWeek = $this->specificDateRangeCount('COUNT(*)',  'currentWeekCount', $lastOneWeek, $currentDate);
        $lastOneMonth = DB::SELECT(
            DB::RAW(
                'SELECT COUNT(*) AS monthlyGateOutTrip FROM tbl_kaya_trips WHERE trip_status = \'1\' AND tracker >= \'5\' AND MONTH(gated_out) = MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE())'
            )
        );
        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        foreach($allLoadingSites as $loadingSite){
            $countDailyTripByLoadingSite[] = trip::WHERE('loading_site_id', $loadingSite->id)->WHEREDATE('gated_out', date('Y-m-d'))->WHERE('tracker', '>=', 5)->GET()->COUNT();
            $loading_sites[] = strtoupper($loadingSite->loading_site);
        }
        $target = target::SELECT('target')->GET()->LAST();
        $achieved = $highValueTrips[0]->currentMonthGateOut;

        $remainder = $target->target - $achieved;
        if($remainder <= 0) {
            $leftOver = 0;
        }
        else {
            $leftOver = $remainder;
        }

        $percentageTarget = number_format(($achieved / $target->target) * 100, 1);

        return array(
            'total_gate_out' => $totalGateOutCount,
            'truck_available' => $availableTrucks,
            'high_value_trips' => $achieved,
            'trip_status' => [$gateIn, $loadingBay, $departedLoadingBay, $onJourney, $atDestination, $offloadedTrips[0]->offloadedTrips],
            'current_day_gate_out_count' => $numberofdailygatedout,
            'trips_per_day' => $noOfTripsPerDay,
            'current_week_gate_out' => $noOfGatedOutTripForCurrentWeek[0]->currentWeekCount,
            'loading_site_daily_count' => $countDailyTripByLoadingSite,
            'loading_site' => $loading_sites,
            'target' => [$leftOver, $achieved],
            'percentage' => $percentageTarget,
            'achieved' => $achieved,
            'monthlyTarget' => $target->target
        );
    }

    function specificDateRangeCount($condition, $alias, $start, $finish){
        return DB::SELECT(
            DB::RAW(
                'SELECT '.$condition.' as '.$alias.'  FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$start.'" and "'.$finish.'" and tracker >= 5'
            )
        );
    }

    public function tripStatusResult(Request $request) {
        $currentDate = date('Y-m-d');
        $status = $request->trip_status;
        if($status == 'Gate In') { 
            $trips = $this->recordTracker(1, 1);  $fieldId = 'gate_in'; $tracker = 1;
        }
        if($status == 'At Loading Bay'){ 
            $trips = $this->recordTracker(2, 3); $fieldId = 'arrival_at_loading_bay';  $tracker = 2;
        }
        if($status == 'Departed Loading Bay'){ 
            $trips = $this->recordTracker(4, 4); $fieldId = 'departure_date_time'; $tracker = 4;
        }
        if($status == 'On Journey'){ 
            $trips = $this->recordTracker(5, 6); $fieldId = 'gated_out'; $tracker = 5;
        }
        if($status == 'At Destination'){ 
            $trips = $this->recordTracker(7, 7); $fieldId = 'gated_out'; $tracker = 7;
        }
        if($status == 'Offloaded'){ 
            $trips = $this->offloadedRecords($currentDate); $fieldId = 'gated_out'; $tracker = 8;
        }
        
        $tripEventListing = [];
        foreach($trips as $trip) {
            $tripEventListing[] = tripEvent::WHERE('trip_id', $trip->id)->GET()->LAST();
        }
        $response = $this->displayRecords(strtoupper($status), $trips, $fieldId, $tripEventListing, $tracker);
        return $response;
    }

    function recordTracker($firstTrack, $secondTrack) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker BETWEEN "'.$firstTrack.'" AND "'.$secondTrack.'"  ORDER BY a.trip_id ASC'
            )
        );
        return $query;
    }

    function offloadedRecords($current_date) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_events h ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND h.trip_id = a.id WHERE a.trip_status = \'1\' AND offloading_status = TRUE AND DATE(offload_start_time) = "'.$current_date.'" AND a.tracker = \'8\' ORDER BY a.trip_id DESC'
            )
        );
        return $query;
    }

    function displayRecords($activeSubheading, $arrayObject, $fieldLabel, $tripEvent, $tracker) {
        $data = '<div class="table-responsive">
            <table class="table table-striped table-hover">
            <thead>
                <tr class="table-success">
                    <th class="text-center">SN</th>
                    <th>'.$activeSubheading.'</th>
                    <th>TRUCK</th>
                    <th>ORDER DETAILS</th>';
                    if($tracker >=5 && $tracker <= 6){
                        $data.= '<th>LAST SEEN</th>';
                    }
                    if($tracker == 7){
                        $data.= '<th>ARRIVED AT?</th>';
                    }
                    if($tracker <= 4) {
                        $data.= '<th>COMMENT</th>';
                    }
                $data.='
                </tr>
            </thead>
            <tbody id="masterDataTable">';
                if(count($arrayObject)) {
                    $count = 0;
                    foreach($arrayObject as $object) {
                        $count +=1;

                        $comment = '<i class="icon-comment ml-4 text-danger operationsUpdate pointer" id="'.$object->trip_id.'"></i>';
                        $object->operations_remark ? $classes = 'ml-1 d-block bg-info font-size-xs p-1' : $classes = '';
                        $operationResult = '
                        <span id="defaultOPR'.$object->trip_id.'" class="'.$classes.'">
                            '.$object->operations_remark.' <strong class="d-block">'.$object->opr_remarks_timestamp.'</strong>
                        </span>
                        <span id="oploader'.$object->trip_id.'"></span>';

                        $inputText = '<input type="text" class="mt-2 d-none" id="operations'.$object->trip_id.'" value="'.$object->operations_remark.'" />';

                        $data.='<tr>
                        <td class="text-center">('.$count.')</td>
                        <td>
                            <a href="/trip-overview/'.$object->trip_id.'" target="_blank">
                                <p class="font-weight-bold" style="margin:0">'.$object->trip_id.'</p>
                            </a>
                            <p  style="margin:0; "class="text-warning font-weight-bold">'.$object->loading_site.',</p>';
                            if($tracker <=5) {
                                $data.='<p>'.date('d-m-Y', strtotime($object->$fieldLabel)).' <br> '.date('H:i A', strtotime($object->$fieldLabel)).'</p>';
                            }
                            if($object->$fieldLabel >= 5 && $object->truck_type == 'Flatbed') {
                                $data.='<p class="font-size-xs font-weight-bold">'.$object->loaded_weight.'</p>';
                            }
                        $data.='
                        </td>
                        <td width="30%">
                            <span class="text-primary"><b>'.$object->truck_no.'</b></span>
                            <p style="margin:0"><b>Truck Type</b>: '.$object->truck_type.', '.$object->tonnage/1000 .'T</p>
                            <p style="margin:0"><b>Transporter</b>: '.$object->transporter_name.', <a href="tel:+'.$object->phone_no.'"> '.$object->phone_no.'</a> </p>
                        </td>';
                        $data.='<td><p style="margin:0" class="text-primary font-weight-bold">Destination</p>
                            <p style="margin-bottom:3px">'.$object->exact_location_id.'</p>
                            <p  style="margin:0" class="text-primary font-weight-bold">Product</p>
                            <p style="margin:0">'.$object->product.'</p>
                        </td>';
                        if($tracker <= 4) {
                            $data.='
                            <td width="25%">
                                <p>'.$comment.'</p>
                                '.$inputText.'
                                <p>'.$operationResult.'</p>
                            </td>';
                        }
                        
                        if($tracker >=5 && $tracker <=6){
                            $data.='<td width="25%">';
                            $counter = 1;
                            
                            foreach($tripEvent as $onJourneyTrips) {
                                if($onJourneyTrips && $onJourneyTrips->trip_id === $object->id) {
                                    if($onJourneyTrips->location_check_two) {
                                        $data.='
                                            <p class="font-size-sm ml-1 font-weight-bold d-block m-0">
                                                '.$onJourneyTrips->location_two_comment.',
                                            </p>';
                                        $data.='
                                            <p class="font-size-xs d-block ml-1 m-0">
                                                '.date('d-m-Y, H:i A', strtotime($onJourneyTrips->location_check_two)).'
                                                '.$comment.'
                                            <p>';
                                        $data.='
                                        <span class="font-size-xs text-danger d-block ml-2">
                                            '.$onJourneyTrips->afternoon_issue_type.'
                                        </span>';

                                    }
                                    else {
                                        $data.='
                                            <p class="font-size-sm ml-1 font-weight-bold d-block m-0">
                                                '.$onJourneyTrips->location_one_comment.',
                                            </p>';
                                        $data.='
                                            <p class="font-size-xs d-block ml-1 m-0">
                                                '.date('d-m-Y, H:i A', strtotime($onJourneyTrips->location_check_one)).'
                                                '.$comment.'
                                            <p>';
                                        $data.='
                                            <span class="font-size-xs text-danger d-block ml-2">
                                                '.$onJourneyTrips->morning_issue_type.'
                                            </span>';
                                    }

                                    $data.= $inputText;

                                    $data.= $operationResult;
                                }
                            }
                            $now = time(); // or your date as well
                            $your_date = strtotime($object->gated_out);
                            $datediff = $now - $your_date;
                            $noOfDays = round($datediff / (60 * 60 * 24));

                            $noOfDays > 5 ? $className = 'notifier' : $className = '';

                            $data.= '<p class=" defaultNotifier '.$className.'">
                                <a href="trip-overview/'.$object->trip_id.'"> '.$noOfDays.' Days </a>
                                </p>';
                            $data.='
                            <input type="text" class="finance-report__input d-none"  />
                            </td>';
                        }
                        if($tracker == 7){
                            $data.='<td width="25%">';
                            foreach($tripEvent as $tripActivities){
                                if($tripActivities && $tripActivities->trip_id == $object->id){
                                    if($tripActivities->time_arrived_destination == ''){
                                        $timeArrivedDestination = '';
                                    } else {
                                        $timeArrivedDestination = date('d-m-Y, H:i A', strtotime($tripActivities->time_arrived_destination));
                                    } 
                                    $data.='<span class="text-primary font-weight-semibold font-size-sm">'.$timeArrivedDestination.' '.$comment.'</span>';
                                    $data.=$inputText.'
                                    <p>'.$operationResult.'</p>
                                </td>';
                                }
                            }
                            $data.='</td>';
                            
                           
                        }
                        $data.='
                        </tr>';
                    }
                }
                else {
                    $data.='<tr><td colspan="3">No record is available.</td></tr>';
                }
                
            $data.='</tbody>
        </table>
         </div>';
        return $data;
    }

    public function updateOperationsRemark(Request $request) {
        $currentTimeAndDate = date('d-m-Y, H:i:s A');
        $trip_id = $request->trip_id;
        $remark = $request->remark;
        $remarks = trip::WHERE('trip_id', $trip_id)->GET()->LAST();
        $remarks->operations_remark = $remark;
        $remarks->opr_remarks_timestamp = $currentTimeAndDate;
        $remarks->save();
        return 'updated';
    }

    public function truckAvailabilityData(Request $request) {
        $availableTrucks = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.loading_site, d.truck_no, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no, g.product, h.state, i.first_name, i.last_name, j.tonnage, j.truck_type FROM tbl_kaya_truck_availabilities a JOIN tbl_kaya_clients b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f JOIN tbl_kaya_products g JOIN tbl_regional_state h JOIN users i JOIN tbl_kaya_truck_types j ON a.client_id = b.id AND a.loading_site_id = c.id AND a.truck_id = d.id AND a.transporter_id = e.id and a.driver_id = f.id and a.product_id = g.id and a.destination_state_id = h.regional_state_id and a.reported_by = i.id AND d.truck_type_id = j.id  WHERE a.status = FALSE'
            )
        );
        
        $response =
        '<table class="table table-striped table-hover">
            <thead>
                <tr class="table-success font-size-sm">
                    <th>SN</th>
                    <th width="25%">AVAILABLE FOR</th>
                    <th>TRUCK DETAILS</th>
                    <th>STATUS</th>
                </tr>
            </thead>';

            $response.='
            <tbody id="monthlyGatedOutData">';
                $count = 1; 
                if(count($availableTrucks)) {
                    foreach($availableTrucks as $availableTruck) {
                    $response.='
                    <tr>
                        <td>('.$count++.')</td>
                        <td>
                            <p class="font-weight-bold" style="margin:0; padding:0">'.$availableTruck->loading_site.'</p>
                            <p style="margin:0"><span class="text-primary font-weight-bold">Location</span>: '.$availableTruck->exact_location_id.'</p>
                            <p style="margin:0"><span class="text-primary font-weight-bold">Product:</span>'.$availableTruck->product.'</p>
                        </td>
                        <td>
                            <span class="text-primary"><b>'.$availableTruck->truck_no.'</b></span>
                            <p style="margin:0" class="font-size-xs"><b>Truck Type</b>: '.$availableTruck->truck_type.' '.$availableTruck->tonnage / 1000 .'T</p>
                            <p style="margin:0"><b>'.$availableTruck->transporter_name.'</b>: '.$availableTruck->phone_no.'</p>
                        </td>
                        <td>
                            <p style="margin:0" class="text-primary-400 font-weight-bold">Status: '.$availableTruck->truck_status.'</p>
                            <p class="font-size-sm">Profiled by: '.ucfirst($availableTruck->first_name).' '.ucfirst($availableTruck->last_name).', at '.date('d-m-Y H:i A', strtotime($availableTruck->updated_at)).'</p>
                            

                        </td>
                    </tr>';
                    }
                }
                else
                {
                    $response.=
                    '<tr>
                        <td colspan="4">No trip is available</td>
                    </tr>';
                }
            $response.='
            </tbody>
        </table>';

        return $response;
    }

    public function todayGateOut(Request $request) {
        $currentDate = date('Y-m-d');
        $currentGateOutRecord = $this->displayRecordOfTrips('gated_out', $currentDate);
        $data = 
        '<table class="table table-striped table-hover">
            <thead class="table-success font-size-sm" style="font-size:11px">
                <tr>
                    <th width="20%" class="text-center font-weight-bold">GATE OUT DETAILS</th>
                    <th width="30%" class="font-weight-bold">TRUCK</th>
                    <th width="20%" class="font-weight-bold">WAYBILL DETAILS</th>
                    <th width="30%" class="font-weight-bold">CONSIGNEE DETAILS</th>
                </tr>
            </thead>
            <tbody id="currentGateOutData">';
                if(count($currentGateOutRecord)) {
                    foreach($currentGateOutRecord as $specificRecord) {
                    $data.='<tr>
                        <td class="text-center">
                            <p class="font-weight-bold" style="margin:0">'.$specificRecord->trip_id.'</p>
                            <p>'.$specificRecord->loading_site.' <br> '.date('d-m-Y', strtotime($specificRecord->gated_out)).' <br> '.date('h:i A', strtotime($specificRecord->gated_out)).' </p>
                        </td>
                        <td>
                            <span class="text-primary"><b>'.$specificRecord->truck_no.'</b></span>
                            <p style="margin:0"><b>Truck Type</b>: '.$specificRecord->truck_type.' '.$specificRecord->tonnage / 1000 .' T</p>
                            <p style="margin:0"><b>Transporter</b>: '.$specificRecord->transporter_name.', '.$specificRecord->phone_no.'</p>
                        </td>
                        <td>';
                            foreach($tripWaybills as $tripWaybill) {
                                if($specificRecord->id == $tripWaybill->trip_id) {
                                $data.='<span class="mb-2"><a href="assets/img/waybills/'.$tripWaybill->photo.'" target="_blank" title="View waybill '.$tripWaybill->sales_order_no.'">'.$tripWaybill->sales_order_no.'
                                '.$tripWaybill->invoice_no.'</a></span>';
                                }
                            }
                        $data.='
                        </td>
                        <td>
                            <p class="font-weight-bold" style="margin:0">'.$specificRecord->customers_name.'</p>
                            <p  style="margin:0">Location: '.$specificRecord->exact_location_id.'</p>
                            <p  style="margin:0">Product: '.$specificRecord->product.'</p>
                        </td>
                    </tr>';
                    }
                }
                else {
                    $data.='<tr><td colspan="4">No record is available.</td></tr>';
                }
                $data.='
            </tbody>
        </table>';

        return $data;
    }

    function displayRecordOfTrips($fieldValue, $currentDate) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND DATE('.$fieldValue.') = "'.$currentDate.'" ORDER BY a.trip_id DESC'
            )
        );
        return $query;
    }

    function displayRecordOfTripsTwo($fieldValue, $currentDate) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND a.client_id != \'1\' AND DATE('.$fieldValue.') = "'.$currentDate.'" ORDER BY a.trip_id DESC'
            )
        );
        return $query;
    }
}
