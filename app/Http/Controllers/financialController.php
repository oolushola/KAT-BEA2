<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\clientProduct;
use App\completeInvoice;
use App\cargoAvailability;
use App\target;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\invoiceClientRename;
use App\vatRate;
use App\expenses;

class financialController extends Controller
{
    function revenueCalculator($yearInView, $monthInview, $alias) {
        $revenueGenerator =  DB::SELECT(
            DB::RAW('SELECT SUM(client_rate) AS "'.$alias.'" FROM tbl_kaya_trips WHERE MONTH(gated_out) = "'.$monthInview.'" AND YEAR(gated_out) = "'.$yearInView.'"')
        );
        return $revenueGenerator;
    }

    function outflowCalculator($params, $clause, $monthInview, $yearInview) {
        $outflow = DB::SELECT(
            DB::RAW(
                'SELECT '.$params.' FROM tbl_kaya_trips a JOIN tbl_kaya_trip_payments b ON a.id = b.trip_id WHERE '.$clause.' = TRUE AND MONTH(b.updated_at) = '.$monthInview.' AND YEAR(b.updated_at) = '.$yearInview.' ORDER BY a.trip_id ASC'
            )
        );
        return $outflow;
    }

    function invoiceTrend($monthInview, $yearInview) {
        $avgInvoiceTrend = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate + ((client_rate + IFNULL(amount, 0)) * (vat_used / 100)))   - SUM((client_rate + IFNULL(amount, 0)) * (withholding_tax_used/100)) as amountPayable FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id LEFT OUTER JOIN tbl_kaya_trip_incentives c ON a.id = c.trip_id WHERE MONTH(b.created_at) = '.$monthInview.' AND YEAR(b.created_at) = '.$yearInview.''
            )
        );
        return $avgInvoiceTrend;
    }

    public function financialsOverview(){
        $divisor = 1000000;
        $invoicedAndUnpaid = DB::SELECT(
            DB::RAW(
                'SELECT a.id, client_rate, amount_paid, vat_used, withholding_tax_used, invoice_no, amount as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id LEFT JOIN tbl_kaya_trip_incentives c ON a.id = c.trip_id WHERE paid_status = FALSE '
            )
        );
        $amountPayable = 0;
        foreach($invoicedAndUnpaid as $unpaidTrip) {
            $unpaidTrip->incentive == NULL ? $incentive = 0 : $incentive = $unpaidTrip->incentive;
            $unpaidTrip->amount_paid != NULL ? $clientRate = $unpaidTrip->amount_paid + $incentive : $clientRate = $unpaidTrip->client_rate + $incentive;
            $unpaidTrip->vat_used == NULL ? $vat = 5 : $vat = $unpaidTrip->vat_used;
            $unpaidTrip->withholding_tax_used ? $wtx_ = 5 : $wtx_ = $unpaidTrip->withholding_tax_used;
            $totalDue = $clientRate  + ($clientRate * $vat / 100);
            $wtx = $clientRate * ($wtx_ / 100);
            $amountPayable+= $totalDue - $wtx;
        }
        $invoicedNotPaid = round($amountPayable / $divisor, 2);
        $notInvoiced = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) as total_uninvoiced_trips FROM tbl_kaya_trips a LEFT JOIN tbl_kaya_trip_waybill_statuses b ON a.id = b.trip_id JOIN `tbl_kaya_vat_rates` c WHERE `date_invoiced` IS NULL AND trip_status = TRUE ORDER BY a.trip_id  ASC'
            )
        );
        $taxInfo = vatRate::GET()->FIRST();
        $totalUninvoiced_ = $notInvoiced[0]->total_uninvoiced_trips;
        $subtotalUninvoicedwithVat = $totalUninvoiced_ + ($totalUninvoiced_ * ($taxInfo->vat_rate / 100));
        $totalUninvoicedwithwtx = $totalUninvoiced_ *($taxInfo->withholding_tax / 100);
        $totalUnvoicedTrips_ = $subtotalUninvoicedwithVat - $totalUninvoicedwithwtx;
        $notInvoiced = round($totalUnvoicedTrips_ / $divisor, 2);

        $counter = -1;
        $lastYear = date('Y') - 1;
        for ($m=1; $m<=12; $m++) {
            $counter+=1;
            $monthName = date('F', mktime(0,0,0,$m, 1, date('Y')));
            $monthsOfTheYear[] = $monthName;

            [$currentYearRevenue_[]] = $this->revenueCalculator(date('Y'), $m, 'currentYearRevenue');
            if($currentYearRevenue_[$counter]->currentYearRevenue > 0) {
                $currentYearRevenue[] = round(($currentYearRevenue_[$counter]->currentYearRevenue) / $divisor, 2);
            }
            
            [$lastYearRevenue_[]] = $this->revenueCalculator($lastYear, $m, 'lastYearRevenue');
            $lastYearRevenue[] = round(($lastYearRevenue_[$counter]->lastYearRevenue) / $divisor, 2);

            // Cash Inflow Calculator

            [$cashInflow[]] = DB::SELECT(
                DB::RAW(
                    'SELECT SUM(client_rate) AS totalClientRate FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id WHERE MONTH(date_paid) = "'.$m.'" AND YEAR(date_paid) = "'.date('Y').'"'
                )
            );
            $cashInflow[$counter]->totalClientRate == NULL ? $cis[] = 0 : $cis[] = $cashInflow[$counter]->totalClientRate;
            $vatedCis[] = $cis[$counter] + ($cis[$counter] * ($taxInfo->vat_rate / 100));
            $wtxCis[] = $cis[$counter] *($taxInfo->withholding_tax / 100);
            $totalCis[] = round(($vatedCis[$counter] - $wtx[$counter]) / $divisor, 2);
            
            // Cash Outflow Calculator
            
            [$advanceMonthlyCo[]] = $this->outflowCalculator('SUM(advance) AS advanceOutflow', 'b.advance_paid', $m, date('Y'));
            $advanceMonthlyCo[$counter]->advanceOutflow == NULL ? $advancecof[] = 0 : $advancecof[] = $advanceMonthlyCo[$counter]->advanceOutflow;
            [$balanceMonthlyCo[]] = $this->outflowCalculator('SUM(balance) AS balanceOutflow', 'b.balance_paid', $m, date('Y'));
            $balanceMonthlyCo[$counter]->balanceOutflow == NULL ? $balancecof[] = 0 : $balancecof[] = $balanceMonthlyCo[$counter]->balanceOutflow;

            $expenses[] = expenses::WHERE('month', $m)->WHERE('year', date('Y'))->GET()->FIRST();
            $expenses[$counter] != NULL ? $totalExpenses[] = $expenses[$counter]->expenses : $totalExpenses[] = 0;

            $outflow_[] =  $advancecof[$counter] + $balancecof[$counter] + $totalExpenses[$counter];
            $outflow[] = round($outflow_[$counter] / $divisor, 2);

            //Invoice Trend...
            [$lastYearInvoiceTrend[]] = $this->invoiceTrend($m, $lastYear);
            $lastYearInvTrend_[] = $lastYearInvoiceTrend[$counter]->amountPayable;
            [$currentYearInvoiceTrend_[]] = $this->invoiceTrend($m, date('Y'));
            $currentYearInvStatus = $currentYearInvoiceTrend_[$counter]->amountPayable;
            $currentYearInvStatus == NULL ? $invTrend_[] = 0 : $invTrend_[] = $currentYearInvStatus;

            $invTrend[] = round($invTrend_[$counter] / $divisor, 2);
            $previousYearInvTrend[] = round($lastYearInvTrend_[$counter] / $divisor, 2);
        }

        $result = compact('monthsOfTheYear', 'lastYearRevenue', 'currentYearRevenue', 'invoicedNotPaid', 'notInvoiced', 'totalCis', 'outflow', 'invTrend', 'previousYearInvTrend');
        return view('finance.financials.overview', $result);
    }

    public function loadingSiteOnFinance(Request $request) {
        $loadingsites = DB::SELECT(
            DB::RAW(
                'select b.* from tbl_kaya_client_loading_sites a join tbl_kaya_loading_sites b on a.loading_site_id = b.id where client_id = '.$request->client_id.''
            )
        );
        $answer = '<select class="form-control" name="loading_site_id" id="loadingSite">
                    <option value=""></option>';
        foreach($loadingsites as $loadingsite){
            $answer.='<option value="'.$loadingsite->id.'">'.$loadingsite->loading_site.'</option>';
        }
        
        $answer.='</select>';

        return $answer;
    }

    public function displayFinancialRecords(Request $request) {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        $loadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id WHERE tracker <> \'0\' ORDER BY a.trip_id DESC'
            )
        );

        foreach($orders as $trip) {
            $waybills[] = tripWaybill::SELECT('trip_id', 'sales_order_no', 'invoice_no')->WHERE('trip_id', $trip->id)->GET();
        }

        foreach($waybills as $tripWaybills){
            foreach($tripWaybills as $waybill){
                $waybillListings[] = $waybill;
            }
        }

        $collection = new Collection($orders);
        $perPage = 50;
        $currentPage =  $request->get('page');
        $pagedData = $collection->slice($currentPage * $perPage, $perPage)->all();
        $path = url('/').'/financials/dashboard?'.$currentPage;
        $pagination = new LengthAwarePaginator(($pagedData), count($collection), $perPage );
        $pagination = $pagination->withPath($path);
        
        $transporterPayment = DB::SELECT(
            DB::RAW(
                'SELECT SUM(advance) as totaladvancepaid, SUM(balance) AS totalbalancepaid FROM tbl_kaya_trip_payments'
            )
        );

        $trippayments = tripPayment::GET();
        $revenue = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) AS totalRevenue, SUM(transporter_rate) AS totalTransporterRate FROM tbl_kaya_trips WHERE tracker >=5'
            )
        );

        foreach($orders as $specificTrip){
            $completeInvoiceListings[] = completeInvoice::WHERE('trip_id', $specificTrip->id)->ORDERBY('invoice_no', 'ASC')->GET();
        }
        foreach($completeInvoiceListings as $completedInvoicing) {
            foreach($completedInvoicing as $invoices) {
                $completedInvoices[] = $invoices;
            }
        }

        $billers = DB::SELECT(
            DB::RAW(
                'SELECT a.client_name, a.invoice_no, b.* FROM tbl_kaya_invoice_biller a JOIN tbl_kaya_complete_invoices b ON a.invoice_no = b.completed_invoice_no '
            )
        );

        $destinations = trip::SELECT('exact_location_id AS destination')->ORDERBY('exact_location_id', 'ASC')->DISTINCT()->GET();
        $invoiceNos = completeInvoice::SELECT('invoice_no', 'completed_invoice_no')->ORDERBY('invoice_no', 'ASC')->DISTINCT()->GET();

        return view('finance.financials.view',
            compact(
                'pagination',
                'waybillListings',
                'trippayments',
                'clients',
                'transporters',
                'loadingSites',
                'revenue',
                'transporterPayment',
                'completedInvoices',
                'billers',
                'destinations',
                'invoiceNos'
            )
        );
    }


    function companyRateAndExpectedRevenue($selectedColumn, $alias) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT SUM('.$selectedColumn.') AS '.$alias.' FROM tbl_kaya_trips WHERE tracker >= 5 ORDER BY trip_id ASC'
            )
        );
        return $query;
    }

    function specificPayment($columnName, $alias, $columnClause) {
        $payment = DB::SELECT(
            DB::RAW(
                'SELECT SUM('.$columnName.') AS '.$alias.' FROM tbl_kaya_trip_payments WHERE '.$columnClause.'= TRUE'
            )
        );
        return $payment;

    }

    function topThree($foreign_id, $foreign_value, $alias, $tableName) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT '.$foreign_id.', '.$foreign_value.', COUNT('.$foreign_id.') AS '.$alias.' FROM tbl_kaya_trips a JOIN '.$tableName.' b ON a.'.$foreign_id.' = b.id WHERE a.tracker >= 5 GROUP BY '.$foreign_id.' ORDER BY '.$alias.' DESC LIMIT 3'
            )
        );
        return $query;
    }

    function trippayment($arrayRecord, $master, $field, $checker) {
        foreach($arrayRecord as $payment) {
            if(($payment->trip_id == $master->id) && ($payment->$checker == TRUE)) {
                return $answer = $payment->$field;
            }
        }
    }

    function exceptionRemarks($arrayRecord, $master, $field) {
        foreach($arrayRecord as $object) {
            if($object->trip_id == $master->id) {
                return $object->$field;
            }
        }
    }

    function totalPayout($arrayRecord, $master, $advance, $balance) {
        $checkone = 0.00;
        $checktwo = 0.00;
        foreach($arrayRecord as $payment) {
            if($payment->trip_id === $master->id) {
                if($payment->advance_paid == true && $payment->balance_paid == false){
                    return '&#x20a6;'.number_format($calculate = $payment->$advance, 2);
                }
                elseif($payment->advance_paid == true && $payment->balance_paid == true){
                    $calculate = $payment->$advance + $payment->$balance;
                    return '&#x20a6;'.number_format($calculate = $payment->$advance + $payment->balance, 2); 
                }
                // else{
                //     return '&#x20a6;'.number_format($calculate = $checkone + $checktwo, 2);
                // }
            }
        }
    }
}


