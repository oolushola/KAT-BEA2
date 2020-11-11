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
}
