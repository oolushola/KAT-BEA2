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

class FinanceSortController extends Controller
{
    public function financePaymentStatus(Request $request) {
        $payment_status = $request->payment_status;
        
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h JOIN tbl_kaya_complete_invoices i ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id AND a.id = i.trip_id WHERE tracker <> 0 AND i.acknowledged = TRUE AND i.paid_status = '.$payment_status.' ORDER BY a.trip_id DESC'
            )
        );
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices);
    }

    public function financeClientDateRange(Request $request) {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $client = $request->client;
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id WHERE tracker <> 0 AND a.client_id = '.$client.' AND date(gated_out) BETWEEN "'.$date_from.'" AND "'.$date_to.'"  ORDER BY a.trip_id DESC'
            )
        );
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices);
    }

    public function financeClientInvoicePayment(Request $request) {
        $payment_status = $request->payment_status;
        $client = $request->client;

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h JOIN tbl_kaya_complete_invoices i ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id AND a.id = i.trip_id WHERE tracker <> 0 AND i.acknowledged = TRUE AND i.paid_status = '.$payment_status.'  AND a.client_id = '.$client.' ORDER BY a.trip_id DESC'
            )
        );
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices);

    }

    public function financeDateRange(Request $request) {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id WHERE tracker <> 0 AND date(gated_out) BETWEEN "'.$date_from.'" AND "'.$date_to.'"  ORDER BY a.trip_id DESC'
            )
        );
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices);
    }

    public function financeInvoice(Request $request) {
        $invoice_no = $request->invoice_no;
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h JOIN tbl_kaya_complete_invoices i ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id AND h.trip_id AND a.id = i.trip_id WHERE tracker <> \'0\' AND i.invoice_no = "'.$invoice_no.'" ORDER BY a.trip_id DESC'
            )
        );
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices);
    }

    public function financeClientLoadingSite(Request $request) {
        $client_id = $request->client;
        $loading_site_id = $request->loading_site;
        $orders = $this->twoCombinationFilter('a.client_id', 'a.loading_site_id', $client_id, $loading_site_id);
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices); 
    }
    
    public function clientAndInvoiceStatus(Request $request) {
        $client_id = $request->client;
        $invoice_status = $request->invoice_status;
        $orders = $this->twoCombinationFilter('a.client_id', 'h.invoice_status', $client_id, $invoice_status);
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices); 
    }

    public function clientDestination(Request $request) {
        $client_id = $request->client;
        $exact_location = $request->destination;
        $orders = $this->twoCombinationFilter('a.client_id', 'a.exact_location_id', $client_id, $exact_location);
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices); 
    }

    public function clientLoadingSiteInvoiceStatus(Request $request) {
        $client_id = $request->client;
        $invoice_status = $request->invoice_status;
        $loading_site_id = $request->loading_site;
        
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id WHERE tracker <> \'0\' AND a.client_id = '.$client_id.' AND a.loading_site_id = '.$loading_site_id.' AND h.invoice_status = '.$invoice_status.' ORDER BY a.trip_id DESC'
            )
        );
        
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices);
        
    }

    public function financeClientInvoiceDateRange(Request $request) {
        $client_id = $request->client;
        $invoice_status = $request->invoice_status;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h JOIN tbl_kaya_complete_invoices i ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id AND a.id = i.trip_id WHERE tracker <> 0 AND h.invoice_status = TRUE AND a.client_id = '.$client_id.' AND DATE(i.created_at) BETWEEN "'.$date_from.'" AND "'.$date_to.'" ORDER BY a.trip_id DESC'
            )
        );
        
        $waybillListings = $this->waybillsProcessor($orders);
        $completedInvoices = $this->invoicesProcessor($orders);
        return $this->tableSorter($orders, $waybillListings, $completedInvoices);
    }

    function tableSorter($orders, $waybills, $completedInvoices) {
        $trippayments = tripPayment::GET();
        $billers = DB::SELECT(
            DB::RAW(
                'SELECT a.client_name, a.invoice_no, b.* FROM tbl_kaya_invoice_biller a JOIN tbl_kaya_complete_invoices b ON a.invoice_no = b.completed_invoice_no '
            )
        );

        $revenue = DB::SELECT(
            DB::RAW(
                'SELECT SUM(client_rate) AS totalRevenue, SUM(transporter_rate) AS totalTransporterRate FROM tbl_kaya_trips WHERE tracker >=5'
            )
        );

        $transporterPayment = DB::SELECT(
            DB::RAW(
                'SELECT SUM(advance) as totaladvancepaid, SUM(balance) AS totalbalancepaid FROM tbl_kaya_trip_payments'
            )
        );

        $totalClientRate = $revenue[0]->totalRevenue;
        $totalTransporterRate = $revenue[0]->totalTransporterRate;
        $totalGrossMargin = $totalClientRate - $totalTransporterRate;
        $averagePercentageMarkup = $totalGrossMargin / $totalTransporterRate * 100;
        $averagePercentageMargin = $totalGrossMargin / $totalClientRate * 100;
        $totalAdvancePaid = $transporterPayment[0]->totaladvancepaid;
        $totalBalancePaid = $transporterPayment[0]->totalbalancepaid;
        
        $response ='<table class="table table-bordered" id="exportTableData">
            <thead class="table-info">
                <tr>
                    <th></th>
                    <th id="totalClientRate" class="bg-primary-400">&#x20a6;'.number_format($totalClientRate, 2).'</th>
                    <th id="totalTransporterRate" class="bg-primary-400">&#x20a6;'.number_format($totalTransporterRate, 2).'</th>
                    <th id="totalGrossMargin" class="bg-primary-400">&#x20a6;'.number_format($totalGrossMargin, 2).'</th>
                    <th id="averagePercentageMarkup" class="bg-primary-400">'.number_format($averagePercentageMarkup, 2).'%</th>
                    <th id="averagePercentageMargin" class="bg-primary-400">'.number_format($averagePercentageMargin, 2).'%</th>
                    <th id="totalAdvancePaid">&#x20a6;'.number_format($totalAdvancePaid, 2).'</th>
                    <th id="totalBalancePaid">&#x20a6;'.number_format($totalBalancePaid).'</th>
                    <th id="totalAmountPaid">&#x20a6;'.number_format($totalAdvancePaid + $totalBalancePaid, 2).'</th>
                    <th colspan="15"></th>
                </tr>
                <tr class="font-weigth-semibold" style="font-size:11px; background:#000; color:#eee; ">
                    <th class="text-center headcol">KAID</th>
                    <th class="text-center">CLIENT RATE</th>
                    <th class="text-center">TRANSPORTER RATE</th>
                    <th>GROSS MARGIN</th>
                    <th>% MARKUP</th>
                    <th>% MARGIN</th>
                    <th class="text-center bg-warning-400">ADVANCE</th>
                    <th class="text-center bg-warning-400">BALANCE</th>
                    <th class="text-center text-center bg-warning-400">TOTAL</th>
                    <th class="bg-warning-400">REMARK</th>
                    <th>INVOICE STATUS</th>
                    <th>DATE INVOICE</th>
                    <th class="text-center text-center">INVOICE NO</th>
                    <th class="text-center text-center">BILLED TO</th>
                    <th>DATE PAID</th>                    
                    <th class="text-center">WAYBILL DETAILS</th>
                    <th>CUSTOMER</th>
                    <th>CURRENT STAGE</th>
                    <th>WAYBILL STATUS</th>
                    <th>TRANSPORTER</th>
                </tr>
            </thead>';
            $response.='<tbody id="masterDataTable">';
                    $counter = 0;
                    $gatedOutCounter = 0;
                    $totalClientRate = 0;
                    $totalTransporterRate = 0;
                    $totalGrossMargin = 0;
                    $averagePercentageMarkup = 0.0;
                    $averagePercentageMargin = 0.0;
                    $totalAdvancePaid = 0;
                    $totalBalancePaid = 0;
                    $totalAmountPaid = 0;
                    $totalClientRateFiltered = 0;
                    $totalTransporterRateFiltered = 0;
                
                if(count($orders)) {
                    foreach($orders as $trip) {
                    $counter++;
                    $counter % 2 == 0 ? $css = ' font-weight-semibold ' : $css = 'order-table font-weight-semibold';
                        if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                        if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                        if($trip->tracker == 3){ $current_stage = 'LOADING';}
                        if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                        if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                        if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                        if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                        if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                        
                    $response.='<tr class="'.$css.' hover" style="font-size:10px;">
                        <td class="text-center">'.$trip->trip_id.'
                            <span class="d-block text-danger">'.strtoupper($trip->truck_no).'</span>
                            <span class="d-block text-primary">'.strtoupper($trip->loading_site).'</span>
                        </td>
                        <td class="text-center font-weight-semibold clientRate" value="'.$trip->trip_id.'">
                            <span id="defaultClientRate'.$trip->trip_id.'">'.number_format($trip->client_rate, 2).'</span>
                            <span id="changeClientRate'.$trip->trip_id.'" class="hidden">
                                <input type="text" class="updateClientRate" value="'.$trip->client_rate.'" title="'.$trip->trip_id.'" id="clientRate'.$trip->trip_id.'Value" />
                            </span>
                            <span id="clientRate'.$trip->trip_id.'Loader"></span>
                        </td>

                        <td class="text-center font-weight-semibold transporterRate" value="'.$trip->trip_id.'">
                            <span id="defaultTransporterRate'.$trip->trip_id.'">'.number_format($trip->transporter_rate, 2).'</span>
                            <span id="changeTransporterRate'.$trip->trip_id.'" class="hidden">
                                <input type="text" class="updateTransporterRate" value="'.$trip->transporter_rate.'" title="'.$trip->trip_id.'" id="transporterRate'.$trip->trip_id.'Value" />
                            </span>
                            <span id="transporterRate'.$trip->trip_id.'Loader"></span>
                        </td>

                        <td class="text-center font-weight-semibold">'.number_format($trip->client_rate - $trip->transporter_rate, 2).'</td>
                        <td class="text-center font-weight-semibold">';
                            if(isset($trip->transporter_rate) && $trip->transporter_rate != 0) {
                                $grossMargin = $trip->client_rate - $trip->transporter_rate;
                                $percentageMarkUp = ($grossMargin / $trip->transporter_rate) * 100;
                            } 
                            else {
                                $percentageMarkUp = 0;
                            }
                        
                            $response.= number_format($percentageMarkUp, 2).'%'; 
                        $response.='</td>

                        <td class="text-center font-weight-semibold">'; 
                             
                            if(isset($trip->client_rate) && $trip->client_rate != 0){
                                $grossMargin = $trip->client_rate - $trip->transporter_rate;
                                $percentageMargin = ($grossMargin / $trip->client_rate) * 100;
                            } else {
                                $percentageMargin = 0;
                            }
                            
                            $response.= number_format($percentageMargin, 2).'%';
                        $response.='</td>

                        <td class="text-center font-weight-semibold">';
                                $advance = $this->trippayment($trippayments, $trip, 'advance', 'advance_paid');
                                if(isset($advance)) { $response.= '&#x20a6;'.number_format($advance, 2); } else { $response.='';}
                        $response.='</td>
                        <td class="text-center font-weight-semibold">';
                            $balance = $this->trippayment($trippayments, $trip, 'balance', 'balance_paid');
                            if(isset($balance)) { $response.='&#x20a6;'.number_format($balance, 2); } else { $response.=''; }
                        $response.='</td>
                        <td class="text-center font-weight-semibold">';
                            $response.= $this->totalPayout($trippayments, $trip, 'advance', 'balance');
                        $response.='</td>
                        <td class="font-weight-semibold">';
                            $response.= $this->exceptionRemarks($trippayments, $trip, 'remark');
                        $response.='</td>
                        <td class="text-center">';
                            if($trip->invoice_status) {
                                $response.='<span class="badge badge-primary">INVOICED</span>';
                            }
                        $response.='</td>';
                        $response.='<td class="text-center">';
                            foreach($completedInvoices as $invoice) {
                                if($invoice->trip_id == $trip->id) {
                                    $response.= date('d-m-Y', strtotime($invoice->created_at));
                                }
                            }
                        $response.='</td>
                        <td class="text-center">';
                            foreach($completedInvoices as $invoice) {
                                if($invoice->trip_id == $trip->id) {
                                    $response.= $invoice->invoice_no; 
                                }
                            }
                        $response.='</td>
                        <td>';
                            foreach($billers as $biller) {
                                if($biller->trip_id === $trip->id) {
                                     $response.= $biller->client_name;
                                }
                            }
                        $response.='</td>
                        <td class="text-center">';
                            foreach($completedInvoices as $invoice) {
                                if($invoice->trip_id == $trip->id && $invoice->date_paid) {
                                    $response.= date('d-m-Y', strtotime($invoice->date_paid));
                                }
                            }
                        $response.='</td>
                        
                        <td class="text-center font-weight-semibold">';
                            foreach($waybills as $waybill) {
                                if($waybill->trip_id == $trip->id) {
                                    $response.='<span class="d-block">'.$waybill->sales_order_no.' '.$waybill->invoice_no.'</span>';
                                }
                            }
                        $response.='</td>
                        <td>'.strtoupper($trip->customers_name).'
                            <span class="d-block">Destination: '.strtoupper($trip->exact_location_id).'</span>
                            <span class="d-block">Product: '.strtoupper($trip->product).'</span>
                        </td>
                        <td class="font-weight-semibold">'.$current_stage.'</td>';
                        
                        $response.='<td class="text-center">';
                            if($trip->waybill_status == 0) { 
                                $response.= $trip->comment;
                            }
                            else {
                                $response.='<i class="icon icon-checkmark2"></i>';
                            }
                        $response.='</td>
                        <td>'.strtoupper($trip->transporter_name).'</td>';
                        $response.= '</tr>';
                        if($trip->tracker >= 5) {
                            $gatedOutCounter += 1;
                            if($trip->transporter_rate != 0) {
                                $percentageMarkUp = ($grossMargin / $trip->transporter_rate) * 100;
                            } else {
                                $percentageMarkUp = 0;
                            }
                            $gatedOutCounter+=1;
                            if($trip->client_rate != 0){
                                $percentageMargin = ($grossMargin / $trip->client_rate) * 100;
                            } else {
                                $percentageMargin = 0;
                            }
                        }
                        
                        $totalClientRateFiltered += $trip->client_rate;
                        $totalTransporterRateFiltered += $trip->transporter_rate;
                    }
                    $response.='<tr>
                        <td>&nbsp;</td>
                        <td class="font-size-sm font-weight-semibold">Sum: &#x20a6;'.number_format($totalClientRateFiltered * 1.025, 2).'</td>
                        <td class="font-size-sm font-weight-semibold">Sum: &#x20a6;'.number_format($totalTransporterRateFiltered, 2).'</td>
                        <td class="font-size-sm font-weight-semibold">Difference: &#x20a6;'.number_format($totalClientRateFiltered - $totalTransporterRateFiltered, 2).'</td>
                        <td colspan="20"></td>
                    </tr>';
                } else {   
                    $response.='<tr>
                        <td class="table-success" colspan="30">No record found</td>
                    </tr>';
                }  

            $response.='</tbody>
        </table>';

        return $response;
    }

    function waybillsProcessor($trips) {
        foreach($trips as $trip) {
            $waybills[] = tripWaybill::SELECT('trip_id', 'sales_order_no', 'invoice_no')->WHERE('trip_id', $trip->id)->GET();
        }

        $waybills = [];
        $waybillListings = [];
        foreach($waybills as $tripWaybills){
            foreach($tripWaybills as $waybill){
                $waybillListings[] = $waybill;
            }
        }
        return $waybillListings;
    }

    function invoicesProcessor($trips) {
        if(count($trips)) {
            foreach($trips as $specificTrip){
                $completeInvoiceListings[] = completeInvoice::WHERE('trip_id', $specificTrip->id)->ORDERBY('invoice_no', 'ASC')->GET();
            }
        }
        else {
            $completeInvoiceListings = [];
        }

        $completedInvoices = [];
        if(count($completeInvoiceListings)) {
            foreach($completeInvoiceListings as $completedInvoicing) {
                foreach($completedInvoicing as $invoices) {
                    $completedInvoices[] = $invoices;
                }
            }
        }
        else{
            $completeInvoiceListings = [];
        }

        return $completedInvoices;
    }


    function grossMargin($arrayRecord, $master, $field) {
        foreach($arrayRecord as $object) {
            if(($master->client_id == $object->client_id) && ($master->exact_location_id == $object->id)) {
                return number_format($object->amount_rate - $object->transporter_amount_rate, 2);
            }
        }
    }

    
    function trippayment($arrayRecord, $master, $field, $checker) {
        foreach($arrayRecord as $payment) {
            if(($payment->trip_id == $master->id) && ($payment->$checker == TRUE)) {
                return $answer = $payment->$field;
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

    function twoCombinationFilter($fieldOne, $fieldTwo, $paramsOne, $paramsTwo) {
        $twoCombination = DB::SELECT(
            DB::RAW(
                'SELECT  a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.waybill_status, h.comment, h.invoice_status, h.date_invoiced FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_trip_waybill_statuses h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.id = h.trip_id WHERE tracker <> \'0\' AND '.$fieldOne.' = '.$paramsOne.' AND '.$fieldTwo.' = "'.$paramsTwo.'" ORDER BY a.trip_id DESC'
            )
        );
        return $twoCombination;
    }
}














