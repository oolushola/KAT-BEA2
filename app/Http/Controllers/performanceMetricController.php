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
            $unitHeadSpecificTargets[] = $unitHead->target / 1000000;

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
             $myGrossMargin[] = ($myTotalRevenue - $myTotalTransporterRate) / 1000000;
             $myOutstanding[] = $myGrossMargin[$key] - $unitHeadSpecificTargets[$key];

             if($myGrossMargin[$key] == 0 || $myTotalRevenue == 0){
                $unitHeadCurrentMarkUp[] = number_format(0, 2);
             }
             else{
                $unitHeadCurrentMarkUp[] = number_format(($myGrossMargin[$key] / ($myTotalRevenue/1000000) * 100), 2);
            }
            
            $tripCount[] = trip::WHEREMONTH('gated_out', now())->WHEREYEAR('gated_out', now())->WHERE('trip_status', 1)->WHERE('account_officer_id', $unitHead->user_id)->GET()->COUNT();

        }

        $currentMonthOverview = array('unitHeadInformation' =>  $unitHeadInformation, 'unitHeadSpecificTargets' => $unitHeadSpecificTargets, 'myGrossMargin' => $myGrossMargin, 'myOutstanding' => $myOutstanding, 'unitHeadCurrentMarkUp' => $unitHeadCurrentMarkUp, 'trip_count' => $tripCount);
    
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

                $myTotalRevenue = trip::WHERE('account_officer_id', $unitHead->user_id)
                    ->WHERE('trip_status', 1)
                    ->WHERE('tracker', '>=', 5)
                    ->WHEREMONTH('gated_out', $selectedMonth)
                    ->WHEREYEAR('gated_out', $currentYear)
                    ->VALUE(DB::RAW("SUM(client_rate)")
                );
                $myTotalTransporterRate = trip::WHERE('account_officer_id', $unitHead->user_id)
                    ->WHERE('trip_status', 1)
                    ->WHERE('tracker', '>=', 5)
                    ->WHEREMONTH('gated_out', $selectedMonth)
                    ->WHEREYEAR('gated_out', $currentYear)
                    ->VALUE(DB::RAW("SUM(transporter_rate)")
                );
                $myGrossMargin[] = ($myTotalRevenue - $myTotalTransporterRate) / 1000000;
                $myOutstanding[] = $myGrossMargin[$key] - $unitHeadSpecificTargets[$key];

                if($myGrossMargin[$key] == 0 || $myTotalRevenue == 0){
                    $unitHeadCurrentMarkUp[] = number_format(0, 2);
                }
                else{
                    $unitHeadCurrentMarkUp[] = number_format(($myGrossMargin[$key] / ($myTotalRevenue/1000000) * 100), 2);
                }
                
                $tripCount[] = trip::WHEREMONTH('gated_out', $selectedMonth)->WHEREYEAR('gated_out', $currentYear)->WHERE('trip_status', 1)->WHERE('account_officer_id', $unitHead->user_id)->GET()->COUNT();

            } 
    
            $currentMonthOverview = array(
                'unitHeadInformation' =>  $unitHeadInformation, 
                'unitHeadSpecificTargets' => $unitHeadSpecificTargets, 
                'myGrossMargin' => $myGrossMargin, 
                'myOutstanding' => $myOutstanding, 
                'unitHeadCurrentMarkUp' => $unitHeadCurrentMarkUp, 
                'tripCount' => $tripCount,
                'selectedMonth' => $currentMonth.', '.$currentYear 
            );
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
                'SELECT SUM(client_rate) AS revenueForTheMonth, SUM(transporter_rate) AS totalTrFortheMonth FROM tbl_kaya_trips WHERE account_officer_id = "'.$userId.'" AND MONTH(gated_out) = "'.$selectedMonth.'" AND YEAR(gated_out) = "'.$year.'" AND trip_status = \'1\' AND tracker >= 5 '
            )
        );
    
        $profitGenerated = $choosenDateRevenueAndCost[0]->revenueForTheMonth - $choosenDateRevenueAndCost[0]->totalTrFortheMonth;
            
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
                'SELECT a.trip_id, a.gated_out, a.customers_name, a.customer_no, a.exact_location_id, a.client_rate, a.transporter_rate, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND MONTH(gated_out) = "'.$selectedMonth.'" AND YEAR(gated_out) = "'.$year.'" AND tracker >= \'5\' AND account_officer_id = "'.$userId.'" ORDER BY gated_out ASC '
            )
        );

        $totalTripsData = count($this->masterQueryData('tracker', '>=', 5, $userId));

        $myMonthAndYearData = '
            <div class="table-responsive">
                <table class="table table-condensed">
                    <tr class="table-success">
                        <td colspan="7" class="font-weight-semibold">Total number of trips: ('.count($selectedMonthAndYearData).')
                            <input type="text" placeholder="Search" style="outline:none; font-size:11px; width:150px; padding:5px" id="searchPreviousTripsOfSelectedDate" />
                        </td>
                        
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
                            if($count % 2 == 1) { $css = "table-success"; } else { $css = " "; }
                            $myMonthAndYearData.='<tr class="font-size-xs '.$css.'">
                                <td class="text-center">'.$count++.'</td>
                                <td class="text-center">'.$trips->trip_id.' 
                                    <span class="font-weight-semibold d-block">'.$trips->loading_site.'</span>
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
                        $myMonthAndYearData.='<tr>
                            <td colspan="9" class="font-size-sm">They have no trip for this selected month.</td>
                        </tr>';
                    }
                $myMonthAndYearData.='</tbody>
                </table>
            </div>
        ';

        $revenueGenerated = number_format($choosenDateRevenueAndCost[0]->revenueForTheMonth, 2);
        $transporterRate = number_format($choosenDateRevenueAndCost[0]->totalTrFortheMonth, 2);

        $percentageProfit = number_format(($profitGenerated / $myMonthlyTargetValue->target) * 100, 2);
        $target = number_format($myMonthlyTargetValue->target, 2);

        $grossMargin = $choosenDateRevenueAndCost[0]->revenueForTheMonth - $choosenDateRevenueAndCost[0]->totalTrFortheMonth;
        $percentageMarkUp = ($grossMargin / $choosenDateRevenueAndCost[0]->revenueForTheMonth) * 100;

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
            'revenueGenerated' => $revenueGenerated,
            'transporterRate' => $transporterRate,
            'percentageProfit' => $percentageProfit,
            'target' => $target,
            'ratingsChart' => $ratingsChart,
            'percentageMarkUp' => number_format($percentageMarkUp, 2),
            'tripsYetToGateOut' => $tripsYetToGateOut,
        );
        return $performanceReview;
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
}
