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

class financialController extends Controller
{
    public function financialsOverview(){
        $allTrips = trip::SELECT('id', 'trip_id')->ORDERBY('trip_id', 'DESC')->WHERE('tracker', '>=', 5)->GET();
        $current_month = date('F');
        $current_year = date('Y');
        $tripCounts = trip::WHERE('gated_out', '!=', '')->WHERE('trip_status', '!=', 0)->GET()->COUNT();
        $availableCargo = DB::SELECT(
            DB::RAW(
                'SELECT SUM(available_order) AS total_order FROM `tbl_kaya_cargo_availabilities`'
            )
        );

        
        
        $monthlyTarget = target::WHERE('current_month', $current_month)
            ->WHERE('current_year', $current_year)->LIMIT(1)
            ->GET();
        $getGatedOutByMonth = trip::WHERE('month', $current_month)
            ->WHERE('year', $current_year)
            ->WHERE('tracker', '>=', 5
            )->GET()
            ->COUNT();
        $currentTrip = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, a.exact_location_id, a.tracker, b.truck_no, a.transporter_rate, d.product, e.* FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_products d join `tbl_kaya_trip_payments` e ON a.`truck_id` = b.id  AND a.`product_id` = d.`id` AND a.id = e.trip_id ORDER BY a.trip_id DESC LIMIT 1'
            )
        );
        if(sizeof($currentTrip)>0){
            $trip_id = $currentTrip[0]->trip_id;
        }
        else{
            $trip_id = '';
        }
        $tripWaybills = tripWaybill::SELECT('sales_order_no', 'invoice_no', 'invoice_status')
        ->WHERE('trip_id', $trip_id)
        ->GET();
        
        $topThreeProducts = $this->topThree(
            'product_id', 
            'product', 
            'products', 
            'tbl_kaya_products'
        );
        $threeDestination = DB::SELECT(
                DB::RAW(
                    'SELECT exact_location_id, COUNT(exact_location_id) as locations FROM tbl_kaya_trips WHERE tracker >= 5 GROUP BY exact_location_id ORDER BY locations DESC LIMIT 3'
                )
            );
        $threeLoadingSites = $this->topThree(
            'loading_site_id', 
            'loading_site', 
            'sites', 
            'tbl_kaya_loading_sites'
        );
        
        $expectedRevenue = $this->companyRateAndExpectedRevenue('client_rate', 'expectedrevenue');
        $expectedCompanyRate = $this->companyRateAndExpectedRevenue('transporter_rate', 'company_rate');

        $actualPaymentsToTransporters = DB::SELECT(
            DB::RAW(
                'SELECT SUM(b.advance) AS advancePayment, SUM(b.balance) AS balancePayment FROM tbl_kaya_trips a JOIN tbl_kaya_trip_payments b ON a.id = b.trip_id WHERE a.tracker >= 5 AND b.advance_paid = TRUE'
            )
        );

        $offloadedButNotInvoiced = DB::SELECT(
            DB::RAW(
                'SELECT count(invoice_status) as completed_not_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b ON a.`id` = b.trip_id WHERE tracker  = 8 AND invoice_status = false'
            )
        );
        $invoicedTrips;
        $onJourney = trip::WHERE('tracker', '>=', 5)->WHERE('tracker', '<=', 6)->GET()->COUNT();
        $atDestination = trip::WHERE('tracker', 7)->GET()->COUNT();
        $offloadedTrips = trip::WHERE('tracker', 8)->GET()->COUNT();
        $totalTons = DB::SELECT(
            DB::RAW(
                'SELECT SUM(c.tonnage) AS tons_in_total FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_truck_types c ON a.truck_id = b.id AND b.truck_type_id = c.id'
            )
        );

        $paymentRequest = tripPayment::WHERE('advance_paid', FALSE)->ORWHERE('balance_paid', FALSE)->GET()->COUNT();
        $clients = client::WHERE('client_status', '1')->GET()->COUNT();
    
        $totalInvoiced = DB::SELECT(
            DB::RAW(
                'SELECT SUM(b.client_rate) as totalInvoicedAmount FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b ON a.trip_id = b.id'
            )
        );

        $numberofinvoiced = DB::SELECT(
            DB::RAW(
                'SELECT count(a.trip_id) as totalInvoiced FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id'
            )
        );
        $numberofdailygatedout = trip::WHEREDATE('gated_out', date('Y-m-d'))->GET()->COUNT();
        $gatedOutForTheMonth = trip::WHERE('month', $current_month)->WHERE('year', $current_year)->WHERE('tracker', '>=', 5)->GET()->COUNT();



       

        $totalamountofinvoicedbutnotpaid = DB::SELECT(
            DB::RAW(
                'SELECT SUM(b.client_rate) as invoicedNotPaid FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b ON a.trip_id = b.id WHERE a.paid_status = FALSE '
            )
        );

        $notinvoiced = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) as notInvoiced FROM tbl_kaya_trips  WHERE tracker >=5 AND tracker < 8'
            )
        );

        $valueofcompletedbutnotinvoiced = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) as completed_value_not_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b  ON a.id = b.trip_id WHERE tracker = 8 AND invoice_status = false'
            )
        );

        $totalAdvance = $this->specificPayment('advance', 'totalAdvancePaid', 'advance_paid');
        $totalBalance = $this->specificPayment('balance', 'totalBalancePaid', 'balance_paid');

        $actualPayments = $totalBalance[0]->totalBalancePaid + $totalAdvance[0]->totalAdvancePaid;


        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        foreach($allLoadingSites as $loadingSite){
            $countDailyTripByLoadingSite[] = trip::WHERE('loading_site_id', $loadingSite->id)->WHEREDATE('gated_out', date('Y-m-d'))->WHERE('tracker', '>=', 5)->GET()->COUNT();
            $loading_sites[] = $loadingSite->loading_site;
        }

        return view('finance.financials.overview', compact('allTrips', 'totalAdvance', 'totalBalance', 'actualPayments', 'valueofcompletedbutnotinvoiced', 'notinvoiced', 'totalamountofinvoicedbutnotpaid', 'gatedOutForTheMonth', 'numberofdailygatedout', 'numberofinvoiced', 'totalInvoiced', 'totalTons', 'offloadedTrips', 'atDestination', 'onJourney', 'offloadedButNotInvoiced', 'actualPaymentsToTransporters', 'expectedCompanyRate', 'expectedRevenue', 'threeLoadingSites', 'threeDestination', 'topThree251', 'tripWaybills', 'currentTrip', 'getGatedOutByMonth', 'monthlyTarget', 'availableCargo', 'tripCounts', 'allTrips'));
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

/*

        $allTrips = trip::SELECT('id', 'trip_id')->ORDERBY('trip_id', 'DESC')->WHERE('tracker', '>=', 5)->GET();
        $current_month = date('F');
        $current_year = date('Y');
        $tripCounts = trip::WHERE('tracker', '>=', 5)->GET()->COUNT();
        $availableCargo = DB::SELECT(
            DB::RAW(
                'SELECT SUM(available_order) AS total_order FROM `tbl_kaya_cargo_availabilities`'
            )
        );

        
        $monthlyTarget = target::WHERE('current_month', $current_month)
            ->WHERE('current_year', $current_year)->LIMIT(1)
            ->GET();
        $getGatedOutByMonth = trip::WHERE('month', $current_month)
            ->WHERE('year', $current_year)
            ->WHERE('tracker', '>=', 5
            )->GET()
            ->COUNT();
        $currentTrip = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, a.exact_location_id, a.tracker, b.truck_no, a.transporter_rate, d.product, e.* FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_products d join `tbl_kaya_trip_payments` e ON a.`truck_id` = b.id  AND a.`product_id` = d.`id` AND a.id = e.trip_id ORDER BY a.trip_id DESC LIMIT 1'
            )
        );
        if(sizeof($currentTrip)>0){
            $trip_id = $currentTrip[0]->trip_id;
        }
        else{
            $trip_id = '';
        }
        $tripWaybills = tripWaybill::SELECT('sales_order_no', 'invoice_no', 'invoice_status')
        ->WHERE('trip_id', $trip_id)
        ->GET();
        
        $topThreeProducts = $this->topThree(
            'product_id', 
            'product', 
            'products', 
            'tbl_kaya_products'
        );
        $threeDestination = DB::SELECT(
                DB::RAW(
                    'SELECT exact_location_id, COUNT(exact_location_id) as locations FROM tbl_kaya_trips WHERE tracker >= 5 GROUP BY exact_location_id ORDER BY locations DESC LIMIT 3'
                )
            );
        $threeLoadingSites = $this->topThree(
            'loading_site_id', 
            'loading_site', 
            'sites', 
            'tbl_kaya_loading_sites'
        );
        
        $expectedRevenue = $this->companyRateAndExpectedRevenue('client_rate', 'expectedrevenue');
        $expectedCompanyRate = $this->companyRateAndExpectedRevenue('transporter_rate', 'company_rate');

        $actualPaymentsToTransporters = DB::SELECT(
            DB::RAW(
                'SELECT SUM(b.advance) AS advancePayment, SUM(b.balance) AS balancePayment FROM tbl_kaya_trips a JOIN tbl_kaya_trip_payments b ON a.id = b.trip_id WHERE a.tracker >= 5 AND b.advance_paid = TRUE'
            )
        );

        $offloadedButNotInvoiced = DB::SELECT(
            DB::RAW(
                'SELECT count(invoice_status) as completed_not_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b ON a.`id` = b.trip_id WHERE tracker  = 8 AND invoice_status = false'
            )
        );
        $invoicedTrips;
        $onJourney = trip::WHERE('tracker', '>=', 5)->WHERE('tracker', '<=', 6)->GET()->COUNT();
        $atDestination = trip::WHERE('tracker', 7)->GET()->COUNT();
        $offloadedTrips = trip::WHERE('tracker', 8)->GET()->COUNT();
        $totalTons = DB::SELECT(
            DB::RAW(
                'SELECT SUM(c.tonnage) AS tons_in_total FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_truck_types c ON a.truck_id = b.id AND b.truck_type_id = c.id'
            )
        );

        $paymentRequest = tripPayment::WHERE('advance_paid', FALSE)->ORWHERE('balance_paid', FALSE)->GET()->COUNT();
        $clients = client::WHERE('client_status', '1')->GET()->COUNT();
        Session::put([
            'payment_request' => $paymentRequest,
            'on_journey' => $onJourney,
            'client' => $clients,
            'offloaded_trips' => $offloadedTrips
        ]);

        $totalInvoiced = DB::SELECT(
            DB::RAW(
                'SELECT SUM(b.client_rate) as totalInvoicedAmount FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b ON a.trip_id = b.id'
            )
        );

        $numberofinvoiced = DB::SELECT(
            DB::RAW(
                'SELECT count(a.trip_id) as totalInvoiced FROM tbl_kaya_trips a JOIN tbl_kaya_complete_invoices b ON a.id = b.trip_id'
            )
        );
        $numberofdailygatedout = trip::WHEREDATE('gated_out', date('Y-m-d'))->GET()->COUNT();
        $gatedOutForTheMonth = trip::WHERE('month', $current_month)->WHERE('year', $current_year)->WHERE('tracker', '>=', 5)->GET()->COUNT();

        $lastOneWeek = date('Y-m-d', strtotime('-7 days'));
        $currentDate = date('Y-m-d');

        //$noOfGatedOutTripForCurrentWeek = trip::WHEREBETWEEN('gated_out', ["'.$lastOneWeek.'", "'.$currentDate.'"])->WHERE('tracker', '>=', 5)->GET()->COUNT();

        $noOfGatedOutTripForCurrentWeek = DB::SELECT(
            DB::RAW(
                'select COUNT(*) as weeklygateout  from tbl_kaya_trips where Date(gated_out) between "'.$lastOneWeek.'" and "'.$currentDate.'" and tracker >= 5'
            )
        );
        $noOfGatedOutTripForCurrentWeek;

        $totalamountofinvoicedbutnotpaid = DB::SELECT(
            DB::RAW(
                'SELECT SUM(b.client_rate) as invoicedNotPaid FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b ON a.trip_id = b.id WHERE a.paid_status = FALSE '
            )
        );

        $notinvoiced = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) as notInvoiced FROM tbl_kaya_trips  WHERE tracker >=5 AND tracker < 8'
            )
        );

        $valueofcompletedbutnotinvoiced = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) as completed_value_not_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b  ON a.id = b.trip_id WHERE tracker = 8 AND invoice_status = false'
            )
        );

        $totalAdvance = $this->specificPayment('advance', 'totalAdvancePaid', 'advance_paid');
        $totalBalance = $this->specificPayment('balance', 'totalBalancePaid', 'balance_paid');

        $actualPayments = $totalBalance[0]->totalBalancePaid + $totalAdvance[0]->totalAdvancePaid;


        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        foreach($allLoadingSites as $loadingSite){
            $countDailyTripByLoadingSite[] = trip::WHERE('loading_site_id', $loadingSite->id)->WHEREDATE('gated_out', date('Y-m-d'))->WHERE('tracker', '>=', 5)->GET()->COUNT();
            $loading_sites[] = $loadingSite->loading_site;
        }


*/

