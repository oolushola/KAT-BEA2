<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\buhMonthlyTarget;
use App\trip;
use Illuminate\Support\Facades\DB;
use App\truckAvailability;


class performanceMetricController extends Controller
{
    public function performanceMetrics(){
        $currentYear = date('Y');
        $currentMonth = date('F');
        $unitHeadTargets = buhMonthlyTarget::WHERE('current_month', $currentMonth)->WHERE('current_year', $currentYear)->GET();

        foreach($unitHeadTargets as $key=> $unitHead) {
            $unitHeadRecord = User::findOrFail($unitHead->user_id);
            $unitHeadInformation[] = $unitHeadRecord->first_name.' '.substr($unitHeadRecord->last_name, 0, 1).'.';

            $unitHeadSpecificTargets[] = $unitHead->target;

            // get the sum total of client rate for the trips done this month by the user
            // get the sum total of transporter rate for the trips done this month by this user

            $myTotalRevenue = trip::WHERE('account_officer_id', $unitHead->user_id)
                ->WHERE('trip_status', 1)
                ->WHERE('tracker', '>=', 5)
                ->WHEREMONTH('gated_out', now())
                ->WHEREYEAR('gated_out', now())
                ->VALUE(DB::RAW("SUM(client_rate)")
            );
            $myTotalTransporterRate = trip::WHERE('account_officer_id', $unitHead->user_id)
                ->WHERE('trip_status', 1)
                ->WHERE('tracker', '>=', 5)
                ->WHEREMONTH('gated_out', now())
                ->WHEREYEAR('gated_out', now())
                ->VALUE(DB::RAW("SUM(transporter_rate)")
            );
             $myGrossMargin[] = $myTotalRevenue - $myTotalTransporterRate;
             $myOutstanding[] = $myGrossMargin[$key] - $unitHeadSpecificTargets[$key];

             if($myGrossMargin[$key] == 0){
                $unitHeadCurrentMarkUp[] = number_format(0, 2);
             }
             else{
                $unitHeadCurrentMarkUp[] = number_format(($myGrossMargin[$key] / $myTotalRevenue * 100), 2);
            }            
        }

        $currentMonthOverview = array('unitHeadInformation' =>  $unitHeadInformation, 'unitHeadSpecificTargets' => $unitHeadSpecificTargets, 'myGrossMargin' => $myGrossMargin, 'myOutstanding' => $myOutstanding, 'unitHeadCurrentMarkUp' => $unitHeadCurrentMarkUp);
    
        return view('performance-metric.master', $currentMonthOverview);
    }

    public function businessUnitHead($roleId, $userIdentity) {
       $userId = base64_decode($userIdentity);
       $userRecord = User::findOrFail($userId);
       $role = $userRecord->role_id;
       if($role >=1 && $role <=3){
            return redirect('performance-metrics');
        } 
        else {
            $currentYear = date('Y');
            $currentMonth = date('F');
            
            $myMonthlyTargetValue = buhMonthlyTarget::SELECT('target')->WHERE('current_year', $currentYear)->WHERE('current_month', $currentMonth)->WHERE('user_id', $userId)->FIRST();

            $myClientRateForTheMonth = $this->sumAndCounter(
                'SUM(client_rate)', 
                'totalMonthlyClientRate', 
                'tbl_kaya_trips', 
                $userId
            );
            $myTransporterRateForTheMonth = $this->sumAndCounter(
                'SUM(transporter_rate)', 
                'totalMonthlyTransporterRate', '
                tbl_kaya_trips', 
                $userId
            );
    
           $mymonthlyProfit = $myClientRateForTheMonth[0]->totalMonthlyClientRate - $myTransporterRateForTheMonth[0]->totalMonthlyTransporterRate;

            $myTotalRevenue = trip::WHERE('account_officer_id', $userId)
                ->WHERE('trip_status', 1)
                ->WHERE('tracker', '>=', 5)
                ->VALUE(DB::RAW("SUM(client_rate)"));
            $myTotalTransporterRate = trip::WHERE('account_officer_id', $userId)
                ->WHERE('trip_status', 1)
                ->WHERE('tracker', '>=', 5)
                ->VALUE(DB::RAW("SUM(transporter_rate)"));
            
            $grossMargin = $myTotalRevenue - $myTotalTransporterRate;

            $currentYearAndMonth = date('Y-m');
            $currentDay = date('d');

            for($i = 1; $i <= $currentDay; $i++){
                $fullDate = $currentYearAndMonth.'-'.$i.',';
                $dailyGateOutForCurrentMonth[] = trip::WHEREDATE('gated_out', $fullDate)->WHERE('account_officer_id', $userId)->WHERE('trip_status', 1)->WHERE('tracker', '>=', 5)->GET()->COUNT();
            }
            
           $notGatedOut = trip::WHERE('tracker', '<', 5)->WHERE('account_officer_id', $userId)->WHERE('trip_status', 1)->GET()->COUNT();

            $pendingPayments = DB::SELECT(
                DB::RAW(
                    'SELECT  a.trip_id as kaid, a.account_officer_id, a.exact_location_id, b.*  FROM tbl_kaya_trips a JOIN tbl_kaya_trip_payments b ON a.id = b.trip_id AND a.account_officer_id = "'.$userId.'" WHERE b.advance_paid = FALSE OR b.balance_paid = false OR b.outstanding_balance != \'\' '
                )
            );

            $totalTripsData = $this->masterQueryData('tracker', '>=', 5, $userId);
            $availableTrucks = $this->availableTrucks();
            $yetTogateOutData = $this->masterQueryData('tracker', '<=', 4, $userId);

            $currentMonthData = DB::SELECT(
                DB::RAW(
                    'SELECT a.trip_id, a.gated_out, a.customers_name, a.customer_no, a.exact_location_id, a.client_rate, a.transporter_rate, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND MONTH(gated_out) = MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE())  AND tracker >= \'5\' AND account_officer_id = "'.$userId.'" ORDER BY gated_out ASC '
                )
            );

            return view('performance-metric.business-unit', 
                array(
                    
                    'buhCurrentMonthTarget' => $myMonthlyTargetValue->target,
                    'gtvForCurrentMonth' => $myClientRateForTheMonth[0]->totalMonthlyClientRate,
                    'trForCurrentMonth' => $myTransporterRateForTheMonth[0]->totalMonthlyTransporterRate,
                    'currentMonthRateDiff' => $mymonthlyProfit,
                    'overallGtv' => $myTotalRevenue,
                    'overAllTr' => $myTotalTransporterRate,
                    'overallDiff' => $grossMargin,
                    'buhNoOfGatePerDayInCurrentMonth' => $dailyGateOutForCurrentMonth,
                    'notGatedOut' => $notGatedOut,
                    'pendingPayments' => $pendingPayments,
                    'availableTrucks' => $availableTrucks,
                    'yetTogateOut' => $yetTogateOutData,
                    'totalTripsData' => $totalTripsData,
                    'currentMonthData' => $currentMonthData,

                )
            );
        }
    }

    public function sumAndCounter($operation, $alias, $tableName, $user){
        $query = DB::SELECT(
            DB::RAW(
                'SELECT '.$operation.' AS '.$alias.' FROM '.$tableName.' WHERE account_officer_id = "'.$user.'" AND MONTH(gated_out) = MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE()) AND trip_status = \'1\' AND tracker >= 5 '
            )
        );
        return $query;
    }

    public function availableTrucks(){
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.loading_site, d.truck_no, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no, g.product, h.state, i.first_name, i.last_name, j.tonnage, j.truck_type FROM tbl_kaya_truck_availabilities a JOIN tbl_kaya_clients b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f JOIN tbl_kaya_products g JOIN tbl_regional_state h JOIN users i JOIN tbl_kaya_truck_types j ON a.client_id = b.id AND a.loading_site_id = c.id AND a.truck_id = d.id AND a.transporter_id = e.id and a.driver_id = f.id and a.product_id = g.id and a.destination_state_id = h.regional_state_id and a.reported_by = i.id AND d.truck_type_id = j.id  WHERE a.status = FALSE'
            )
        );
        return $query;
    } 

    public function masterQueryData($fieldName, $condition, $clause, $user) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.trip_id, a.gated_out, a.customers_name, a.customer_no, a.exact_location_id, a.client_rate, a.transporter_rate, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND  '.$fieldName.' '.$condition.'  "'.$clause.'" AND account_officer_id = "'.$user.'" ORDER BY a.trip_id DESC'
            )
        );
        return $query;
    }

    public function updateClientRate(Request $request) {
        $clientRates = $request->clientRates;
        $tripLists = $request->tripListings;
        foreach($tripLists as $key => $trip_id){
            if(isset($clientRates[$key]) && $clientRates[$key] != "") {
                $tripRecord = trip::WHERE('trip_id', $trip_id)->FIRST();
                $tripRecord->client_rate = $clientRates[$key];
                $tripRecord->save();
            }
        }
        return 'saved';
    }
}
