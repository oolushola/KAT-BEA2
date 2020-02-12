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

        $monthlyTarget = target::WHERE('current_month', $current_month)->WHERE('current_year', $current_year)->GET();
        $getGatedOutByMonth = trip::WHERE('month', $current_month)->WHERE('year', $current_year)->WHERE('tracker', '>=', 5)->GET()->COUNT();

        $gateIn = trip::WHERE('tracker', 1)->WHERE('trip_status', '!=', 0)->GET()->COUNT();
        $loadingBay = trip::WHERE('tracker', 2)->WHERE('trip_status', '!=', 0)->GET()->COUNT();
        $onJourney = trip::WHERE('tracker', 6)->GET()->COUNT();
        $atDestination = trip::WHERE('tracker', 7)->GET()->COUNT();
        $offloadedTrips = trip::WHERE('tracker', 8)->GET()->COUNT();
        
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

        $offloadedTrips = trip::WHERE('tracker', 8)->GET()->COUNT();


        Session::put([
            'payment_request' => $paymentRequest,
            'on_journey' => $onJourney,
            'client' => $clients,
            'offloaded_trips' => $offloadedTrips
        ]);
        

        return view('dashboard', compact('getGatedOutByMonth', 'allTrips', 'monthlyTarget', 'onJourney', 'atDestination', 'offloadedTrips',  'numberofdailygatedout', 'gatedOutForTheMonth', 'countDailyTripByLoadingSite', 'loading_sites', 'noOfGatedOutTripForCurrentWeek', 'loadingBay', 'gateIn', 'allclients'));
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

