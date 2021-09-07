<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\completeInvoice;
use App\client;
use App\ExpensesBreakdown;
use App\expenses;
use App\VatRate;

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
        if($lastMonthTrip[0]->totalGateOut != 0){
            $avgMarginForLastMonth = number_format(($lastMonthMargin / $lastMonthTrip[0]->totalGateOut), 2);
        }
        else{
            $avgMarginForLastMonth = 0;
        }
        
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
                'SELECT DISTINCT YEAR(gated_out) AS "years", MONTH(gated_out) AS "months" FROM tbl_kaya_trips WHERE gated_out <> "" LIMIT 14, 15'
            )  
        );

        $divisior = 1000000;
        foreach($lastOneYearAndMonth as $key => $period) {
            $periods[] = date('M', mktime(0,0,0,$period->months, 1, date('Y'))).', '.$period->years;

            [$getRevenueResult[]] = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(client_rate) as clientRate FROM tbl_kaya_trips WHERE YEAR(gated_out) = "'.$period->years.'" AND MONTH(gated_out) = "'.$period->months.'" AND trip_status = 1 '
                )
            );

            //get incentives too;
            [$incentives[]] = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(IFNULL(amount, 0)) AS incentive FROM tbl_kaya_trip_incentives WHERE YEAR(updated_at) = "'.$period->years.'" AND MONTH(updated_at) = "'.$period->months.'" '
                )
            );

            $incentive = $incentives[$key]->incentive;
            $revenue = ($getRevenueResult[$key]->clientRate + $incentive) * 1.025;
            $revenues[] = number_format($revenue / $divisior , 2);
            
            [$tripmarginpermonth[]] = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(client_rate / '.$divisior.') AS revenue, SUM(transporter_rate / '.$divisior.') as cost FROM tbl_kaya_trips WHERE YEAR(`gated_out`) = "'.$period->years.'" AND MONTH(gated_out) = "'.$period->months.'"'
                )
            );

            $margin[] = number_format(($revenue/ $divisior) - $tripmarginpermonth[$key]->cost, 2);

            $expenses[] = DB::SELECT(
                DB::RAW(
                    'SELECT * FROM tbl_kaya_expenses WHERE year = "'.$period->years.'" AND month = "'.$period->months.'"'
                )
            );

            if($expenses[$key]){
                $monthlyExpenses[] = number_format((float)$expenses[$key][0]->expenses / $divisior, 2, '.', '');                
            }
            else{ 
                $monthlyExpenses[] = 0;
            }

            $profitAndLoss[] = number_format((float)$margin[$key] - $monthlyExpenses[$key], 2);
        }
        // return $tripmarginpermonth;
        $margins = $margin;
        $sumOfTotalMargin = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate * 1.025) AS totalRevenue, SUM(transporter_rate) AS totalCost, ROUND(SUM((client_rate * 1.025) - transporter_rate), 2) AS totalMargin FROM tbl_kaya_trips WHERE tracker >= 5 AND trip_status = TRUE'
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
                    'SELECT a.id, a.trip_id, a.client_id, a.client_rate, @amountPaid := IFNULL(a.amount_paid, 0) as amountPaid, a.client_rate - @amountPaid as diff, b.id AS invoiceTripId, b.invoice_no, b.completed_invoice_no, b.acknowledged_date, b.created_at, b.vat_used, b.withholding_tax_used FROM tbl_kaya_trips a JOIN `tbl_kaya_complete_invoices` b ON a.id = b.trip_id WHERE client_id = "'.$clientListings->id.'" AND tracker >= 5 AND trip_status = 1 AND b.`payment_type` = FALSE'
                )
            );

            // get the sum of every single nexted array and save inside of a destructured array!
            if($clientOutStandingPayment[$key]) {
                $sumOfAllOutstandingTrips = 0;
                $sumOfOverdueInvoices = 0;
                foreach($clientOutStandingPayment[$key] as $specificTrip){
                    if($specificTrip->vat_used > 0) {
                        $sumOfAllOutstandingTrips += $specificTrip->diff * 1.025;
                    }
                    else {
                        $sumOfAllOutstandingTrips += $specificTrip->diff;
                    }

                    //perform overdue invoices operation here
                    $now = time();
                    $acknowledged = strtotime($specificTrip->created_at);
                    $datediff = $acknowledged - $now;
                    $numberofdays = (floor($datediff / (60 * 60 * 24)) * -1) -1;

                    if($numberofdays > 14) {
                        if($specificTrip->vat_used > 0) {
                            $sumOfOverdueInvoices += $specificTrip->diff * 1.025;
                        }
                        else {
                            $sumOfOverdueInvoices += $specificTrip->diff;
                        }

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
                    'SELECT ROUND(SUM((client_rate * 1.025)  / 1000000), 2) AS revenuePerMonth FROM tbl_kaya_trips WHERE year(gated_out) = "'.$period->years.'" AND MONTH(gated_out) = "'.$period->months.'" AND client_id = "'.$request->client.'" AND trip_status = 1 '
                )
            );
        }
        $clientRevenueForYearInView = array_column($getRevenueResult, 'revenuePerMonth');
        return [$periods, $clientRevenueForYearInView];
    }

    public function clientPaymentModel(Request $request) {
        $client = client::WHERE('client_alias', $request->client)->GET()->FIRST();
        $allInvoicedTrips = DB::SELECT(
            DB::RAW(
                'SELECT SUM((client_rate * (vat_used / 100)) + client_rate ) as gtv  FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id WHERE client_id = "'.$client->id.'" AND b.paid_status = FALSE'
            )
        );
        $totalReceived = DB::SELECT(
            DB::RAW(
                'SELECT SUM((client_rate * (vat_used / 100)) + client_rate ) as received_amount  FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id WHERE client_id = "'.$client->id.'" AND acknowledged = TRUE AND b.paid_status = TRUE'
            )
        );
        $acknowledgedNotReceived = DB::SELECT(
            DB::RAW(
                'SELECT SUM((client_rate * (vat_used / 100)) + client_rate ) as ack_np  FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id WHERE client_id = "'.$client->id.'" AND acknowledged = TRUE AND b.paid_status = FALSE'
            )
        );

        $allUnpaidInvoices = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT invoice_no, completed_invoice_no, vat_used, acknowledged_date, paid_status, a.created_at FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b ON a.trip_id = b.id AND client_id = "'.$client->id.'" ORDER BY invoice_no DESC'
            )
        );

        $lastTwentyTrips = DB::SELECT(
            DB::RAW(
                'SELECT * FROM (SELECT DISTINCT invoice_no, completed_invoice_no, DATEDIFF(date_paid, a.acknowledged_date) AS no_of_days FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b on a.trip_id = b.id WHERE paid_status =  TRUE AND client_id = "'.$client->id.'" ORDER BY invoice_no DESC LIMIT 20) tbl_kaya_complete_invoices ORDER BY completed_invoice_no DESC'
            )
        );
        $totalDays = 0;
        $maxDays = 0;
        if(count($lastTwentyTrips)) {
            foreach($lastTwentyTrips as $lastTwenty) {
                $totalDays+= $lastTwenty->no_of_days;
                $invoices[] = $lastTwenty->completed_invoice_no;
                $days[] = $lastTwenty->no_of_days;
            }
            $avgDayToPayment = floor($totalDays / count($lastTwentyTrips));
            if($avgDayToPayment > 14) {
                $maxDays = 14;
            }
            else{
                $maxDays = $avgDayToPayment;
            }
        }

        $acknowledgedTrips = DB::SELECT(
            DB::RAW(
                'SELECT a.trip_id, client_rate, vat_used, invoice_no, completed_invoice_no, acknowledged_date, (client_rate * (vat_used / 100)) + client_rate  as ack_np, c.amount, c.incentive_description  FROM tbl_kaya_trips a LEFT JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id LEFT JOIN tbl_kaya_trip_incentives c ON a.id = c.trip_id WHERE client_id = "'.$client->id.'" AND acknowledged = TRUE AND acknowledged_date <> \'NULL\' AND b.paid_status = FALSE'
            )
        );

        $response = '
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-body text-primary-300 text-center">
                        <span class="font-weight-bold"><i class="icon-pencil3 mr-1"></i>Invoiced</span>
                        <h5 class="m-0 p-0 mt-1 ml-3 font-weight-semibold">&#x20a6;'.number_format($allInvoicedTrips[0]->gtv, 2).'</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-body text-center">
                        <span class="font-weight-bold"><i class="icon-coins mr-1"></i>Expected Amount: '.date('d/m/Y').'</span>';
                        if(count($acknowledgedTrips)) {
                            $sumOfAcknowledgedTrips = 0;
                            foreach($acknowledgedTrips as $key=> $tripAcknowledged) {
                                $today = time();
                                $acknowledgedTrip = strtotime($tripAcknowledged->acknowledged_date);
                                $date_diff = $acknowledgedTrip - $today;
                                $nod = (floor($date_diff / (60 * 60 * 24)) * -1) -1;
                                if($nod > $maxDays) {
                                    $sumOfAcknowledgedTrips += $tripAcknowledged->ack_np + $tripAcknowledged->amount;
                                }
                            }
                            $expectedAmount = $sumOfAcknowledgedTrips;
                        }
                        else{
                            $expectedAmount = 0;
                        }
                        $response.='
                            <h5 class="m-0 p-0 mt-1 ml-3 font-weight-semibold">&#x20a6;'.number_format($expectedAmount, 2).'</h5>
                    </div>                            
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-body text-success text-center">
                        <span class="font-weight-bold"><i class="icon-bag mr-1"></i>Total Amount Received</span>
                        <h5 class="m-0 p-0 mt-1 ml-3 font-weight-semibold">&#x20a6;'.number_format($totalReceived[0]->received_amount, 2).'</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-body text-info text-center">
                        <span class="font-weight-bold">
                            <i class="icon-coins mr-1"></i>Acknowledged not Received</span>
                        <h5 class="m-0 p-0 mt-1 ml-3 font-weight-semibold">&#x20a6;'.number_format($acknowledgedNotReceived[0]->ack_np, 2).'</h5>
                    </div>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="card"  style="border:none">
                    <div class="card-body">
                        <span class="font-weight-bold d-block mb-1  text-danger-300">
                            <select style="font-size:10px; outline:none; padding:5px; border: none" id="filterPayments">
                                <option>All Invoices</option>
                                <option>Overdue</option>
                                <option>Paid</option>
                                <option>Not Due</option>
                            </select>';
                            if(count($allUnpaidInvoices) > 12) {
                                $response.='<input type="text" style="padding:4px; outline:none; border:none; " placeholder="FIND AN INVOICE" class="font-size-sm font-weight-semibold" id="searchFilteredPayments"  />';
                            }
                            
                        $response.='</span>
                        
                        <div class="row" style="max-height:200px; overflow:auto" id="paymentsLog">';
                            if(count($allUnpaidInvoices)) {
                                foreach($allUnpaidInvoices as $key=> $unpaidInvoice) {
                                    $now = time();
                                    $dateInvoiced = strtotime($unpaidInvoice->created_at);
                                    $datediff = $dateInvoiced - $now;
                                    $numberofdays = (floor($datediff / (60 * 60 * 24)) * -1) -1;
        
                                    $response.='
                                    <div class="col-md-2">
                                        <a href="/invoice-trip/'.$unpaidInvoice->completed_invoice_no.'" target="_blank" style="color:#333">
                                        <div class="card">';
                                            if(!$unpaidInvoice->paid_status) {
                                                $response.='
                                                <div class="invoice-receivables">
                                                    <div>'.$numberofdays.'</div>
                                                </div>';
                                            }
                                            $response.='<div class="card-body text-center">
                                                <span class="font-weight-bold d-block">'.$unpaidInvoice->completed_invoice_no.'</span>
                                                <span class="d-block font-weight-semibold text-primary-400 font-size-xs">
                                                    '.ltrim(date('dS \of M, Y', strtotime($unpaidInvoice->created_at)), '0').'
                                                </span>'; 
                                                if($unpaidInvoice->paid_status) {
                                                    $response.='<span class="badge badge-success"><i class="icon-checkmark2"></i>Paid</span>';
                                                }
                                                else{
                                                    if($numberofdays > $maxDays) {
                                                        $response.='<span class="badge badge-danger">Overdue</span>';
                                                    }
                                                    else {
                                                        $response.='<span class="badge badge-primary">Not Due</span>';
                                                    }
                                                }
                                            $response.='
                                            </div>
                                        </div></a>
                                    </div>';
                                }
                            }
                            else {
                                $response.='<div class="col-md-12 text-center">
                                    <p class="font-weight-semibold text-danger">There are no pending invoice for this client</p>
                                </div>';
                            }

                        $response.='</div>
                    </div>
                </div>
            </div>
            
            
        </div>';

        return array(
            'clientInfo' => $client,
            'model' => $response,
            'invoiceNos' => $invoices,
            'no_days_paid' => $days,
            'avg_payment_days' => $avgDayToPayment
        );
    }


    function numericalMonthDetector($selectedMonth) {
        if($selectedMonth == 'Jan') { $month = 1; }
        if($selectedMonth == 'Feb') { $month = 2; }
        if($selectedMonth == 'Mar') { $month = 3; }
        if($selectedMonth == 'Apr') { $month = 4; }
        if($selectedMonth == 'May') { $month = 5; }
        if($selectedMonth == 'Jun') { $month = 6; }
        if($selectedMonth == 'Jul') { $month = 7; }
        if($selectedMonth == 'Aug') { $month = 8; }
        if($selectedMonth == 'Sep') { $month = 9; }
        if($selectedMonth == 'Oct') { $month = 10; }
        if($selectedMonth == 'Nov') { $month = 11; }
        if($selectedMonth == 'Dec') { $month = 12; }

        return $month;
    }

    public function showExpensesBreakdown(Request $request) {
        $date = $request->time_inview;
        $splitter = explode(',', $date);
        $month = $this->numericalMonthDetector($splitter[0]);
        $selectedYear = trim($splitter[1]);

        //return $month.' '.$selectedYear;

        $expenseList = ExpensesBreakdown::SELECT('category', 'amount')->WHERE('current_month', $month)->WHERE('current_year', $selectedYear)->GET();
        $expense = expenses::WHERE('year', $selectedYear)->WHERE('month', $month)->GET()->LAST();

        if(count($expenseList) <= 0) {
            return 'not_found';
        }
        else{
            foreach($expenseList as $key => $expenses) {
                $expensesAmount[] = $expenses->amount / 1000000;
                $expensesCategory[] = ucfirst($expenses->category); 
                $percentageOccupied[] = round(($expensesAmount[$key] / $expense->expenses) * 100, 2);
            }

            return array(
                'percentage' => $percentageOccupied, 
                'categories' => $expensesCategory, 
                'allExpenses' => $expense,
                'expensesAmount' => $expensesAmount
            );
        }
    }

    public function expenseCategoryBreakdown(Request $request) {
        $currentMonth = $this->numericalMonthDetector($request->month);
        $currentYear = $request->year;
        $expenseCategory = $request->expenseCategory;
        $sumTotal = DB::SELECT(DB::RAW(
            'SELECT SUM(b.amount) AS total_amount FROM tbl_kaya_expenses_breakdowns a JOIN tbl_kaya_payment_voucher_descs b JOIN tbl_kaya_payment_vouchers c  ON a.category = b.expense_type AND b.payment_voucher_id = c.id  WHERE current_month = "'.$currentMonth.'" AND current_year = "'.$currentYear.'" AND expense_type="'.$expenseCategory.'" AND upload_status = TRUE AND voucher_status = TRUE AND decline_status = FALSE'
        ));
        $response = '<table class="table table-condensed">
            <thead>
                <tr>
                    <th colspan="5" class="font-weight-bold text-danger">'.$expenseCategory.'</th>
                </tr>
                <tr>
                    <td colspan="4">&nbsp;</td>
                    <td class="font-weight-bold bg-danger text-center">Total: &#x20A6;'.number_format($sumTotal[0]->total_amount, 2).'</td>
                </tr>
                <tr class="text-center">
                    <th>SN</th>
                    <th>VOUCHER ID</th>
                    <th>PAID TO</th>
                    <th>DESCRIPTION</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>';
            $expensesBreakdown = DB::SELECT(
                DB::RAW(
                    'SELECT expense_type, description, payment_voucher_id, b.amount, b.owner, uniqueId FROM tbl_kaya_expenses_breakdowns a JOIN tbl_kaya_payment_voucher_descs b JOIN tbl_kaya_payment_vouchers c ON a.category = b.expense_type AND b.payment_voucher_id = c.id WHERE current_month = "'.$currentMonth.'" AND current_year = "'.$currentYear.'" AND expense_type="'.$expenseCategory.'"'
                )
            );
        
        $response.="<tbody>";
        if(count($expensesBreakdown) > 0) {
            $counter = 1;
            foreach ($expensesBreakdown as $key => $expense) {
                $response.='
                <tr class="text-center">
                    <td>'.$counter++ .'</td>
                    <td>'.strtoupper($expense->uniqueId).'</td>
                    <td>'.$expense->owner.'</td>
                    <td>'.$expense->description.'</td>
                    <td>'.number_format($expense->amount, 2).'</td>
                </tr>';
            }
        }
        else{
            $response.='<tr>
                <td colspan="5">No expenses found for this expense category</td>
            </tr>';
        }
        $response.="</table>";
        return $response;
    }
}
