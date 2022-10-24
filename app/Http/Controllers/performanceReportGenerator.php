<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\buhMonthlyTarget;
use App\User;
use App\tripIncentives;
use App\Ago;

class performanceReportGenerator extends Controller
{
    public function generateReport(Request $request) {
        $startDate = $request->reportStartFrom;
        $endDate = $request->reportEndTo;
        $derivedMonth = explode('-', $startDate);
        $teammembers = buhMonthlyTarget::WHEREYEAR('created_at', $startDate)->WHEREMONTH('created_at', $derivedMonth[1])->GET();

        $result = '<div class="row">';
        
        $grandTotalRevenue = 0;
        $grandTotalCost = 0;

        foreach($teammembers as $key=> $member) {
            $user = User::findOrFail($member->user_id);
            $result.= '<div class="col-md-6">';
            $assignedClients[] = DB::SELECT(
                DB::RAW(
                    'SELECT client_alias, company_name, client_id, user_id FROM tbl_kaya_client_account_manager a JOIN tbl_kaya_clients b ON a.client_id = b.id WHERE a.user_id = "'.$user->id.'"'
                )
            );
            $result.='<h5 class="font-weight-semibold">'.$user->first_name.'\'s report</h5>';

            $distinctClient = $assignedClients[$key];

            $result.='<table class="table mb-4">
                <tr class="table-success">
                    <th>SN</th>
                    <th>CLIENT</th>
                    <th class="text-center">COUNT</th>
                    <th class="text-center">REVENUE (&#x20A6;)</th>
                    <th class="text-center">COST (&#x20A6;)</th>
                    <th class="text-center">MARGIN (&#x20A6;)</th>
                </tr>
                <tbody>';
                $count = 1;
                $sumTripCount = 0;
                $sumRevenue = 0;
                $sumNetMargin = 0;
                $sumCost = 0;
                $sumTotalIncentive = 0;
                $sumTotalAgo = 0;
                foreach ($distinctClient as $key => $client) {
                    $trips = $this->query('COUNT(*) as trip_count', $startDate, $endDate, $client->client_id);
                    $tripsCost = $this->query('SUM(transporter_rate) as cost', $startDate, $endDate, $client->client_id);
                    $tripRevenue = $this->queryRevenue($startDate, $endDate, $client->client_id);
                    $incentive = $this->queryTripIncentives($startDate, $endDate, $client->client_id);
                    $ago = $this->queryTripAgo($startDate, $endDate, $client->client_id);
                    $netMargin = $tripRevenue[0]->revenue + $incentive + $ago - $tripsCost[0]->cost;

                    $sumTripCount += $trips[0]->trip_count;
                    $sumRevenue += $tripRevenue[0]->revenue;
                    $sumCost += $tripsCost[0]->cost;
                    $sumTotalIncentive += $incentive;
                    $sumTotalAgo += $ago;

                    $result.='
                        <tr>
                            <td>'.$count++.'</td>
                            <td>'.$client->client_alias.'</td>
                            <td class="text-center">'.$trips[0]->trip_count.'</td>
                            <td class="text-center">'.number_format($tripRevenue[0]->revenue + $incentive + $ago, 2).'</td>
                            <td class="text-center">'.number_format($tripsCost[0]->cost, 2).'</td>
                            <td class="text-center">'.number_format($netMargin, 2).'</td>
                        </tr>
                    ';
                }

                $grandTotalRevenue += $sumRevenue + $sumTotalIncentive + $sumTotalAgo;
                $grandTotalCost += $sumCost;

                $result.='
                    <tr class="bg-info-400 font-weight-bold">
                        <td colspan="2">Total</td>
                        <td class="text-center">'.$sumTripCount.'</td>
                        <td class="text-center">&#x20A6;'.number_format($grandTotalRevenue, 2).'</td>
                        <td class="text-center">&#x20A6;'.number_format($sumCost, 2).'</td>
                        <td class="text-center">&#x20A6;'.number_format($grandTotalRevenue - $sumCost, 2).'</td>
                    </tr>
                </tbody>';

            $result.='</table>';

            $result.='</div>';
        } 
        
        
        $result.='</div>';

        $totalMargin_ = $grandTotalRevenue - $grandTotalCost;

        return $result.'`&#x20A6;'.number_format($grandTotalRevenue, 2).'`&#x20A6;'.number_format($grandTotalCost, 2).'`&#x20A6;'.number_format($totalMargin_, 2);
    }

    public function query($clause, $startDate, $endDate, $clientId) {
        $result = DB::SELECT(
            DB::RAW(
                'SELECT '.$clause.' FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$startDate.'" AND "'.$endDate.'" AND client_id = "'.$clientId.'" AND trip_status = 1 AND tracker >= 5'
            )
        );
        return $result;
    }

    public function queryRevenue($startDate, $endDate, $clientId) {
        $result = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) AS revenue FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$startDate.'" AND "'.$endDate.'" AND client_id = "'.$clientId.'" AND trip_status = 1 AND tracker >= 5'
            )
        );
        return $result;
    }

    public function queryTripIncentives($startDate, $endDate, $clientId) {
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT id FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$startDate.'" AND "'.$endDate.'" AND client_id = "'.$clientId.'" AND trip_status = 1 AND tracker >= 5'
            )
        );
        $sumTotalOfIncentive = 0;
        foreach($trips as $trip) {
            $tripIncentives = tripIncentives::WHERE('trip_id', $trip->id)->GET();
            if(count($tripIncentives) > 0) {
                foreach($tripIncentives as $incentive) {
                    $tripincentives_[] = $incentive;
                    $sumTotalOfIncentive += $incentive->amount;
                }
            }
        }
        return $sumTotalOfIncentive;
    }

    public function queryTripAgo($startDate, $endDate, $clientId) {
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT id FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$startDate.'" AND "'.$endDate.'" AND client_id = "'.$clientId.'" AND trip_status = 1 AND tracker >= 5'
            )
        );
        $sumTotalAgo = 0;
        foreach($trips as $trip) {
            $tripAgos = Ago::WHERE('trip_id', $trip->id)->GET();
            if(count($tripAgos) > 0) {
                foreach($tripAgos as $ago) {
                    $tripAgos_[] = $ago;
                    $sumTotalAgo += $ago->amount;
                }
            }
        }
        return $sumTotalAgo;
    }
}

