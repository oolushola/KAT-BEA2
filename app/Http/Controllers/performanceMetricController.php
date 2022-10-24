<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\buhMonthlyTarget;
use App\trip;
use Illuminate\Support\Facades\DB;
use App\truckAvailability;
use App\transporter;
use App\client;
use App\AccountManagerTarget;
use App\transporterDocuments;
use App\BonusAccrued;
use App\expenses;
use App\tripWaybill;
use App\tripIncentives;
use App\Ago;


class performanceMetricController extends Controller
{
    public function performanceMetrics(){
        $currentYear = date('Y');
        $currentMonth = date('F');
        $unitHeadTargets = buhMonthlyTarget::WHERE('current_month', $currentMonth)->WHERE('current_year', $currentYear)->GET();

        $incentives = [];
        $agos = [];
        $sumTotal = 0;
        $sumTotalAgo = 0;
        foreach($unitHeadTargets as $key=> $unitHead) {
            $unitHeadRecord = User::findOrFail($unitHead->user_id);
            $unitHeadInformation[] = $unitHeadRecord->first_name.' '.substr($unitHeadRecord->last_name, 0, 1).'.';
            $unitHeadIds[] = $unitHeadRecord->id;
            $unitHeadSpecificTargets[] = $unitHead->target / 1000000;

            [$myTotalRevenue[]] = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(client_rate) AS totalRevenue FROM tbl_kaya_trips WHERE account_officer_id = "'.$unitHead->user_id.'" AND trip_status = TRUE AND MONTH(gated_out) = '.date('m').' AND YEAR(gated_out) = '.date('Y').''
                )
            );

            $unitHeadTrips[] = DB::SELECT(
                DB::RAW(
                    'SELECT id, trip_id FROM tbl_kaya_trips WHERE account_officer_id = "'.$unitHead->user_id.'" AND trip_status = TRUE AND MONTH(gated_out) = '.date('m').' AND YEAR(gated_out) = '.date('Y').''
                )
            );
            if(count($unitHeadTrips[$key]) > 0) {
                $sumTotal = 0;
                $sumTotalAgo = 0;
                foreach ($unitHeadTrips[$key] as $k=> $trip) {
                    $total = tripIncentives::WHERE('trip_id', $trip->id)->GET()->SUM('amount');
                    $totalAgo = Ago::WHERE('trip_id', $trip->id)->GET()->SUM('amount');
                    $sumTotal += $total;
                    $sumTotalAgo += $totalAgo;
                }
                $incentives[] = $sumTotal;
                $agos[] = $sumTotalAgo;
            }
            else {
                $incentives[] = 0;
                $agos[] = 0;
            }

            $myTotalTransporterRate = trip::WHERE('account_officer_id', $unitHead->user_id)
                ->WHERE('trip_status', 1)
                ->WHERE('tracker', '>=', 5)
                ->WHEREMONTH('gated_out', now())
                ->WHEREYEAR('gated_out', now())
                ->VALUE(DB::RAW("SUM(transporter_rate)")
            );
            
             $myGrossMargin[] = (($myTotalRevenue[$key]->totalRevenue + $incentives[$key] + $agos[$key]) - $myTotalTransporterRate) / 1000000;
             $myOutstanding[] = $myGrossMargin[$key] - $unitHeadSpecificTargets[$key];

             if($myGrossMargin[$key] == 0 || $myTotalRevenue[$key]->totalRevenue == 0){
                $unitHeadCurrentMarkUp[] = number_format(0, 2);
             }
             else{
                $unitHeadCurrentMarkUp[] = number_format(($myGrossMargin[$key] / ($myTotalRevenue[$key]->totalRevenue/1000000) * 100), 2);
            }
            
            $tripCount[] = trip::WHEREMONTH('gated_out', now())->WHEREYEAR('gated_out', now())->WHERE('trip_status', 1)->WHERE('account_officer_id', $unitHead->user_id)->GET()->COUNT();

            [$clientAccountManager[]] = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(target) as target FROM tbl_kaya_account_manager_targets a JOIN tbl_kaya_client_account_manager b ON a.client_id = b.client_id WHERE current_year = '.DATE("Y").' and current_month = '.DATE("m").' AND user_id = "'.$unitHead->user_id.'" '
                )
            );
            $monthlyTripRemainder[] = $clientAccountManager[$key]->target - $tripCount[$key];
            if($monthlyTripRemainder[$key] < 0) {
                $monthlyTripRemainder[$key] = 0;
            }
            else{
                $monthlyTripRemainder[$key] = $monthlyTripRemainder[$key];
            }

            $transportersGained[] = transporter::WHEREYEAR('registration_completed', now())->WHEREMONTH('registration_completed', now())->WHERE('assign_user_id', $unitHead->user_id)->GET()->COUNT();

            $bonuses[] = BonusAccrued::WHERE('user_id', $unitHeadIds[$key])->GET()->SUM('bonus_accrued');
        }

        $clients = client::ORDERBY('client_alias', 'ASC')->SELECT('id', 'company_name', 'client_alias')->WHERE('client_status', "1")->GET();
        $clientTarget = [];
        foreach($clients  as $key => $client) {
            $accountManagerTarget[] = AccountManagerTarget::SELECT('id', 'client_id', 'target')->WHERE('current_year', date('Y'))->WHERE('current_month', date('m'))->WHERE('client_id', $client->id)->GET()->LAST();
            if($accountManagerTarget[$key] == NULL) {
                $clientTarget[] = 0;
            }
            else{
                $clientTarget[] = $accountManagerTarget[$key]->target;
            }
            $clientTripCount[] = trip::WHERE('client_id', $client->id)->WHEREYEAR('gated_out', now())->WHEREMONTH('gated_out', now())->GET()->COUNT();
            $clientNames[] = $client->client_alias;

            $uncompletedTrips[] = $clientTarget[$key] - $clientTripCount[$key];
            if($uncompletedTrips[$key] <= 0) {
                $uncompletedTrips[$key] = 0;
            }
            else {
                $uncompletedTrips[$key] = $uncompletedTrips[$key];
            }
        }

        // Bonus & Earnings Generator!
        
        $currentMonthOverview = array('unitHeadInformation' =>  $unitHeadInformation, 'unitHeadSpecificTargets' => $unitHeadSpecificTargets, 'myGrossMargin' => $myGrossMargin, 'myOutstanding' => $myOutstanding, 'unitHeadCurrentMarkUp' => $unitHeadCurrentMarkUp, 'trip_count' => $tripCount, 'remainingTrip' => $monthlyTripRemainder, 'transporter_gained' => $transportersGained, 'clientNames' => $clientNames, 'tripDoneWithClient' => $clientTripCount, 'pendingTrips' => $uncompletedTrips, 'totalBonus' => $bonuses);
        
        return view('performance-metric.master', $currentMonthOverview);
    }

    public function businessUnitHead($roleId, $userIdentity) {
       $userId = base64_decode($userIdentity);
       $userRecord = User::findOrFail($userId);
       $role = $userRecord->role_id;
        //    if($role == 1 || $role == 3 || $role == 6 ){
        //         return redirect('performance-metrics');
        //     } 
        //else {
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

            $totalIncentive = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(b.amount) AS total_incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trip_incentives b ON a.id = b.trip_id WHERE MONTH(gated_out) = MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE()) AND a.account_officer_id = "'.$userId.'"'
                )
            );

            $gtv = $myClientRateForTheMonth[0]->totalMonthlyClientRate + $totalIncentive[0]->total_incentive;
    
           $mymonthlyProfit = $gtv - $myTransporterRateForTheMonth[0]->totalMonthlyTransporterRate;

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
                    'SELECT a.trip_id, a.id, a.gated_out, a.customers_name, a.customer_no, a.exact_location_id, a.client_rate, a.transporter_rate, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND MONTH(gated_out) = MONTH(CURRENT_DATE()) AND YEAR(gated_out) = YEAR(CURRENT_DATE())  AND tracker >= \'5\' AND account_officer_id = "'.$userId.'" ORDER BY gated_out ASC '
                )
            );
            
            $accountOfficerTrucks = DB::SELECT(
                DB::RAW('SELECT id, truck_no FROM tbl_kaya_trucks WHERE id IN (SELECT DISTINCT truck_id from tbl_kaya_trips WHERE account_officer_id = "'.$userId.'") ORDER BY truck_no ASC')
            );

            $waybillListings = [];
            $waybillDetails = [];
            foreach($currentMonthData as $key => $trips) {
                $waybillInfo = tripWaybill::SELECT('trip_id', 'sales_order_no', 'invoice_no')->WHERE('trip_id', $trips->id)->GET();
                if(isset($waybillInfo) && count($waybillInfo) > 0) {
                    [$waybillDetails[]] = tripWaybill::WHERE('trip_id', $trips->id)->GET();
                }
            }
            
            return view('performance-metric.business-unit', 
                array(
                    
                    'buhCurrentMonthTarget' => $myMonthlyTargetValue->target,
                    'gtvForCurrentMonth' => $gtv,
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
                    'waybillDetails' => $waybillDetails,
                    'accountOfficerTrucks' => $accountOfficerTrucks

                )
            );
        //}
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
                if($tripRecord->client_id == 1) {
                    $transporterRate = $clientRates[$key] * 0.9;
                    $tripRecord->transporter_rate = $transporterRate;
                }
                $tripRecord->save();
            }
        }
        return 'saved';
    }

    public function updateTransporterRate(Request $request) {
        $transporterRates = $request->transporterRates;
        $tripIds = $request->tripListings;
        foreach($tripIds as $key => $trip_id) {
            if(isset($transporterRates[$key]) && $transporterRates[$key] != '') {
                $trip = trip::WHERE('trip_id', $trip_id)->FIRST();
                $trip->transporter_rate = $transporterRates[$key];
                $trip->save();
            }
        }
        return 'saved';
    }

    public function filterPerformanceMetrics(Request $request) {
        $currentYear = $request->current_year;
        $currentMonth = $request->current_month;
        $selectedMonth = $this->monthGetter($currentMonth);
        $unitHeadTargets = buhMonthlyTarget::WHERE('current_month', $currentMonth)->WHERE('current_year', $currentYear)->GET();

        if(count($unitHeadTargets) <= 0) 
        {
            return 'NoTarget';
        }
        else 
        {
            foreach($unitHeadTargets as $key=> $unitHead) {
                $unitHeadRecord = User::findOrFail($unitHead->user_id);
                $unitHeadInformation[] = $unitHeadRecord->first_name.' '.substr($unitHeadRecord->last_name, 0, 1).'.';
                $unitHeadSpecificTargets[] = $unitHead->target / 1000000;
                $unitHeadIds[] = $unitHeadRecord->id;
                
                $myTotalRevenue_ = trip::WHERE('account_officer_id', $unitHead->user_id)->WHERE('trip_status', 1)->WHERE('tracker', '>=', 5)->WHEREMONTH('gated_out', $selectedMonth)->WHEREYEAR('gated_out', $currentYear)->VALUE(DB::RAW("SUM(client_rate)"));
                $mnt = $this->monthGetter($currentMonth);
                [$myIncentives[]] = DB::SELECT(DB::RAW('SELECT SUM(amount) as totalIncentives FROM tbl_kaya_trips a JOIN tbl_kaya_trip_incentives b ON a.id = b.trip_id WHERE account_officer_id = "'.$unitHead->user_id.'" AND trip_status = TRUE AND tracker >= 5 AND MONTH(gated_out) = "'.$mnt.'" AND YEAR(gated_out) = "'.$currentYear.'"' ));
                [$myAgos[]] = DB::SELECT(DB::RAW('SELECT SUM(amount) as totalAgos FROM tbl_kaya_trips a JOIN tbl_kaya_agos b ON a.id = b.trip_id WHERE account_officer_id = "'.$unitHead->user_id.'" AND trip_status = TRUE AND tracker >= 5 AND MONTH(gated_out) = "'.$mnt.'" AND YEAR(gated_out) = "'.$currentYear.'"' ));
                
                $myTotalTransporterRate = trip::WHERE('account_officer_id', $unitHead->user_id)
                    ->WHERE('trip_status', 1)
                    ->WHERE('tracker', '>=', 5)
                    ->WHEREMONTH('gated_out', $selectedMonth)
                    ->WHEREYEAR('gated_out', $currentYear)
                    ->VALUE(DB::RAW("SUM(transporter_rate)")
                );
                $myTotalRevenue = $myTotalRevenue_ + $myIncentives[$key]->totalIncentives + $myAgos[$key]->totalAgos;
                $myGrossMargin[] = ($myTotalRevenue - $myTotalTransporterRate) / 1000000;
                $myOutstanding[] = $myGrossMargin[$key] - $unitHeadSpecificTargets[$key];

                if($myGrossMargin[$key] == 0 || $myTotalRevenue == 0){
                    $unitHeadCurrentMarkUp[] = number_format(0, 2);
                }
                else{
                    $unitHeadCurrentMarkUp[] = number_format(($myGrossMargin[$key] / ($myTotalRevenue/1000000) * 100), 2);
                }
                
                $tripCount[] = trip::WHEREMONTH('gated_out', $selectedMonth)->WHEREYEAR('gated_out', $currentYear)->WHERE('trip_status', 1)->WHERE('account_officer_id', $unitHead->user_id)->GET()->COUNT();

                [$clientAccountManager[]] = DB::SELECT(
                    DB::RAW(
                        'SELECT SUM(target) as target FROM tbl_kaya_account_manager_targets a JOIN tbl_kaya_client_account_manager b ON a.client_id = b.client_id WHERE current_year = '.$currentYear.' and current_month = '.$selectedMonth.' AND user_id = "'.$unitHead->user_id.'" '
                    )
                );
                $monthlyTripRemainder[] = $clientAccountManager[$key]->target - $tripCount[$key];
                if($monthlyTripRemainder[$key] <= 0) {
                    $monthlyTripRemainder[$key] = 0;
                }
                else{
                    $monthlyTripRemainder[$key] = $monthlyTripRemainder[$key];
                }
    
                $transportersGained[] = transporter::WHEREYEAR('registration_completed', $currentYear)->WHEREMONTH('registration_completed', $selectedMonth)->WHERE('assign_user_id', $unitHead->user_id)->GET()->COUNT();
            } 
            
            //return $myAgos;
            
            $clients = client::ORDERBY('client_alias', 'ASC')->SELECT('id', 'company_name', 'client_alias')->WHERE('client_status', "1")->GET();
            $clientTarget = [];
            foreach($clients  as $key => $client) {
                $accountManagerTarget[] = AccountManagerTarget::SELECT('id', 'client_id', 'target')->WHERE('current_year', $currentYear)->WHERE('current_month', $selectedMonth)->WHERE('client_id', $client->id)->GET()->LAST();
                if($accountManagerTarget[$key] == NULL) {
                    $clientTarget[] = 0;
                }
                else{
                    $clientTarget[] = $accountManagerTarget[$key]->target;
                }
                $clientTripCount[] = trip::WHERE('client_id', $client->id)->WHEREYEAR('gated_out', $currentYear)->WHEREMONTH('gated_out', $selectedMonth)->GET()->COUNT();
                $clientNames[] = $client->client_alias;

                $uncompletedTrips[] = $clientTarget[$key] - $clientTripCount[$key];
                if($uncompletedTrips[$key] <= 0) {
                    $uncompletedTrips[$key] = 0;
                }
                else {
                    $uncompletedTrips[$key] = $uncompletedTrips[$key];
                }
            }
            // Bonus & Earnings Generator!
            foreach($unitHeadIds as $key => $assignedUserId) {
                [$trips[]] = DB::SELECT(
                    DB::RAW(
                        'SELECT COUNT(*) as noOfTrips, SUM(client_rate) as totalClientRate, SUM(transporter_rate) as totalTransporterRate, SUM(client_rate - transporter_rate) as profitGenerated  FROM tbl_kaya_trips a JOIN tbl_kaya_client_account_manager b ON a.client_id = b.client_id LEFT JOIN tbl_kaya_trip_incentives c ON a.id = c.trip_id WHERE trip_status = TRUE AND YEAR(gated_out) = '.$currentYear.' AND account_officer_id = '.$assignedUserId.''
                    )
                );
                [$clientAssigned[]] = DB::SELECT(
                    DB::RAW(
                        'SELECT COUNT(client_id) AS assignedClientId FROM tbl_kaya_client_account_manager WHERE user_id = '.$assignedUserId.''
                    )
                );
                $x_profitGenerated[] = 0.1 * $trips[$key]->profitGenerated;
                if($clientAssigned[$key]->assignedClientId <= 0) {
                    $averageWeight[] = 0;
                }
                else{
                    $averageWeight[] = 100 / $clientAssigned[$key]->assignedClientId;
                }
                $weightAverageBonus[] = round(($averageWeight[$key] / 100) * $x_profitGenerated[$key] / 1000, 2);
            }
            
    
            $currentMonthOverview = array(
                'unitHeadInformation' =>  $unitHeadInformation, 
                'unitHeadSpecificTargets' => $unitHeadSpecificTargets, 
                'myGrossMargin' => $myGrossMargin, 
                'myOutstanding' => $myOutstanding, 
                'unitHeadCurrentMarkUp' => $unitHeadCurrentMarkUp, 
                'tripCount' => $tripCount,
                'selectedMonth' => $currentMonth.', '.$currentYear,
                'monthlyTripRemainder' => $monthlyTripRemainder,
                'transportersGained' => $transportersGained,
                'nameOfClient' => $clientNames,
                'tripRemainder' => $uncompletedTrips,
                'tripDoneForClient' => $clientTripCount,
                'totalBonus' => $weightAverageBonus

,            );
            return $currentMonthOverview;
        }
    }

    public function specificBuhPerformance(Request $request) {
        $user = explode(' ', $request->user);
        $firstName = $user[0];
        $year = $request->year;
        $month = $request->month;
        $userRecord = User::WHERE('first_name', $firstName)->GET()->FIRST();
        $selectedMonth = $this->monthGetter($month);
        $userId = $userRecord->id;
        
        $myMonthlyTargetValue = buhMonthlyTarget::SELECT('target')->WHERE('current_year', $year)->WHERE('current_month', $month)->WHERE('user_id', $userId)->FIRST();

        $choosenDateRevenueAndCost = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate + IFNULL(b.amount, 0)) AS revenueForTheMonth, SUM(transporter_rate) AS totalTrFortheMonth FROM tbl_kaya_trips a LEFT JOIN tbl_kaya_trip_incentives b ON a.id = b.trip_id WHERE account_officer_id = "'.$userId.'" AND MONTH(gated_out) = "'.$selectedMonth.'" AND YEAR(gated_out) = "'.$year.'" AND trip_status = \'1\' AND tracker >= 5 '
            )
        );
            
        $currentYearAndMonth = $year.'-'.$selectedMonth;

        if(date('Y') == $year && date('m') == $selectedMonth) {
            $numberOfDays = date('d');
        } else{
            $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $year);
        }

        for($i = 1; $i <= $numberOfDays; $i++){
            $fullDate = $currentYearAndMonth.'-'.$i;
            $dailyGateOutForCurrentMonth[] = trip::WHEREDATE('gated_out', $fullDate)->WHERE('account_officer_id', $userId)->WHERE('trip_status', 1)->WHERE('tracker', '>=', 5)->GET()->COUNT();
            
            $daysIntheMonth[] = $i.date("S-", mktime(0, 0, 0, 0, $i, 0)).$this->monthShortName($month);
        }

        $yetTogateOutData = $this->masterQueryData('tracker', '<=', 4, $userId);            
        $selectedMonthAndYearData = DB::SELECT(
            DB::RAW(
                'SELECT a.trip_id, a.gated_out, a.customers_name, a.customer_no, a.exact_location_id, a.client_rate, a.transporter_rate, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, IFNULL(h.amount, 0) AS incentive FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id LEFT JOIN tbl_kaya_trip_incentives h ON a.id = h.trip_id WHERE a.trip_status = \'1\' AND MONTH(gated_out) = "'.$selectedMonth.'" AND YEAR(gated_out) = "'.$year.'" AND tracker >= \'5\' AND account_officer_id = "'.$userId.'" ORDER BY gated_out ASC'
            )
        );

        $totalTripsData = count($this->masterQueryData('tracker', '>=', 5, $userId));

        $myMonthAndYearData = '
            <div class="mb-2">
                <input type="text" placeholder="Search" style="outline:none; font-size:11px; width:150px; padding:5px" id="searchPreviousTripsOfSelectedDate" />
                <input type="date" id="drFrom" style="outline:none; font-size:11px; width:150px; padding:5px; width:130px;" />
                <input type="date" id="drTo" style="outline:none; font-size:11px; width:150px; padding:5px; width:130px;" /> 
                <button id="filterDateRange" class="btn btn-primary font-size-xs font-weight-bold btn-sm">GO!</button>
                <input type="hidden" id="userSelectedId" value="'.$userId.'" />
            </div>

            <div class="table-responsive" id="tripsDateRangeFilter">
                <table class="table table-condensed">
                    <tr class="table-success">
                        <td colspan="7" class="font-weight-semibold">Total number of trips: ('.count($selectedMonthAndYearData).')</td>
                    </tr>
                    <tr class="table-info">
                        <th class="font-size-xs text-center">SN</th>
                        <th class="font-size-xs text-center">TRIP INFO</th>
                        <th class="font-size-xs text-center">TRANSPORTER</th>
                        <th class="font-size-xs text-center">GTV</th>
                        <th class="font-size-xs text-center">TR</th>
                        <th class="font-size-xs text-center">MARGIN</th>
                    </tr>
                    <tbody id="selectedDateDataRecord">';
                    $count = 1;

                    if(count($selectedMonthAndYearData)) {
                        foreach($selectedMonthAndYearData as $trips) {
                            $rate = $trips->client_rate + $trips->incentive;
                            if($count % 2 == 1) { $css = "table-success"; } else { $css = " "; }
                            $myMonthAndYearData.='<tr class="font-size-xs '.$css.'">
                                <td class="text-center">'.$count++.'</td>
                                <td class="text-center">'.$trips->trip_id.' 
                                    <span class="font-weight-semibold d-block">'.$trips->loading_site.'</span>
                                    <span class="d-block">'.date('d-m-Y', strtotime($trips->gated_out)).'</span>
                                </td>
                                <td class="text-center">'.$trips->transporter_name.' 
                                    <span class="font-weight-semibold d-block">'.$trips->phone_no.'</span>
                                </td>
                                <td class="text-center">₦'.number_format($rate, 2).'</td>
                                <td class="text-center">₦'.number_format($trips->transporter_rate, 2).'</td>
                                <td class="text-center">₦'.number_format($rate - $trips->transporter_rate, 2).'</td>
                            </tr>';
                        }
                    }
                    else{
                        $myMonthAndYearData.='<tr>
                            <td colspan="9" class="font-size-sm">They have no trip for this selected month.</td>
                        </tr>';
                    }
                $myMonthAndYearData.='</tbody>
                </table>
            </div>
        ';

        $revenueGenerated = $choosenDateRevenueAndCost[0]->revenueForTheMonth;
        $transporterRate = $choosenDateRevenueAndCost[0]->totalTrFortheMonth;

        $gtv_ = $revenueGenerated;
        $profitGenerated = $gtv_ - $transporterRate;

        $percentageProfit = number_format(($profitGenerated / $myMonthlyTargetValue->target) * 100, 2);
        $target = number_format($myMonthlyTargetValue->target, 2);

        $grossMargin = $choosenDateRevenueAndCost[0]->revenueForTheMonth - $choosenDateRevenueAndCost[0]->totalTrFortheMonth;
        if($choosenDateRevenueAndCost[0]->revenueForTheMonth > 0) { 
        $percentageMarkUp = ($grossMargin / $choosenDateRevenueAndCost[0]->revenueForTheMonth) * 100;
        }
        else{
            $percentageMarkUp = 0;
        }

        if($percentageProfit < 0){ $ratings = 0; $stars = ''; $remark = 'Worrisome'; }
        else if($percentageProfit >= 0 && $percentageProfit <= 9) { $ratings = 0; $stars = ''; $remark = 'Too Bad'; }
        elseif($percentageProfit >= 10 && $percentageProfit <= 19.9) {
            $ratings = 1; $stars = '<i class="icon-star-full2"></i>'; $remark = 'Too Bad';
        }
        elseif($percentageProfit >= 20 && $percentageProfit <= 39.9){
            $ratings = 2; $stars = '<i class="icon-star-full2"></i><i class="icon-star-full2"></i>'; $remark = 'Fair';
        }
        elseif($percentageProfit >= 40 && $percentageProfit <= 59.9){
            $ratings = 3; $stars = '<i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i>';
            $remark = 'Good';
        }
        elseif($percentageProfit >= 60 && $percentageProfit <= 79.9){
            $ratings = 4;
            $stars = '<i class="icon-star-full2"></i> <i class="icon-star-full2"></i> <i class="icon-star-full2"></i> <i class="icon-star-full2"></i>';
            $remark = 'Impressive';
        }
        elseif($percentageProfit >=80 && $percentageProfit <=100){
            $ratings = 5;
            $stars = '<i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i>';
            $remark = 'Goal Getter';
        }
        else{
            $ratings = '<i class="icon-trophy3"></i>';
            $stars = '<i class="icon-medal"></i> <i class="icon-medal"></i> <i class="icon-medal"></i> <i class="icon-medal"></i> <i class="icon-medal"></i>';
            $remark = 'Golden Buzzer';
        }

        $ratingsChart ='<div class="dashboardbox">
                <p class="text-center font-weight-bold text-primary">'.$stars.'</p>

                <h1 class="mt-4 text-center text-primary font-weight-bold">OVERALL RATING</h1>

                <h2 class="mt-4 text-center font-weight-bold text-warning">'.$ratings.' of 5</h2>

                <h5 class="mt-4 text-center font-weight-bold text-warning">REMARK</h5>
                <h1 class="font-weight-bold text-center text-danger ">'.$remark.'</h1>
            </div>';
            
        $yetTogateOutData = $this->masterQueryData('tracker', '<=', 4, $userId);

        $tripsYetToGateOut = '
            <div class="table-responsive">
                <table class="table table-condensed">
                    <tr class="table-success">
                        <td colspan="3" class="font-weight-semibold">Total no of gate out: '.$totalTripsData.'<td>
                        <td colspan="2" class="font-weight-semibold">Yet to gate trips ('.count($yetTogateOutData).')</td>
                    </tr>
                    <tr class="table-info">
                        <th class="font-size-xs text-center">SN</th>
                        <th class="font-size-xs text-center">TRIP INFO</th>
                        <th class="font-size-xs text-center">TRANSPORTER</th>
                        <th class="font-size-xs text-center">TRUCK</th>
                        <th class="font-size-xs text-center">DESTINATION</th>
                    </tr>';
                    $counter = 1;
                    if(count($yetTogateOutData)) {
                        foreach($yetTogateOutData as $ytg) {
                            if($counter % 2 == 1) { $css = "table-success"; } else { $css = " "; }
                            $tripsYetToGateOut.='<tr class="font-size-xs '.$css.'">
                                <td class="text-center">'.$counter++.'</td>
                                <td class="text-center">'.$ytg->trip_id.'
                                    <span class="font-weight-semibold d-block">'.$ytg->loading_site.'</span>
                                </td>
                                <td class="text-center">'.$ytg->transporter_name.' 
                                    <span class="font-weight-semibold d-block">'.$ytg->phone_no.'</span>
                                </td>
                                <td class="text-center">'.$ytg->truck_no.' 
                                    <span class="font-weight-semibold d-block">'.$ytg->truck_type.' '.$ytg->tonnage/1000 .'T</span>
                                </td>
                                <td class="text-center">'.$ytg->exact_location_id.'</td>
                            </tr>';
                        }
                    }
                    else{
                        $tripsYetToGateOut.='<tr>
                            <td colspan="9" class="font-size-sm font-weight-semibold">They currently do not have any truck in any of kaya partner premises.</td>
                        </tr>';
                    }
                $tripsYetToGateOut.='</table>
            </div>
        ';

        $performanceReview = array(
            'fullName' => $userRecord->first_name.' '.$userRecord->last_name,
            'selectedMonthData' => $myMonthAndYearData,
            'daysIntheMonth' => $daysIntheMonth,
            'dailyGateOutForCurrentMonth' => $dailyGateOutForCurrentMonth,
            'selectedMonthTarget' => $myMonthlyTargetValue,
            'profitGenerated' => $profitGenerated,
            'revenueAndCost' => $choosenDateRevenueAndCost,
            'revenueGenerated' => number_format($gtv_, 2),
            'transporterRate' => number_format($transporterRate, 2),
            'percentageProfit' => $percentageProfit,
            'target' => $target,
            'ratingsChart' => $ratingsChart,
            'percentageMarkUp' => number_format($percentageMarkUp, 2),
            'tripsYetToGateOut' => $tripsYetToGateOut,
        );
        return $performanceReview;
    }

    public function transporterGained(Request $request) {
        $user = explode(' ', $request->user);
        $firstName = $user[0];
        $year = $request->year;
        $month = $request->month;
        $userRecord = User::WHERE('first_name', $firstName)->GET()->FIRST();
        $selectedMonth = $this->monthGetter($month);
        $userId = $userRecord->id;

        $transporters = transporter::WHERE('assign_user_id', $userId)->WHEREMONTH('registration_completed', $selectedMonth)->WHEREYEAR('registration_completed', $year)->GET();
        
        if(count($transporters) > 0) {
            $counter = 0;
            $response = '';
            foreach($transporters as $key=> $transporter) {
                $response.= '<div class="col-md-6 col-sm-12 mb-2">
                    <div style="max-height:350px; overflow:auto;">';
                        $response.='<div class="dashboardbox">
                            <h4 class="text-primary m-0 p-0">'.$transporter->transporter_name.'</h4>
                            <p class="p-0 m-0">'.$transporter->phone_no.'</p>
                            <p style="p-0 m-0">'.$transporter->address.'</p>
                            <p style="text-decoration:underline" class="text-primary">Documents</p>';
                            $documents = transporterDocuments::WHERE('transporter_id', $transporter->id)->GET();
                            if(count($documents)) {
                                foreach($documents as $document) {
                                    $response.='
                                        <a href="/assets/img/transporters/documents/'.$document->document.'" target="_blank" title="'.$document->description.'">
                                            <i class="icon-file-check2"></i>
                                        </a>
                                    ';
                                }
                            }
                            else{
                                $response.='<span class="text-danger font-weight-semibold">No document was uploaded.</span>';
                            }
                        $response.='</div>';
                    $response.='
                    </div>
                </div>';
            }
            return $response;
        }
        else{
            echo 'Something went wrong';
        }
        
    }

    public function performanceAnalysis(Request $request) {
        $dateFrom = $request->datedFrom;
        $dateTo = $request->datedTo;
        $currentYear = date('Y');
        $currentMonth = date('F');
        $unitHeadTargets = buhMonthlyTarget::WHERE('current_month', $currentMonth)->WHERE('current_year', $currentYear)->GET();
        $count = 0;
        $response = '';
        foreach($unitHeadTargets as $key=> $unitHead) {
            $unitHeadRecord = User::findOrFail($unitHead->user_id);
            $unitHeadInformation[] = $unitHeadRecord->first_name.' '.substr($unitHeadRecord->last_name, 0, 1).'.';
            $count++;

            $breakDowns[] = DB::SELECT(
                DB::RAW(
                    'SELECT DISTINCT client_id, COUNT(client_id) as trips_done, company_name FROM tbl_kaya_trips a JOIN tbl_kaya_clients b ON a.client_id = b.id WHERE date(gated_out) BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'" AND account_officer_id = "'.$unitHead->user_id.'" AND tracker >= 5 AND trip_status = TRUE AND client_id GROUP BY client_id'
                )
            );
            $response.= '<p class="mt-1 mb-1 p-2 font-weight-bold">'.$unitHeadInformation[$key].'';
                if(count($breakDowns[$key]) > 0) {
                    foreach($breakDowns[$key] as $tripBd) {
                        $response.='<span class="badge badge-primary mr-1">'.$tripBd->company_name.' ('.$tripBd->trips_done.')</span>';
                    }
                }
            $response.='</p>';
        }
        return $response;        
    }

    public function tripsBreakDown(Request $request) {
        $userId = $request->user_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
    
        $breakDowns = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT client_id, COUNT(client_id) as trips_done, company_name FROM tbl_kaya_trips a JOIN tbl_kaya_clients b ON a.client_id = b.id WHERE date(gated_out) BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'" AND account_officer_id = "'.$userId.'" AND tracker >= 5 AND trip_status = TRUE AND client_id GROUP BY client_id'
            )
        );

        $filteredDate = DB::SELECT(
            DB::RAW(
                'SELECT a.trip_id, a.gated_out, a.customers_name, a.customer_no, a.exact_location_id, a.client_rate, a.transporter_rate, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND date(gated_out) BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'" AND tracker >= \'5\' AND account_officer_id = "'.$userId.'" ORDER BY gated_out ASC '
            )
        );

        $totalTripsData = count($this->masterQueryData('tracker', '>=', 5, $userId));

        $filtered = '
        <div class="table-responsive" id="tripsDateRangeFilter">
            <table class="table table-condensed">
                <tr class="table-success">
                    <td colspan="7" class="font-weight-semibold">Total number of trips: ('.count($filteredDate).')</td>
                </tr>
                <tr>
                    <td colspan="7">';
                        foreach($breakDowns as $tripBd) {
                            $filtered.='<span class="badge badge-primary mr-2">'.$tripBd->company_name.' ('.$tripBd->trips_done.')</span>';
                        }
                    $filtered.'</td>
                </tr>
                <tr class="table-info">
                    <th class="font-size-xs text-center">SN</th>
                    <th class="font-size-xs text-center">TRIP INFO</th>
                    <th class="font-size-xs text-center">TRANSPORTER</th>
                    <th class="font-size-xs text-center">GTV</th>
                    <th class="font-size-xs text-center">TR</th>
                    <th class="font-size-xs text-center">MARGIN</th>
                </tr>
                <tbody id="selectedDateDataRecord">';
                $count = 1;
                if(count($filteredDate)) {
                    foreach($filteredDate as $trips) {
                        if($count % 2 == 1) { $css = "table-success"; } else { $css = " "; }
                        $filtered.='<tr class="font-size-xs '.$css.'">
                            <td class="text-center">'.$count++.'</td>
                            <td class="text-center">'.$trips->trip_id.' 
                                <span class="font-weight-semibold d-block">'.$trips->loading_site.'</span>
                                <span class="d-block">'.date('d-m-Y', strtotime($trips->gated_out)).'</span>
                            </td>
                            <td class="text-center">'.$trips->transporter_name.' 
                                <span class="font-weight-semibold d-block">'.$trips->phone_no.'</span>
                            </td>
                            <td class="text-center">₦'.number_format($trips->client_rate, 2).'</td>
                            <td class="text-center">₦'.number_format($trips->transporter_rate, 2).'</td>
                            <td class="text-center">₦'.number_format($trips->client_rate - $trips->transporter_rate, 2).'</td>
                        </tr>';
                    }
                }
                else{
                    $filtered.='<tr>
                        <td colspan="9" class="font-size-sm">They have no trip for this selected date range.</td>
                    </tr>';
                }
            $filtered.='</tbody>
            </table>
        </div>
        ';

        return $filtered;
    }

    function amountGenerated($currentYear, $currentMonth, $clientId, $accountOfficer) {
        $moneyGenerated = DB::SELECT(
            DB::RAW(
                'SELECT  SUM(client_rate) AS revenue, SUM(IFNULL(amount, 0)) AS incentive, SUM(transporter_rate) AS transporterRate, (SUM(client_rate) + SUM(IFNULL(amount,0)) - SUM(transporter_rate)) as marginGenerated  FROM tbl_kaya_trips a LEFT JOIN tbl_kaya_trip_incentives b ON a.id = b.trip_id WHERE trip_status = TRUE AND tracker >= 5 AND client_id = "'.$clientId.'" AND YEAR(gated_out) = "'.$currentYear.'" AND MONTH(gated_out) = "'.$currentMonth.'" AND a.account_officer_id = "'.$accountOfficer.'"'
            )
        );
        return $moneyGenerated;
    }

    public function getBonusBreakDown(Request $request) {
        //print_r($moneyGenerated)
        $currentYear = date('Y');
        $currentMonth = date('m');
        $user = explode(' ', $request->user);
        $firstName = $user[0];
        $userRecord = User::WHERE('first_name', $firstName)->GET()->FIRST();
        $userRecord = User::WHERE('first_name', $firstName)->GET()->FIRST();
        $userId = $userRecord->id;
        
        [$revenueGenerated] = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) AS totalRevenue, SUM(transporter_rate) AS transporterRate, SUM(IFNULL(amount, 0)) AS totalAddOn FROM tbl_kaya_trips a LEFT JOIN tbl_kaya_trip_incentives b ON a.id = b.trip_id WHERE YEAR(gated_out) = "'.$currentYear.'" AND MONTH(gated_out) = "'.$currentMonth.'" AND a.tracker >= 5 AND trip_status = TRUE'
            )
        );
        $profitGenerated = ($revenueGenerated->totalRevenue - $revenueGenerated->transporterRate) + $revenueGenerated->totalAddOn;
        $opex = expenses::WHERE('year', $currentYear)->WHERE('month', (int)$currentMonth)->GET()->FIRST();
        $currentMonthOpex = $opex->opex;
        $currentMonthNetMargin_ = $profitGenerated - $currentMonthOpex;
        if($currentMonthNetMargin_ > 0) {
            $currentMonthNetMargin = $currentMonthNetMargin_;
        }
        else {
            $currentMonthNetMargin = 0;
        }
        $netBonus = 0.05 * $currentMonthNetMargin;
        $supposedBonus = 0;

        $dealsDone = DB::SELECT(
            DB::RAW(
                'SELECT a.percentage, a.business_value, a.client_id, b.user_id, company_name, client_alias, @targetPercentage := ((percentage/100) * a.business_value) as targetPercentage, @threshold := (0.5 * @targetPercentage) as threshold FROM tbl_kaya_account_manager_targets a JOIN tbl_kaya_client_account_manager b ON a.client_id = b.client_id LEFT JOIN tbl_kaya_clients c ON a.client_id = c.id WHERE current_year = "'.$currentYear.'" AND current_month = "'.$currentMonth.'" AND user_id ="'.$userId.'"'
            )
        );

        $sumOfGeneratedValue_ = 0;
        $generatedValue_ = [];
        foreach($dealsDone as $key => $dealings) {
            $marginGenerated_ = $this->amountGenerated($currentYear, $currentMonth, $dealings->client_id, $userId);
            $generatedValue_ = $marginGenerated_[0]->marginGenerated;
            $sumOfGeneratedValue_ +=  $generatedValue_; 
        }
        
        if($profitGenerated > 0) {
            $supposedBonus = ($sumOfGeneratedValue_ / $profitGenerated) * $netBonus;
        }
        else {
            $supposedBonus = 0;
        }

        $log = '
            <div class="row table-responsive">
                <table class="table table-condensed">
                    <thead class="font-size-xs font-weight-bold">
                        <tr class="font-size-lg table-success text-success">
                            <th colspan="2">Profit Generated: &#8358;'.number_format($profitGenerated, 2).'</th>                            
                            <th colspan="2">Operating Expenses: &#8358;'.number_format($currentMonthOpex, 2).'</th>
                            <th colspan="2">Net Margin: &#8358;'.number_format($currentMonthNetMargin, 2).'</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr class="text-center">
                            <th>CLIENT</th>
                            <th>ALLOTED WEIGHT(%)</th>
                            <th>EXPECTED MARGIN(&#8358;)</th>
                            <th>THRESHOLD(&#8358;)</th>
                            <th>MARGIN(&#8358;)</th>
                            <th>STATUS</th>
                            <th>BONUS(&#8358;)</th>
                        </tr>
                    </thead>
                    <tbody>';
                    if(count($dealsDone) > 0) {
                        $generatedValue = [];
                        $sumOfAccruedBonuses = 0;
                        foreach($dealsDone as $key => $dealings) {
                            $marginGenerated = $this->amountGenerated($currentYear, $currentMonth, $dealings->client_id, $userId);
                            $generatedValue = $marginGenerated[0]->marginGenerated;    
                            if($generatedValue >= $dealings->threshold) {
                                $statusCheck = '<i class="icon-checkmark4 text-primary" title="Passed"></i>';
                                $bonusAccrued = $dealings->percentage / 100 * $supposedBonus;
                            }
                            else {
                                $statusCheck = '<i class="icon-cancel-circle2 text-danger" title="Not Passed"></i>';
                                $bonusAccrued = 0;
                            }
                            $sumOfAccruedBonuses += $bonusAccrued;
                            $log.='
                                <tr class="text-center">
                                    <td>'.$dealings->client_alias.'</td>
                                    <td>'.$dealings->percentage.'</td>
                                    <td>'.number_format($dealings->targetPercentage, 2).'</td>
                                    <td>'.number_format($dealings->threshold, 2).'</td>
                                    <td>'.number_format($generatedValue, 2).'</td>
                                    <td>'.$statusCheck.'</td>
                                    <td class="bg-info">'.number_format($bonusAccrued, 2).'</td>
                                </tr>
                            ';
                        }
                        $log.='
                            <tr class="text-center font-weight-bold bg-info">
                                <td>Net Bonus:</td>
                                <td>&#8358;'.number_format($netBonus, 2).'</td>
                                <td>Supposed Bonus:</td>
                                <td>&#8358;'.number_format($supposedBonus, 2).'</td>
                                <td>Total Bonus Accrued:</td>
                                <td colspan="2" class="bg-success">&#8358;'.number_format($sumOfAccruedBonuses, 2).'</td>
                            </tr>';
                    }

                    else {
                        $log.='<tr>
                            <td colspan="5">No businesses were assigned to this account</td>
                        </tr>';
                    }
                    
        $log.='
                </tbody>
            </table>
        </div>';

        return $log;
        
        


    }

    function monthGetter($month) {
        if($month == 'January') { $selectedMonth = '01'; }
        if($month == 'February') { $selectedMonth = '02'; }
        if($month == 'March') { $selectedMonth = '03'; }
        if($month == 'April') { $selectedMonth = '04'; }
        if($month == 'May') { $selectedMonth = '05'; }
        if($month == 'June') { $selectedMonth = '06'; }
        if($month == 'July') { $selectedMonth = '07'; }
        if($month == 'August') { $selectedMonth = '08'; }
        if($month == 'September') { $selectedMonth = '09'; }
        if($month == 'October') { $selectedMonth = '10'; }
        if($month == 'November') { $selectedMonth = '11'; }
        if($month == 'December') { $selectedMonth = '12'; }

        return $selectedMonth;
    }

    function monthShortName($month) {
        if($month == 'January') { $shortMonth = 'Jan'; }
        if($month == 'February') { $shortMonth = 'Feb'; }
        if($month == 'March') { $shortMonth = 'Mar'; }
        if($month == 'April') { $shortMonth = 'Apr'; }
        if($month == 'May') { $shortMonth = 'May'; }
        if($month == 'June') { $shortMonth = 'Jun'; }
        if($month == 'July') { $shortMonth = 'Jul'; }
        if($month == 'August') { $shortMonth = 'Aug'; }
        if($month == 'September') { $shortMonth = 'Sep'; }
        if($month == 'October') { $shortMonth = 'Oct'; }
        if($month == 'November') { $shortMonth = 'Nov'; }
        if($month == 'December') { $shortMonth = 'Dec'; }

        return $shortMonth;
    }
    
    public function truckNoUpdate(Request $request) {
        $trips = $request->tripListings;
        $trucks = $request->truckNos;
        foreach($trips as $key=> $tripId) {
            $tripInfo = trip::WHERE('trip_id', $tripId)->GET()->FIRST();
            $tripInfo->truck_id = $trucks[$key];
            $tripInfo->save();
        }
        return 'updated';
    }
    
    public function exactLocationUpdate(Request $request) {
        $exactLocations = $request->destinations;
        $tripIds = $request->tripListings;
        foreach($tripIds as $key => $trip_id) {
            if(isset($exactLocations[$key]) && $exactLocations[$key] != '') {
                $trip = trip::WHERE('trip_id', $trip_id)->FIRST();
                $trip->exact_location_id = $exactLocations[$key];
                $trip->save();
            }
        }
        return 'saved';
    }
}
