<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\trip;
use App\tripWaybill;
use App\client;
use App\transporter;

class FinancialReportController extends Controller
{
    private $currentDate;

    public function financialReporting() {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        return view('finance.report.report', compact('clients', 'transporters'));
    }

    public function waybillStatus(Request $request) {
        $currentDate = date('Y-m-d H:i:s');
        $type = strtoupper($request->v);
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, truck_no, loading_site, transporter_name, exact_location_id, invoice_status, gated_out, DATEDIFF("'.$currentDate.'", a.gated_out) as gated_out_since, client_rate, transporter_rate, IFNULL(f.amount, 0) as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b JOIN tbl_kaya_trucks c JOIN tbl_kaya_loading_sites d JOIN tbl_kaya_transporters e ON a.id = b.trip_id AND a.truck_id = c.id AND a.loading_site_id = d.id AND a.transporter_id = e.id LEFT JOIN tbl_kaya_trip_incentives f ON a.id = f.trip_id WHERE a.tracker = \'8\' AND b.invoice_status = FALSE' 
            )
        );
        return $this->responseLogger($trips, $type);
    }

    public function unpaidInvoices(Request $request) {
        $type = strtoupper($request->v);
        $client_id = $request->payload;
        if($client_id == 'all') {
            $trips = DB::SELECT(
                DB::RAW(
                    'SELECT a.id, a.trip_id, truck_no, loading_site, exact_location_id, client_rate, amount_paid, transporter_rate, invoice_no, d.created_at, d.date_paid, company_name, IFNULL(f.amount, 0) as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_complete_invoices d JOIN tbl_kaya_clients e ON a.truck_id = b.id AND a.loading_site_id = c.id AND a.id = d.trip_id AND e.id = a.client_id LEFT JOIN tbl_kaya_trip_incentives f ON a.id = f.trip_id WHERE date_paid IS NULL AND tracker >= 5 AND trip_status = TRUE ORDER BY invoice_no ASC' 
                )
            ); 
        }
        else {
            $trips = DB::SELECT(
                DB::RAW(
                    'SELECT a.id, a.trip_id, truck_no, loading_site, exact_location_id, client_rate, amount_paid, transporter_rate, invoice_no, d.created_at, d.date_paid, company_name, IFNULL(f.amount, 0) as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_complete_invoices d JOIN tbl_kaya_clients e ON a.truck_id = b.id AND a.loading_site_id = c.id AND a.id = d.trip_id AND e.id = a.client_id LEFT JOIN tbl_kaya_trip_incentives f ON a.id = f.trip_id WHERE date_paid IS NULL AND tracker >= 5 AND trip_status = TRUE AND client_id = "'.$client_id.'" ORDER BY invoice_no ASC' 
                )
            );
        }
        return $this->secondResponseLogger($trips, $type);
    }

    public function paidInvoices(Request $request) {
        $payloads = json_decode($request->payload); 
        $date_from = $payloads->pi_date_from;
        $date_to = $payloads->pi_date_to;
        $client_id = $payloads->pi_client_id;
        if($client_id != 0) {
            $qExtension = 'AND client_id = '.$client_id.'';
        }
        else{ 
            $qExtension = '';
        }
        $type = strtoupper($request->v);
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, truck_no, loading_site, exact_location_id, client_rate, amount_paid, transporter_rate, invoice_no, d.created_at, d.date_paid, company_name, IFNULL(f.amount, 0) as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_complete_invoices d JOIN tbl_kaya_clients e ON a.truck_id = b.id AND a.loading_site_id = c.id AND a.id = d.trip_id AND e.id = a.client_id LEFT JOIN tbl_kaya_trip_incentives f ON a.id = f.trip_id WHERE date_paid AND tracker >= 5 AND trip_status = TRUE AND date_paid BETWEEN "'.$date_from.'" AND "'.$date_to.'" '.$qExtension.' ORDER BY invoice_no ASC' 
            )
        );
        return $this->secondResponseLogger($trips, $type);
    }

    public function uninvoicedTrips(Request $request) {
        $payloads = json_decode($request->payload);
        $status = $payloads->tracker;
        $clientId = $payloads->clientId;
        if($clientId != 0) {
            $qExtension = ' AND a.client_id = '.$clientId.'';
        }
        else{
            $qExtension = '';
        }
        
        if($status == 'all') { $tracker = '5 AND 8'; }
        if($status == 6) { $tracker = '5 AND 6'; }
        if($status == 7) { $tracker = '7 AND 7'; }
        if($status == 8) { $tracker = '8 AND 8'; }
        $currentDate = date('Y-m-d H:i:s');
        $type = strtoupper($request->v);
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, truck_no, loading_site, transporter_name, exact_location_id, invoice_status, gated_out, DATEDIFF("'.$currentDate.'", a.gated_out) as gated_out_since, client_rate, transporter_rate, IFNULL(f.amount, 0) as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b JOIN tbl_kaya_trucks c JOIN tbl_kaya_loading_sites d JOIN tbl_kaya_transporters e ON a.id = b.trip_id AND a.truck_id = c.id AND a.loading_site_id = d.id AND a.transporter_id = e.id LEFT JOIN tbl_kaya_trip_incentives f ON a.id = f.trip_id WHERE a.tracker BETWEEN '.$tracker.' AND b.invoice_status = FALSE '.$qExtension.'' 
            )
        );
        return $this->responseLogger($trips, $type);
    }

    public function invoicedTrips(Request $request) {
        $payloads = json_decode($request->payload);
        $invoice_date_from = $payloads->invoice_date_from;
        $invoice_date_to = $payloads->invoice_date_to;
        $client_id = $payloads->client;
        if($client_id != 0) {
            $clause = 'BETWEEN "'.$invoice_date_from.'" AND "'.$invoice_date_to.'" AND a.client_id = "'.$client_id.'" ';
        }
        else{
            $clause = 'BETWEEN "'.$invoice_date_from.'" AND "'.$invoice_date_to.'" ';
        }
        $type = strtoupper($request->v);
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, truck_no, loading_site, exact_location_id, client_rate, amount_paid, transporter_rate, invoice_no, d.created_at, d.date_paid, company_name, IFNULL(f.amount, 0) as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_complete_invoices d JOIN tbl_kaya_clients e ON a.truck_id = b.id AND a.loading_site_id = c.id AND a.id = d.trip_id AND e.id = a.client_id LEFT JOIN tbl_kaya_trip_incentives f ON a.id = f.trip_id WHERE tracker >= 5 AND trip_status = TRUE AND d.created_at '.$clause.' ORDER BY invoice_no ASC' 
            )
        );
        return $this->secondResponseLogger($trips, $type);
    }

    public function transporterAccount(Request $request) {
        $payloads = json_decode($request->payload);
        $type = strtoupper($request->v);
        $transporter_id = $payloads->transporter;
        $date_from = $payloads->transporter_date_from;
        $date_to = $payloads->transporter_date_to;
        if(!$date_from && !$date_to) {
            $clause = 'a.transporter_id = "'.$transporter_id.'"';
        }
        else {
            $clause = 'a.transporter_id = "'.$transporter_id.'" AND gated_out BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
        }
        $trips = $this->paymentDetails('WHERE tracker >= 5 AND trip_status = TRUE AND '.$clause.'');
        return $this->paymentDetailsResponse($trips, $type);
    }

    public function outstandingBills(Request $request) {
        $type = $request->v;
        $trips = $this->paymentDetails('WHERE tracker >= 5 AND trip_status = TRUE AND outstanding_balance > 0');
        return $this->paymentDetailsResponse($trips, $type);
    }

    public function tripSearch(Request $request) {
        $payloads = json_decode($request->payload);
        $search = strtoupper($payloads->search);
        $from = $payloads->from;
        $to = $payloads->to;
        if($from != '' && $to != '') {
            $qExtension = 'a.gated_out BETWEEN "'.$from.'" AND "'.$to.'"';
        }
        else {
            $qExtension = 'a.trip_id LIKE "'.$search.'%"';
        }
        $type = $request->v;
        $trips = $this->paymentDetails('WHERE '.$qExtension.'');
        return $this->paymentDetailsResponse($trips, $search);
    }

    function paymentDetails($clause) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.id, e.advance_paid, e.balance_paid, a.trip_id, truck_no, loading_site, exact_location_id, gated_out, client_rate, transporter_rate, transporter_name, e.advance, e.balance, e.outstanding_balance, e.remark, IFNULL(f.amount, 0) as incentive FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_transporters d ON a.truck_id = b.id AND a.loading_site_id = c.id AND a.`transporter_id` = d.id LEFT JOIN `tbl_kaya_trip_payments` e ON e.trip_id = a.id LEFT JOIN tbl_kaya_trip_incentives f ON a.id = f.trip_id '.$clause.' '
            )
        );
        return $query;
    }

    function responseLogger($trips, $selectedModule) {
       $waybills = $this->waybillsLog($trips);
       $response ='
        <table class="table table-striped">
            <thead style="white-space: nowrap;margin:0;">
                <tr class="font-weight-bold font-size-xs font-weight-bold">
                    <th colspan="15">Total: '.count($trips).' Trips | 
                        <span class="text-success pointer" id="exportBtn">EXPORT <i class="icon-cloud-download2"></i></span>
                    </th>
                </tr>
                <tr class="font-size-xs bg-primary-400">
                    <th class="text-center headcol">TRIP ID</th>
                    <th class="text-center">TRUCK NO</th>
                    <th class="text-center">DESTINATION</th>
                    <th class="text-center">SO NO.</th>
                    <th class="text-center">WAYBILL NO.</th>
                    <th class="text-center">LOADING SITE</th>
                    <th class="text-center">GATED OUT</th>
                    <th class="text-center bg-danger-400">SINCE</th>
                    <th>TRANSPORTER</th>
                    <th class="text-center">CLIENT RATE</th>
                    <th class="text-center">INCENTIVE</th>
                    <th class="text-center">TRANSPORTER RATE</th>
                    <th class="text-center">MARGIN</th>
                </tr>
            </thead>
            <tbody class="font-size-xs">';
                if(count($trips)) {
                    foreach($trips as $trip) {
                        $response.='
                            <tr style="white-space: nowrap;margin:0;">
                                <td class="text-center">'.$trip->trip_id.'</td>
                                <td class="text-center">'.$trip->truck_no.'</td>
                                <td class="text-center">'.strtoupper($trip->exact_location_id).'</td>';
                                $response.='
                                <td class="text-center">';
                                foreach($waybills as $key=> $waybillInfo) {
                                    if($waybillInfo->trip_id === $trip->id) {
                                        $response.=$waybillInfo->sales_order_no.' ';
                                    }
                                }
                                $response.='</td>
                                <td class="text-center">';
                                foreach($waybills as $key=> $waybillInfo) {
                                    if($waybillInfo->trip_id === $trip->id) {
                                        $response.=$waybillInfo->invoice_no.' ';
                                    }
                                }
                                $response.='</td>';
                                $response.='<td class="text-center">'.$trip->loading_site.'</td>
                                <td class="text-center">'.date('d-m-Y', strtotime($trip->gated_out)).'</td>
                                <td class="bg-danger-400 text-center">'.$trip->gated_out_since.' Days</td>
                                <td>'.$trip->transporter_name.'</td>
                                <td class="text-center">'.number_format($trip->client_rate, 2).'</td>
                                <td class="text-center">'.number_format($trip->incentive, 2).'</td>
                                <td class="text-center">'.number_format($trip->transporter_rate, 2).'</td>
                                <td class="text-center">'.number_format(($trip->client_rate + $trip->incentive ) - $trip->transporter_rate, 2).'</td>
                            </tr>
                        ';
                    }
                }
                else {
                $response.='<tr>
                    <td colspan="15" class="bg-warning-300 text-center font-size-xs text-danger">You do not have any record for '.ucwords($selectedModule).'</td>
                </tr>';
                }
            $response.='
            </tbody>
        </table>';
        return $response;
    }

    function secondResponseLogger($trips, $selectedModule) {
        $waybills = $this->waybillsLog($trips);
        $response ='
        <table class="table table-striped">
            <thead style="white-space: nowrap;margin:0;">
                <tr class="font-weight-bold font-size-xs font-weight-bold">
                    <th colspan="15">Total: '.count($trips).' Trips | 
                        <span class="text-success pointer" id="exportBtn">EXPORT <i class="icon-cloud-download2"></i></span>
                    </th>
                </tr>
                <tr class="font-size-xs bg-primary-400">
                    <th class="text-center headcol">TRIP ID</th>
                    <th class="text-center">TRUCK NO</th>
                    <th class="text-center">DESTINATION</th>
                    <th class="text-center">CLIENT</th>
                    <th class="text-center">SO NO.</th>
                    <th class="text-center">WAYBILL NO.</th>
                    <th class="text-center">LOADING SITE</th>
                    <th class="text-center">CLIENT RATE</th>
                    <th class="text-center">INCENTIVE</th>
                    <th class="text-center">AMOUNT PAID</th>
                    <th class="text-center">TRANSPORTER RATE</th>
                    <th class="text-center">MARGIN</th>
                    <th class="text-center">INVOICE NO</th>
                    <th class="text-center">INVOICE DATE</th>
                    <th class="text-center">DATE PAID</th>
                </tr>
            </thead>
            <tbody class="font-size-xs">';
                if(count($trips)) {
                    foreach($trips as $trip) {
                        $response.='
                            <tr style="white-space: nowrap;margin:0;">
                                <td class="text-center">'.$trip->trip_id.'</td>
                                <td class="text-center">'.strtoupper($trip->truck_no).'</td>
                                <td class="text-center">'.strtoupper($trip->exact_location_id).'</td>
                                <td class="text-center">'.ucwords($trip->company_name).'</td>';
                                $response.='
                                <td class="text-center">';
                                foreach($waybills as $key=> $waybillInfo) {
                                    if($waybillInfo->trip_id === $trip->id) {
                                        $response.=$waybillInfo->sales_order_no.' ';
                                    }
                                }
                                $response.='</td>
                                <td class="text-center">';
                                foreach($waybills as $key=> $waybillInfo) {
                                    if($waybillInfo->trip_id === $trip->id) {
                                        $response.=$waybillInfo->invoice_no.' ';
                                    }
                                }
                                $response.='</td>';
                                $response.='<td class="text-center">'.$trip->loading_site.'</td>
                                <td class="text-center">'.number_format($trip->client_rate, 2).'</td>
                                <td class="text-center">'.number_format($trip->incentive, 2).'</td>
                                <td class="text-center">'.number_format($trip->amount_paid, 2).'</td>
                                <td class="text-center">'.number_format($trip->transporter_rate, 2).'</td>';
                                if($trip->amount_paid) {
                                    $margin = $trip->amount_paid - $trip->transporter_rate;
                                }
                                else {
                                    $margin = ($trip->client_rate + $trip->incentive) - $trip->transporter_rate;
                                }
                                $response.='<td class="text-center">'.number_format($margin, 2).'</td>
                                <td class="text-center bg-danger-400">'.$trip->invoice_no.'</td>
                                <td class="text-center">'.date('d-m-Y', strtotime($trip->created_at)).'</td>';
                                if($trip->date_paid) {
                                    $datePaid = date('d-m-Y', strtotime($trip->date_paid));
                                }
                                else {
                                    $datePaid = '';
                                }
                                $response.='
                                <td class="text-center">'.$datePaid.'</td>
                            </tr>
                        ';
                    }
                }
                else {
                $response.='<tr>
                    <td colspan="15" class="bg-warning-300 text-center font-size-xs text-danger">You do not have any record for '.ucwords($selectedModule).'</td>
                </tr>';
                }
            $response.='
            </tbody>
        </table>';
        return $response;
    }

    function paymentDetailsResponse($trips, $selectedModule) {
        $waybills = $this->waybillsLog($trips);
        $response ='
         <table class="table table-striped">
             <thead style="white-space: nowrap;margin:0;">
                 <tr class="font-weight-bold font-size-xs font-weight-bold">
                     <th colspan="15">Total: '.count($trips).' Trips | 
                         <span class="text-success pointer" id="exportBtn">EXPORT <i class="icon-cloud-download2"></i></span>
                     </th>
                 </tr>
                 <tr class="font-size-xs bg-primary-400">
                     <th class="text-center headcol">TRIP ID</th>
                     <th class="text-center">TRUCK NO</th>
                     <th class="text-center">DESTINATION</th>
                     <th class="text-center">SO NO.</th>
                     <th class="text-center">WAYBILL NO.</th>
                     <th class="text-center">LOADING SITE</th>
                     <th class="text-center">GATED OUT</th>
                     <th>TRANSPORTER</th>
                     <th class="text-center">CLIENT RATE</th>
                     <th class="text-center">INCENTIVE</th>
                     <th class="text-center">TRANSPORTER RATE</th>
                     <th class="text-center">MARGIN</th>
                     <th class="text-center">ADVANCE</th>
                     <th class="text-center">BALANCE</th>
                     <th>OUTSTANDING</th>
                     <th>REMARKS</th>
                 </tr>
             </thead>
             <tbody class="font-size-xs">';
                 if(count($trips)) {
                     foreach($trips as $trip) {
                         $response.='
                             <tr style="white-space: nowrap;margin:0;">
                                 <td class="text-center">'.$trip->trip_id.'</td>
                                 <td class="text-center">'.$trip->truck_no.'</td>
                                 <td class="text-center">'.strtoupper($trip->exact_location_id).'</td>';
                                 $response.='
                                 <td class="text-center">';
                                 foreach($waybills as $key=> $waybillInfo) {
                                     if($waybillInfo->trip_id === $trip->id) {
                                         $response.=$waybillInfo->sales_order_no.' ';
                                     }
                                 }
                                 $response.='</td>
                                 <td class="text-center">';
                                 foreach($waybills as $key=> $waybillInfo) {
                                     if($waybillInfo->trip_id === $trip->id) {
                                         $response.=$waybillInfo->invoice_no.' ';
                                     }
                                 }
                                 if($trip->advance_paid == FALSE && $trip->balance_paid == FALSE) {
                                     $advance = 0;
                                     $balance = 0;
                                     $outstanding = $trip->transporter_rate;
                                     $className = '';
                                 }
                                 else if($trip->advance_paid == TRUE && $trip->balance_paid == FALSE) {
                                    $advance = $trip->advance;
                                    $balance = 0;
                                    $outstanding = $trip->transporter_rate - $trip->advance;
                                    $className = '';
                                 }
                                 else {
                                    if($trip->advance_paid == TRUE && $trip->balance_paid == TRUE) {
                                        $advance = $trip->advance;
                                        $balance = $trip->balance;
                                        $outstanding = $trip->transporter_rate - ($advance + $balance);
                                        $className = 'bg-danger';
                                    }
                                 }

                                 
                                $outstandingIndicator = $outstanding > 0  ? "bg-danger-300" : "";
                                $incentiveIndicator = $trip->incentive > 0  ? "bg-primary-300" : "";
                                $margin = ($trip->client_rate + $trip->incentive) - $trip->transporter_rate;
                                $marginIndicator = $margin > 0 ? "bg-success-300" : "bg-danger-400";
                                 

                                 $response.='</td>';
                                 $response.='<td class="text-center">'.$trip->loading_site.'</td>
                                 <td class="text-center">'.date('d-m-Y', strtotime($trip->gated_out)).'</td>
                                 <td>'.$trip->transporter_name.'</td>
                                 <td class="text-center bg-primary-300">'.number_format($trip->client_rate, 2).'</td>
                                 <td class="text-center '.$incentiveIndicator.'">'.number_format($trip->incentive, 2).'</td>
                                 <td class="text-center bg-danger-400">'.number_format($trip->transporter_rate, 2).'</td>
                                 <td class="text-center  '.$marginIndicator.'">'.number_format($margin, 2).'</td>
                                 <td class="text-center">'.number_format($advance, 2).'</td>
                                 <td class="text-center">'.number_format($balance, 2).'</td>
                                 <td class="text-center '.$outstandingIndicator.'">'.number_format($outstanding, 2).'</td>
                                 <td class="text-center">'.$trip->remark.'</td>
                             </tr>
                         ';
                     }
                 }
                 else {
                 $response.='<tr>
                     <td colspan="15" class="bg-warning-300 text-center font-size-xs text-danger">You do not have any record for '.ucwords($selectedModule).'</td>
                 </tr>';
                 }
             $response.='
             </tbody>
         </table>';
         return $response;
     }

    public function waybillsLog($trips) {
        $waybillInfos = [];
        foreach($trips as $tripListings) {
            $associatedWaybills = tripWaybill::SELECT('trip_id', 'sales_order_no', 'invoice_no')->WHERE('trip_id', $tripListings->id)->GET();
            if(count($associatedWaybills)) {
                [$waybillInfos[]] = $associatedWaybills;
            }
        }
        return $waybillInfos;
    }
    
}
