<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\completeInvoice;
use App\client;

class trackerController extends Controller
{
    public function receivables() {
        $totalUnpaidTrips = completeInvoice::WHERE('acknowledged', TRUE)
        ->WHERE('acknowledged_date', '!=', '')
        ->WHERE('date_paid', NULL)
        ->GET();

    
        $invoicesYetToBePaid = completeInvoice::ORDERBY('invoice_no', 'DESC')
        ->SELECT('invoice_no', 'completed_invoice_no', 'acknowledged_date')
        ->WHERE('acknowledged', TRUE)
        ->WHERE('acknowledged_date', '!=', '')
        ->WHERE('date_paid', NULL)
        ->DISTINCT()
        ->GET();

        $overdueTodayCounter = 0;
        $overdueCounter = 0;
        $sumOfOverDueInvoices = 0;
        foreach($invoicesYetToBePaid as $pendingInvoices) {
            $now = time();
            $acknowledged = strtotime($pendingInvoices->acknowledged_date);
            $datediff = $acknowledged - $now;
            $numberofdays = (floor($datediff / (60 * 60 * 24)) * -1) -1;

            if($numberofdays == 14) {
                $overdueTodayCounter = $overdueTodayCounter + 1;
            }

            if($numberofdays > 14) {
                $overdueCounter = $overdueCounter + 1;
                $overdueInvoices[] = $pendingInvoices->invoice_no;

            }
        }

        $receiveablesAccount = DB::SELECT(
            DB::RAW(
                'SELECT SUM(b.client_rate) as receivable FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b ON a.trip_id = b.id WHERE acknowledged = TRUE AND acknowledged_date != "" AND date_paid IS NULL '
            )
        );

        $sumTotalofOverdueInvoices = 0;
        foreach($overdueInvoices as $key => $invoiceOverdue) {
            [$amountofOverdueInvoices[]] = DB::SELECT(
                DB::RAW(
                    'SELECT a.*, b.client_rate FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b ON a.trip_id = b.id WHERE invoice_no = '.$invoiceOverdue.' '
                )
            );
            $sumTotalofOverdueInvoices += $amountofOverdueInvoices[$key]->client_rate;
        }
        
        $currentMonth = date('m');
        $currentYear = date('Y');
        $lastMonth = $currentMonth - 1;
        
        $lastMonthTrip =  DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate * 1.025) AS totalClientRate, SUM(transporter_rate) AS transporterRate, COUNT(gated_out) AS totalGateOut FROM tbl_kaya_trips WHERE YEAR(gated_out) =  "'.$currentYear.'" AND MONTH(gated_out) = "'.$lastMonth.'" AND trip_status = 1 '
            )
        );

        $lastMonthMargin = $lastMonthTrip[0]->totalClientRate - $lastMonthTrip[0]->transporterRate;
        $avgMarginForLastMonth = number_format(($lastMonthMargin / $lastMonthTrip[0]->totalGateOut), 2);


        
        if(date('m') == 7) { $opsStarted = 0; }
        if(date('m') == 8) { $opsStarted = 1; }
        if(date('m') == 9) { $opsStarted = 2; }
        if(date('m') == 10) { $opsStarted = 3; }
        if(date('m') == 11) { $opsStarted = 4; }
        if(date('m') == 12) { $opsStarted = 5; }
        if(date('m') == 1) { $opsStarted = 6; }
        if(date('m') == 2) { $opsStarted = 7; }
        if(date('m') == 3) { $opsStarted = 8; }
        if(date('m') == 4) { $opsStarted = 9; }
        if(date('m') == 5) { $opsStarted = 10; }
        if(date('m') == 6) { $opsStarted = 11; }
        
        
        $lastOneYearAndMonth = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT YEAR(gated_out) AS "years", MONTH(gated_out) AS "months" FROM tbl_kaya_trips WHERE gated_out <> "" LIMIT '.$opsStarted.', 13'
            )  
        );

        foreach($lastOneYearAndMonth as $key => $period) {
            $periods[] = date('M', mktime(0,0,0,$period->months, 1, date('Y'))).', '.$period->years;
            [$getRevenueResult[]] = DB::SELECT(
                DB::RAW(
                    'SELECT ROUND(SUM((client_rate * 1.025) / 1000000), 2) AS revenuePerMonth FROM tbl_kaya_trips WHERE year(gated_out) = "'.$period->years.'" AND MONTH(gated_out) = "'.$period->months.'" AND trip_status = 1 '
                )
            );

            [$tripmarginpermonth[]] = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(client_rate * 1.025) AS revenue, SUM(transporter_rate) as cost, ROUND(SUM(((client_rate * 1.025) - transporter_rate)/1000000), 2) AS margin FROM tbl_kaya_trips WHERE YEAR(`gated_out`) = "'.$period->years.'" AND MONTH(gated_out) = "'.$period->months.'"'
                )
            );

            $expenses[] = DB::SELECT(
                DB::RAW(
                    'SELECT * FROM tbl_kaya_expenses WHERE year = "'.$period->years.'" AND month = "'.$period->months.'"'
                )
            );

            if($expenses[$key]){
                $monthlyExpenses[] = number_format((float)$expenses[$key][0]->expenses / 1000000, 2, '.', '');                
            }
            else{ 
                $monthlyExpenses[] = 0;
            }

            $profitAndLoss[] = number_format((float)$tripmarginpermonth[$key]->margin - $monthlyExpenses[$key], 2);
        }

        $margins = array_column($tripmarginpermonth, 'margin');
        $revenues = array_column($getRevenueResult, 'revenuePerMonth');

        $sumOfTotalMargin = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate * 1.025) AS totalRevenue, SUM(transporter_rate) AS totalCost, ROUND(SUM((client_rate * 1.025) - transporter_rate), 2) AS totalMargin FROM tbl_kaya_trips'
            )
        );
        $totalMargin = $sumOfTotalMargin[0]->totalMargin;
        $otherExpenses = DB::SELECT(
            DB::RAW(
                'SELECT SUM(expenses) AS totalExpenses FROM tbl_kaya_expenses'
            )
        );
        $totalExpenses = $otherExpenses[0]->totalExpenses;


        $clients = client::ORDERBY('client_alias')->SELECT('id', 'company_name', 'client_alias')->WHERE('client_alias', '!=', '')->GET();
        $sumOfAllOutstandingTrips = 0;
        foreach($clients as $key => $clientListings) {
            $companyNames[] = $clientListings->client_alias;
            $clientOutStandingPayment[] = DB::SELECT(
                DB::RAW(
                    'SELECT a.id, a.trip_id, a.client_id, a.client_rate, b.id AS invoiceTripId, b.invoice_no, b.completed_invoice_no, b.acknowledged_date FROM tbl_kaya_trips a JOIN `tbl_kaya_complete_invoices` b ON a.id = b.trip_id WHERE client_id = "'.$clientListings->id.'" AND tracker = 8 AND trip_status = 1 AND b.`acknowledged` = true AND b.date_paid IS NULL'
                )
            );

            // get the sum of every single nexted array and save inside of a destructured array!
            if($clientOutStandingPayment[$key]) {
                $sumOfAllOutstandingTrips = 0;
                $sumOfOverdueInvoices = 0;
                foreach($clientOutStandingPayment[$key] as $specificTrip){
                    $sumOfAllOutstandingTrips += $specificTrip->client_rate * 1.025;

                    //perform overdue invoices operation here
                    $now = time();
                    $acknowledged = strtotime($specificTrip->acknowledged_date);
                    $datediff = $acknowledged - $now;
                    $numberofdays = (floor($datediff / (60 * 60 * 24)) * -1) -1;

                    if($numberofdays > 14) {
                        $sumOfOverdueInvoices += $specificTrip->client_rate * 1.025;
                    }
                }

                $sumTotal[] = number_format(($sumOfAllOutstandingTrips) / 1000000, 3); 
                $sumOverdue[] = number_format($sumOfOverdueInvoices / 1000000, 3);
                $yetToDueDifference[] = number_format($sumTotal[$key] - $sumOverdue[$key], 3);
            }
            else{
                $sumTotal[] = 0;
                $sumOverdue[] = 0;
                $yetToDueDifference[] = $sumTotal[$key] - $sumOverdue[$key];
            }
        }

        return view('finance.financials.receivables-tracker',
            compact(
                'totalUnpaidTrips',
                'invoicesYetToBePaid',
                'avgMarginForLastMonth',
                'periods',
                'margins',
                'profitAndLoss',
                'monthlyExpenses',
                'revenues',
                'overdueTodayCounter',
                'overdueCounter',
                'receiveablesAccount',
                'sumTotalofOverdueInvoices',
                'totalMargin',
                'totalExpenses',
                'companyNames',
                'yetToDueDifference',
                'sumOverdue',
                'clients'
            )
        );
    }

    public function clientRevenue(Request $request) {
        $lastOneYearAndMonth = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT YEAR(gated_out) AS "years", MONTH(gated_out) AS "months" FROM tbl_kaya_trips WHERE gated_out <> "" LIMIT 1, 13'
            )  
        );

        foreach($lastOneYearAndMonth as $key => $period) {
            $periods[] = date('M', mktime(0,0,0,$period->months, 1, date('Y'))).', '.$period->years;
            [$getRevenueResult[]] = DB::SELECT(
                DB::RAW(
                    'SELECT ROUND(SUM((client_rate * 1.025)  / 1000000), 2) AS revenuePerMonth FROM tbl_kaya_trips WHERE year(gated_out) = "'.$period->years.'" AND MONTH(gated_out) = "'.$period->months.'" AND client_id = "'.$request->client.'" AND trip_status = 1 '
                )
            );
        }
        $clientRevenueForYearInView = array_column($getRevenueResult, 'revenuePerMonth');
        return [$periods, $clientRevenueForYearInView];
    }
}
