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

        $lastOneWeek = date('Y-m-d', strtotime('last sunday'));
        $currentDate = date('Y-m-d');

        $noOfGatedOutTripForCurrentWeek = DB::SELECT(
            DB::RAW(
                'select COUNT(*) as weeklygateout  from tbl_kaya_trips where Date(gated_out) between "'.$lastOneWeek.'" and "'.$currentDate.'" and tracker >= 5'
            )
        );

        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        foreach($allLoadingSites as $loadingSite){
            $countDailyTripByLoadingSite[] = trip::WHERE('loading_site_id', $loadingSite->id)->WHEREDATE('gated_out', date('Y-m-d'))->WHERE('tracker', '>=', 5)->GET()->COUNT();
            $loading_sites[] = $loadingSite->loading_site;
        }

        $allclients = client::ORDERBY('company_name', 'ASC')->GET();
        $paymentRequest = tripPayment::WHERE('advance_paid', FALSE)->ORWHERE('balance_paid', FALSE)->GET()->COUNT();
        $clients = client::WHERE('client_status', '1')->GET()->COUNT();

        Session::put([
            'payment_request' => $paymentRequest,
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

        $todaysDate = date('d');
        $count=1;
        $dateYearAndMonth = date('Y-m');
        do{
            $newDate = $dateYearAndMonth.'-'.$count;
            $count++;
            $noOfTripsPerDay[] = trip::whereDATE('gated_out',  $newDate)->GET()->COUNT();
        }
        while($count <= $todaysDate);

        $availableTrucks = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.loading_site, d.truck_no, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no, g.product, h.state, i.first_name, i.last_name, j.tonnage, j.truck_type FROM tbl_kaya_truck_availabilities a JOIN tbl_kaya_clients b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f JOIN tbl_kaya_products g JOIN tbl_regional_state h JOIN users i JOIN tbl_kaya_truck_types j ON a.client_id = b.id AND a.loading_site_id = c.id AND a.truck_id = d.id AND a.transporter_id = e.id and a.driver_id = f.id and a.product_id = g.id and a.destination_state_id = h.regional_state_id and a.reported_by = i.id AND d.truck_type_id = j.id  WHERE a.status = FALSE'
            )
        );


        return view('dashboard', compact('getGatedOutByMonth', 'allTrips', 'monthlyTarget', 'onJourney', 'atDestination', 'offloadedTrips',  'numberofdailygatedout', 'gatedOutForTheMonth', 'countDailyTripByLoadingSite', 'loading_sites', 'noOfGatedOutTripForCurrentWeek', 'loadingBay', 'gateIn', 'allclients', 'departedLoadingBay', 'currentGateOutRecord', 'tripWaybills', 'gateInData', 'atloadingbayData', 'departedLoadingBayData', 'onJourneyData', 'atDestinationData', 'offloadedData', 'tripRecordsForTheMonth', 'totalGateOuts', 'noOfTripsPerDay', 'availableTrucks'));
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
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND MONTH(gated_out) = MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE()) ORDER BY a.trip_id DESC'
            )
        );
        return $query;
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
        
        return [$selectedWeeklyCountRange] = DB::SELECT(
            DB::RAW(
                'SELECT count(gated_out) AS TotalWeekly FROM tbl_kaya_trips WHERE Date(gated_out) BETWEEN  "'.$from.'" AND "'.$to.'" AND tracker >= 5'
            )
        );
    }
}

