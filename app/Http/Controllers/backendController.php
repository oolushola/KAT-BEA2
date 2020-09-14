<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\loadingSite;
use App\transporter;
use App\truckType;
use App\trucks;
use App\drivers;
use App\product;
use App\clientFareRate;
use App\trip;
use App\tripEvent;
use App\tripWaybill;
use App\tripWaybillStatus;
use App\client;
use App\transporterRate;
use App\tripPayment;
use App\bulkPayment;
use Auth;
use App\User;
use App\cargoAvailability;
use App\target;
use Session;

class backendController extends Controller
{
    public function login() {
        return view('login');
    }

    public function checkLogin(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:3'
        ]);
        $loginCredentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($loginCredentials)){
            $getTripId = trip::SELECT('id', 'trip_id')->ORDERBY('trip_id', 'ASC')->GET();
            
            return redirect('dashboard');

        } else {
            return back()->with('error', 'Invalid Login Details');
        }
    }

    public function successLogin() {
        $allTrips = trip::SELECT('id', 'trip_id')->ORDERBY('trip_id', 'DESC')->WHERE('tracker', '>=', 5)->GET();
        $current_month = date('F');
        $current_year = date('Y');
        $current_date = date('Y-m-d');

        $monthlyTarget = target::WHERE('current_month', $current_month)->WHERE('current_year', $current_year)->GET()->LAST();
        $getGatedOutByMonth = trip::WHERE('month', $current_month)->WHERE('year', $current_year)->WHERE('tracker', '>=', 5)->GET()->COUNT();

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
        $gatedOutForTheMonth = trip::WHERE('month', $current_month)->WHERE('year', $current_year)->WHERE('tracker', '>=', 5)->GET()->COUNT();

        // comment out this section
        $lastOneWeek = date('Y-m-d', strtotime('last sunday'));
        $currentDate = date('Y-m-d');

        $noOfGatedOutTripForCurrentWeek = $this->specificDateRangeCount('COUNT(*)',  'weeklygateout', $lastOneWeek, $currentDate);
        $specificDataRecord = $this->specificDateRangeData($lastOneWeek, $currentDate);
        // Down to this place
        
        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        foreach($allLoadingSites as $loadingSite){
            $countDailyTripByLoadingSite[] = trip::WHERE('loading_site_id', $loadingSite->id)->WHEREDATE('gated_out', date('Y-m-d'))->WHERE('tracker', '>=', 5)->GET()->COUNT();
            $loading_sites[] = strtoupper($loadingSite->loading_site);
        }

        $allclients = client::ORDERBY('company_name', 'ASC')->GET();

        $advancePendingApproval = trip::WHERE('advance_request', TRUE)->WHERE('advance_paid', FALSE)->GET()->COUNT();
        $balancePendingApproval = DB::SELECT(
            DB::RAW(
                'SELECT COUNT(*) as balancecount from tbl_kaya_trips a JOIN tbl_kaya_trip_payments b ON a.id = b.trip_id WHERE a.advance_paid = TRUE AND b.advance_paid = TRUE AND a.`balance_request` = TRUE AND b.`balance_paid` = FALSE'
            )
        );
        $paymentRequested = $advancePendingApproval + $balancePendingApproval[0]->balancecount;
        $clients = client::WHERE('client_status', '1')->GET()->COUNT();

        Session::put([
            'payment_request' => $paymentRequested,
            'on_journey' => $onJourney,
            'client' => $clients,
            'offloaded_trips' => $offloadedTrips
        ]);

        $currentGateOutRecord = $this->displayRecordOfTrips('gated_out', $currentDate);
        $gateInData = $this->recordTracker(1, 1);
        $atloadingbayData = $this->recordTracker(2, 3);
        $departedLoadingBayData =  $this->recordTracker(4, 4);
        $onJourneyData =  $this->recordTracker(5, 6);
        $atDestinationData = $this->recordTracker(7, 7);
        $offloadedData = $this->offloadedRecords($currentDate);

        $tripWaybills = tripWaybill::GET();
        $tripRecordsForTheMonth = $this->totalTripsForTheCurrentMonth();
        $totalGateOuts = trip::WHERE('gated_out', '<>', '')->WHERE('trip_status', '<>', 0)->GET()->COUNT();

        foreach($tripRecordsForTheMonth as $key => $monthTripId) {
            $monthlyGateOut[] = tripWaybill::SELECT('id', 'trip_id', 'sales_order_no', 'invoice_no', 'photo')->WHERE('trip_id', $monthTripId->id)->GET();
        }

        foreach($monthlyGateOut as $key => $values) {
            foreach($values as $value) {
                $monthWaybillRecord[] = $value;
            }
        }

               
        $todaysDate = date('d');
        $count=1;
        $dateYearAndMonth = date('Y-m');
        do{
            $newDate = $dateYearAndMonth.'-'.$count;
            $count++;
            $noOfTripsPerDay[] = trip::whereDATE('gated_out',  $newDate)->GET()->COUNT();
        }
        while($count <= $todaysDate);
        
        $tripEventListing = tripEvent::ORDERBY('current_date', 'DESC')->GET();
       

        $availableTrucks = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.loading_site, d.truck_no, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no, g.product, h.state, i.first_name, i.last_name, j.tonnage, j.truck_type FROM tbl_kaya_truck_availabilities a JOIN tbl_kaya_clients b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f JOIN tbl_kaya_products g JOIN tbl_regional_state h JOIN users i JOIN tbl_kaya_truck_types j ON a.client_id = b.id AND a.loading_site_id = c.id AND a.truck_id = d.id AND a.transporter_id = e.id and a.driver_id = f.id and a.product_id = g.id and a.destination_state_id = h.regional_state_id and a.reported_by = i.id AND d.truck_type_id = j.id  WHERE a.status = FALSE'
            )
        );

        $tripWaybillYetToReceive = DB::SELECT(
            DB::RAW('SELECT a.*, b.comment, c.loading_site, d.transporter_name, e.product, f.truck_no FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f ON a.id = b.trip_id AND a.loading_site_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id WHERE b.waybill_status = FALSE AND a.trip_status = 1'
            )
        );

        return view('dashboard', compact('getGatedOutByMonth', 'allTrips', 'monthlyTarget', 'onJourney', 'atDestination', 'offloadedTrips',  'numberofdailygatedout', 'gatedOutForTheMonth', 'countDailyTripByLoadingSite', 'loading_sites', 'noOfGatedOutTripForCurrentWeek', 'loadingBay', 'gateIn', 'allclients', 'departedLoadingBay', 'currentGateOutRecord', 'tripWaybills', 'gateInData', 'atloadingbayData', 'departedLoadingBayData', 'onJourneyData', 'atDestinationData', 'offloadedData', 'tripRecordsForTheMonth', 'totalGateOuts', 'noOfTripsPerDay', 'availableTrucks', 'tripEventListing', 'tripWaybillYetToReceive', 'specificDataRecord', 'monthWaybillRecord'));
    }

    function displayRecordOfTrips($fieldValue, $currentDate) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND DATE('.$fieldValue.') = "'.$currentDate.'" ORDER BY a.trip_id DESC'
            )
        );
        return $query;
    }

    function recordTracker($firstTrack, $secondTrack) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker BETWEEN "'.$firstTrack.'" AND "'.$secondTrack.'"  ORDER BY a.trip_id DESC'
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

    function totalTripsForTheCurrentMonth() {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND MONTH(gated_out) = MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE()) ORDER BY a.gated_out DESC'
            )
        );
        return $query;
    }

    public function waybillDetails() {
        $record = $this->totalTripsForTheCurrentMonth();
        foreach($record as $data) {
            echo $data;
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    public function userRegistration(Request $request) {
        $users = User::GET();
        return view('authentication.user', compact('users'));
    }

    public function registerUser(Request $request) {
        $this->validate($request, [
            'first_name' => 'required|string|min:3|max:50',
            'last_name' => 'required|string|min:3|max:50',
            'email' => 'required|email',
            'phone_no' => 'required',
            'role_id' => 'required|integer'
        ]);

        $check = User::WHERE('email', $request->email)->exists();
        if($check) {
            return 'exists';
        }
        else{
            $user = User::firstOrNew([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'role_id' => $request->role_id,
                'password' => Hash::make($request->last_name)
            ]);
            $user->save();
            return 'saved';
        }
    }

    public function editUserRegistration($id) {
        $users = User::GET();
        $recid = User::findOrFail($id);
        return view('authentication.user', 
            compact(
                'recid',
                'users'
            )
        );
    }

    public function updateUserRegistration(Request $request, $id) {
        $check = User::WHERE('email', $request->email)->WHERE('id', '<>', $id)->exists();
        if($check) {
            return 'exists';
        }
        else{
            $recid = User::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'saved';
        }
    }

    public function gatedOutSelectedWeek(Request $request){
        $from = $request->from;
        $to = $request->to;
        $specificDataRecord = $this->specificDateRangeData($from, $to);
        [$selectedWeeklyCountRange] = $this->specificDateRangeCount('COUNT(gated_out)', 'TotalWeekly', $from, $to);
        $tripWaybills = tripWaybill::GET();


        $data = '<table class="table table-striped table-hover">
            <thead class="table-success" style="font-size:10px;">
            <tr>
                <th>SN</th>
                <th width="20%" class="text-center font-weight-bold">GATE OUT DETAILS</th>
                <th width="30%" class="font-weight-bold">TRUCK</th>
                <th width="20%" class="font-weight-bold">WAYBILL DETAILS</th>
                <th width="30%" class="font-weight-bold">CONSIGNEE DETAILS</th>
            </tr>
            </thead>
            <tbody id="currentGateOutData" style="font-size:10px;">';
                if(count($specificDataRecord)) {
                    $count = 1;
                    foreach($specificDataRecord as $specificRecord) {
                    $data.='<tr>
                        <td>('.$count++.')</td>
                        <td class="text-center">
                            <a href="/trip-overview/'.$specificRecord->trip_id.'">
                                <p class="font-weight-bold" style="margin:0">'.$specificRecord->trip_id.'</p>
                            </a>
                            <p>'.$specificRecord->loading_site.' <br> '.date('d-m-Y', strtotime($specificRecord->gated_out)).' <br> '.date('h:i A', strtotime($specificRecord->gated_out)).'</p>
                        </td>
                        <td>
                            <span class="text-primary"><b>'.$specificRecord->truck_no.'</b></span>
                            <p style="margin:0"><b>Truck Type</b>: '.$specificRecord->truck_type.' '.($specificRecord->tonnage/1000).'T</p>
                            <p style="margin:0"><b>Transporter</b>: '.$specificRecord->transporter_name.', '.$specificRecord->phone_no.'</p>
                        </td>';
                        
                        $data.='<td>';
                            foreach($tripWaybills as $tripWaybill) {
                                if($specificRecord->id == $tripWaybill->trip_id) {
                                    $data.='<span class="d-block font-weight-sm">'.$tripWaybill->invoice_no.' '.$tripWaybill->sales_order_no.'</a></span>';
                                }
                            }
                        $data.='</td>';

                        $data.='<td>
                            <p class="font-weight-bold" style="margin:0">'.$specificRecord->customers_name.'</p>
                            <p  style="margin:0">Location: '.$specificRecord->exact_location_id.'</p>
                            <p  style="margin:0">Product: '.$specificRecord-> product.'</p>

                        </td>
                    </tr>';
                    }
                } else {
                    $data.='<tr><td colspan="4">No record is available.</td></tr>';
                }
                
                
            $data.='</tbody>
        </thead>
    </table>';



        return $record=[$data, $selectedWeeklyCountRange];
    }


    function specificDateRangeCount($condition, $alias, $start, $finish){
        return DB::SELECT(
            DB::RAW(
                'SELECT '.$condition.' as '.$alias.'  FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$start.'" and "'.$finish.'" and tracker >= 5'
            )
        );
    }

    function specificDateRangeData($start, $finish) {
        $specificDateRecord = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND DATE(gated_out) BETWEEN "'.$start.'" AND "'.$finish.'" ORDER BY a.trip_id DESC'
            )
        );
        return $specificDateRecord;
    }

    public function dailyGateOutRecord(Request $request) {
        $selected_date = $request->selected_date;
        $currentMothAndYear = date('Y-m');
        $user_selected_date = $currentMothAndYear.'-'.$selected_date[0];

        $choosenDate = ltrim(date('dS \of F, Y', strtotime($user_selected_date)), '0');
        
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND DATE(gated_out) = "'.$user_selected_date.'" ORDER BY a.trip_id DESC'
            )
        );

        $waybillTrips = [];
        $tripWaybills = [];
        foreach($trips as $tripsListings) {
            $waybillTrips[] = tripWaybill::WHERE('trip_id', $tripsListings->id)->GET();
        }
        
        foreach($waybillTrips as $myWaybills) {
            foreach($myWaybills as $waybills) {
                $tripWaybills[] = $waybills;
            }
        }
    
        $response = '
        <div class="table-responsive">
            <table class="table table-striped table-hover font-size-sm">
                <thead class="table-success">
                    <tr>
                        <th class="text-center font-weight-bold">SN</th>
                        <th class="text-center font-weight-bold">GATE OUT DETAILS</th>
                        <th class="font-weight-bold">TRUCK</th>
                        <th class="font-weight-bold">WAYBILL DETAILS</th>
                        <th class="font-weight-bold">CONSIGNEE DETAILS</th>
                    </tr>
                <thead>
                <tbody id="searchSpecificDateGateOutData">';
                    if(count($trips)) {
                        $counter = count($trips);
                        foreach($trips as $key => $specificRecord) {
                        $response.='<tr>
                            <td class="font-weight-bold">('.$counter-- .')</td>
                            <td class="text-center">
                                <a href="/trip-overview/'.$specificRecord->trip_id.'">
                                    <p class="font-weight-bold" style="margin:0">'.$specificRecord->trip_id.'</p>
                                </a>
                                <p>'.$specificRecord->loading_site.' <br> '.date('d-m-Y', strtotime($specificRecord->gated_out)).' <br> '.date('h:i A', strtotime($specificRecord->gated_out)).'</p>
                            </td>
                            <td>
                                <span class="text-primary"><b>'.$specificRecord->truck_no.'</b></span>
                                <p style="margin:0"><b>Truck Type</b>: '.$specificRecord->truck_type.' '.$specificRecord->tonnage / 1000 .'</p>
                                <p style="margin:0"><b>Transporter</b>: '.$specificRecord->transporter_name.', '.$specificRecord->phone_no.'</p>
                            </td>
                            <td>';
                            $response.='<span class="mb-2"><a href="assets/img/waybills/'.$tripWaybills[$key]->photo.'" target="_blank" title="View waybill '.$tripWaybills[$key]->sales_order_no.'">'.$tripWaybills[$key]->sales_order_no.'
                            '.$tripWaybills[$key]->invoice_no.'</a></span>   
                            </td>
                            <td>
                                <p class="font-weight-bold" style="margin:0">'.$specificRecord->customers_name.'</p>
                                <p  style="margin:0">Location: '.$specificRecord->exact_location_id.'</p>
                                <p  style="margin:0">Product: '.$specificRecord-> product.'</p>

                            </td>
                        </tr>';
                        }
                    }
                    else {
                        $response.='<tr><td colspan="4">No record is available.</td></tr>';
                    }
                    $response.='</tbody>
                </tbody>
            </table>
        </div>';

        return $choosenDate.'`'.$response;
    }
}


