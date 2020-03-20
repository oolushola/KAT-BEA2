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

        return view('finance.financials.overview', compact('allTrips', 'totalAdvance', 'totalBalance', 'actualPayments', 'valueofcompletedbutnotinvoiced', 'notinvoiced', 'totalamountofinvoicedbutnotpaid', 'gatedOutForTheMonth', 'numberofdailygatedout', 'numberofinvoiced', 'totalInvoiced', 'totalTons', 'offloadedTrips', 'atDestination', 'onJourney', 'offloadedButNotInvoiced', 'actualPaymentsToTransporters', 'expectedCompanyRate', 'expectedRevenue', 'threeLoadingSites', 'threeDestination', 'topThreeProducts', 'tripWaybills', 'currentTrip', 'getGatedOutByMonth', 'monthlyTarget', 'availableCargo', 'tripCounts', 'allTrips'));
    }

    public function displayFinancialRecords() {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        $loadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE tracker <> \'0\' ORDER BY a.trip_id DESC LIMIT 200'
            )
        );
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();
        $clientRates = DB::SELECT(
            DB::RAW(
                'SELECT a.client_id, a.amount_rate, b.* FROM `tbl_kaya_client_fare_rates` a LEFT JOIN `tbl_kaya_transporter_rates` b ON a.from_state_id = b.transporter_from_state_id AND a.to_state_id = b.transporter_to_state_id AND a.destination = b.transporter_destination'
            )
        );
        $trippayments = tripPayment::GET();
        $products = product::SELECT('id', 'product')->ORDERBY('product')->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );

        $totalRevenue = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) AS totalRevenue FROM tbl_kaya_trips WHERE tracker >=5'
            )
        );

        $transporterRate = DB::SELECT(
            DB::RAW(
                'SELECT SUM(transporter_rate) AS totalTransporterRate FROM tbl_kaya_trips WHERE tracker >=5'
            )
        );

        


        $invoiceCriteria = completeInvoice::GET();

        return view('finance.financials.view',
            compact(
                'orders',
                'tripWaybills',
                'tripEvents',
                'waybillstatuses',
                'clientRates',
                'trippayments',
                'clients',
                'transporters',
                'loadingSites',
                'products',
                'states',
                'invoiceCriteria',
                'totalRevenue',
                'transporterRate'
            )
        );
    }

    public function getExtremeWaybill(Request $request) {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        $loadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' ORDER BY a.trip_id DESC'
            )
        );
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();
        $clientRates = DB::SELECT(
            DB::RAW(
                'SELECT a.client_id, a.amount_rate, b.* FROM `tbl_kaya_client_fare_rates` a LEFT JOIN `tbl_kaya_transporter_rates` b ON a.from_state_id = b.transporter_from_state_id AND a.to_state_id = b.transporter_to_state_id AND a.destination = b.transporter_destination'
            )
        );
        $trippayments = tripPayment::GET();
        $products = product::SELECT('id', 'product')->ORDERBY('product')->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $invoiceCriteria = completeInvoice::GET();

        $response = '<table class="table table-bordered">
            <thead class="table-info">
                
                <tr class="font-weigth-semibold" style="font-size:11px; background:#000; color:#eee; ">
                    <th>SN</th>
                    <th class="text-center">KAID</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th>DESTINATION</th>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO.</th>
                    <th>PRODUCT</th>
                    <th>CUSTOMER</th>
                    <th>CURRENT STAGE</th>
                    <th>WAYBILL STATUS</th>
                    <th>DAYS SINCE</th>
                    <th>TRANSPORTER</th>
                </tr>
            </thead>
            <tbody id="masterDataTable">';
                $counter = 0;
                
            
                if(count($orders)){
                    foreach($orders as $trip){
                        if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                        if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                        if($trip->tracker == 3){ $current_stage = 'LOADING';}
                        if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                        if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                        if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                        if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                        if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                        
                        if($trip->gated_out != '') {
                            if(count($waybillstatuses)){
                                foreach($waybillstatuses as $waybillChecker){
                                    // if($waybillChecker->trip_id == $trip->id && $waybillChecker->waybill_status == TRUE){
                                        $now = time();
                                        $gatedout = strtotime($trip->gated_out);;
                                        $datediff = $now - $gatedout;
                                        $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                                        if($numberofdays >=10){
                                            $counter++;
                                            $counter % 2 == 0 ? $css = ' font-weight-semibold ' : $css = 'order-table font-weight-semibold';
                                            $bgcolor = '#FF0000';
                                            $textdescription = $numberofdays.' Days ';

                                            $response.='<tr class="font-weight-semibold" style="font-size:10px; background:'.$bgcolor.'"><td>'.$counter.'</td>';
                                            $response.='<td>'.strtoupper($trip->trip_id).'</td>';
                                                    $response.='<td>'.strtoupper($trip->truck_no).'</td>
                                                    <td>'.strtoupper($trip->exact_location_id).'</td>
                                                    <td>'.$trip->loading_site.'</td>
                                                    <td class="text-center font-weight-semibold">';
                                                        foreach($tripWaybills as $salesNo) {
                                                            if($trip->id == $salesNo->trip_id) {
                                                            $response.=$salesNo->sales_order_no.'<br>
                                                            </a>';
                                                            }
                                                        }
                                                    $response.='</td>
                                                    <td>'.$trip->product.'</td>
                                                    <td>'.strtoupper($trip->customers_name).'</td>
                                                    <td class="font-weight-semibold">'.$current_stage.'</td>';
                                                    
                                                    $response.='<td>';
                                                        foreach($waybillstatuses as $waybillstatus){
                                                            if($waybillstatus->trip_id == $trip->id){
                                                                $response.=$waybillstatus->comment;
                                                            }
                                                        }
                                                    $response.='</td>';
                                                    
                                                    
                                                    $response.='<td class="text-center">'.$textdescription.'</td>                        
                                                    <td>'.strtoupper($trip->transporter_name).'</td>

                                                    </tr>';
                                                }
                                            break;
                                            } 
                                        }
                                    //}
                                    
                                    continue;
                                }
                            }
                        }
                            
                        
                        
            

                        return $response;

                        
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

