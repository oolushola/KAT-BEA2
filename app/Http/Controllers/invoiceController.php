<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\client;
use App\tripWaybill;
use App\tripWaybillStatus;
use App\completeInvoice;
use App\companyProfile;
use App\trip;
use App\tripIncentives;
use App\incentives;
use App\invoiceSubheading;

class invoiceController extends Controller
{
    public function invoiceArchive() {
        $invoiceList = $this->detailedInvoiceInformation();
        $clientList = client::ORDERBY('company_name', 'ASC')->GET();
        $waybillinfos = tripWaybill::SELECT('id', 'sales_order_no', 'trip_id', 'tons')->ORDERBY('trip_id', 'ASC')->GET();
        return view('finance.invoice.invoice-archive',
            array(
                'invoiceList' => $invoiceList,
                'clientName' => $clientList,
                'waybillinfos' => $waybillinfos
            )
        );
    }

    function detailedInvoiceInformation() {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT b.id, b.client_rate, b.transporter_rate, b.trip_id, b.client_id, b.customers_name, b.exact_location_id, c.truck_no, c.truck_type_id, d.product, e.truck_type, e.tonnage, f.state FROM tbl_kaya_trip_waybill_statuses a JOIN tbl_kaya_trips b JOIN tbl_kaya_trucks c JOIN tbl_kaya_products d JOIN tbl_kaya_truck_types e JOIN tbl_regional_state f ON a.trip_id = b.id AND c.id = b.truck_id AND b.product_id = d.id AND c.truck_type_id = e.id AND b.destination_state_id = f.regional_state_id    WHERE waybill_status = TRUE AND comment = \'Recieved\' AND invoice_status = FALSE ORDER BY a.trip_id ASC'
            )
        );
        
        return $query;
    }

    function clientSpecificInvoice($client_id) {
        $clientSpecificQuery = DB::SELECT(
            DB::RAW(
                'SELECT b.id, b.client_rate, b.transporter_rate, b.trip_id, b.client_id, b.customers_name, b.exact_location_id, c.truck_no, c.truck_type_id, d.product, e.truck_type, e.tonnage, f.state FROM tbl_kaya_trip_waybill_statuses a JOIN tbl_kaya_trips b JOIN tbl_kaya_trucks c JOIN tbl_kaya_products d JOIN tbl_kaya_truck_types e JOIN tbl_regional_state f ON a.trip_id = b.id AND c.id = b.truck_id AND b.product_id = d.id AND c.truck_type_id = e.id AND b.destination_state_id = f.regional_state_id    WHERE waybill_status = TRUE AND comment = \'Recieved\' AND invoice_status = FALSE AND b.client_id = '.$client_id.' ORDER BY a.trip_id ASC'
            )
        );
        
        return $clientSpecificQuery;
    }

    public function invoiceByClient(Request $request) {
        $client_id = $request->client_id;
        $answer = '<table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">TRIP ID</th>
                <th>CUSTOMER</th>
                <th>PRODUCT</th>
                <th>TRUCK NO.</th>
                <th class="text-center">S.O. Number</th>
                <th>TONS<sub> in (Kg)</sub></th>
                <th class="text-center">ADD TO INVOICE</th>
                <th>AMOUNT</th>

            </tr>
        </thead>
        <tbody id="searchAvailableInvoices">';

            $invoiceList = $this->clientSpecificInvoice($client_id);
            $waybillinfos = tripWaybill::SELECT('id', 'sales_order_no', 'trip_id')->ORDERBY('trip_id', 'ASC')->GET();
            $totalAmount = 0;
            $totalVatRate = 0;
        
            if(count($invoiceList)) {
                foreach($invoiceList as $invoice) {
                    $totalAmount+=$invoice->client_rate;
                    $vatRate = 5 / 100 * $invoice->client_rate;
                    $totalVatRate+=$vatRate;
                    $answer.='<tr>
                        <td class="text-center">'.$invoice->trip_id.'</td>
                        <td>
                            <h6 class="mb-0">
                                <a href="#">'.$invoice->customers_name.'</a>
                                <span class="d-block font-size-sm text-muted">Destination: 
                                    '.$invoice->state.', '.$invoice->exact_location_id.'
                                </span>
                            </h6>
                        </td>
                        <td>'.$invoice->product.'</td>
                        <td><span class="badge badge-primary">'.$invoice->truck_no.'</span></td>
                        <td class="text-center">';
                            foreach($waybillinfos as $salesOrderNumber) {
                                if($salesOrderNumber->trip_id == $invoice->id) {
                                    $answer.= $salesOrderNumber->sales_order_no.'<br>';
                                }
                            }
                        $answer.='</td>
                        <td>'.$invoice->tonnage.'</td>
                        <td class="text-center"><input type="checkbox" name="trips[]" value='.$invoice->id.'></td>
                        <td>
                            <h6 class="mb-0 font-weight-bold">
                                &#x20a6;'.number_format($invoice->client_rate, 2).'    
                                <span class="d-block font-size-sm text-muted font-weight-normal">
                                VAT: &#x20a6;'.number_format($vatRate, 2).'
                                </span>
                            </h6>
                        </td>
                    </tr>';
                }
                $answer.='</tbody>';
                $answer.='<tr>
                    <td colspan="5">Total Amount and Vat Rate Inclusive</td>
                    <td>
                        <button type="submit" class="btn btn-primary addIncentive hidden" id="proceedWithIntencive">PROCEED</button>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary" id="invoiceTrip">INVOICE</button>
                    </td>
                    <td style="background:#000; color:#fff">
                        <h6 class="mb-0 font-weight-bold">
                            &#x20a6;'.number_format($totalAmount, 2).'    
                            <span class="d-block font-size-sm text-muted font-weight-normal">
                            VAT: &#x20a6;'.number_format($totalVatRate, 2).'
                            </span>
                        </h6>
                    </td>
                </tr>';
            } else {
                $answer.='<tr>
                    <td colspan="9">No waybill available for invoicing</td>
                </tr>';
            }
                        
                    
            $answer.='
        
        </table>';


    return $answer;
    }

    public function invoiceTemplate(Request $request) {
        $clientId = $request->client_id;
        $tripsListings = $request->trips;
        
        foreach($tripsListings as $specificTrip)
        [$trucksAndKaidArray[]] = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.truck_no FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b ON a.truck_id = b.id WHERE a.id = '.$specificTrip.' '
            )
        );

        $clientInformation = client::WHERE('id', $clientId)->GET();
        foreach($tripsListings as $trip_id) {
              [$invoicelists[]] = DB::SELECT(
                DB::RAW(
                    'SELECT b.id, b.client_rate, b.transporter_rate, b.trip_id, b.client_id, b.customers_name, b.exact_location_id, b.gated_out, c.truck_no, c.truck_type_id, d.product, e.truck_type, e.tonnage, f.state FROM tbl_kaya_trip_waybill_statuses a JOIN tbl_kaya_trips b JOIN tbl_kaya_trucks c JOIN tbl_kaya_products d JOIN tbl_kaya_truck_types e JOIN tbl_regional_state f ON a.trip_id = b.id AND c.id = b.truck_id AND b.product_id = d.id AND c.truck_type_id = e.id AND b.destination_state_id = f.regional_state_id    WHERE waybill_status = TRUE AND comment = \'Recieved\' AND invoice_status = FALSE AND b.client_id = '.$clientId.' AND b.id = '.$trip_id.' ORDER BY a.trip_id ASC'
                )
            );
        }
        $waybillinfos = tripWaybill::SELECT('id', 'sales_order_no', 'invoice_no', 'tons', 'trip_id')->ORDERBY('trip_id', 'ASC')->GET();
        $tripIncentives = tripIncentives::GET();

        $tripRecord = trip::findOrFail($trip_id);
        $invoiceHeadings = invoiceSubheading::WHERE('client_id', $tripRecord->client_id)->FIRST();

        $invoiceNumber = completeInvoice::SELECT('invoice_no')->ORDERBY('invoice_no', 'DESC')->LIMIT(1)->GET();
        if(sizeof($invoiceNumber) <= 0) {
            $counter = intval('0000') + 135;
        }
        else {
            $counter = intval('0000') + $invoiceNumber[0]->invoice_no + 1;
        }
        $completedInvoiceNumber = 'INV-'.date('Y').'-'.sprintf('%04d', $counter);
        $companyProfile = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name, b.phone_no, b.email FROM tbl_kaya_company_profiles a JOIN users b ON a.authorized_user_id = b.id'
            )
        );

        $availableIncentives = incentives::ALL();
        
        return view('finance.invoice.invoice-template',
            array(
                'invoicelists' => $invoicelists,
                'clientInformation' => $clientInformation,
                'waybillinfos' => $waybillinfos,
                'invoice_no' => $completedInvoiceNumber,
                'invoiceNumberCounter' => $counter,
                'companyProfile' => $companyProfile,
                'incentive' => $tripIncentives,
                'invoiceHeadings' => $invoiceHeadings,
                'availableIncentives' => $availableIncentives,
                'trucksAndKaidArray' => $trucksAndKaidArray
            )
        );
    }

    public function invoicedWaybill(Request $request) {

        $addedIncentives = $request->addedIncentives;
        if(isset($addedIncentives) && sizeof($addedIncentives) > 0) {
            foreach($addedIncentives as $key => $incentiveOnLocation){
                $incentiveRecord = incentives::findOrFail($incentiveOnLocation);
                $trip_id = $request->tripIdentity[$key];
                $storeTripIncentive = tripIncentives::firstOrNew(['trip_id' => $trip_id]);
                $storeTripIncentive->incentive_description = $incentiveRecord->incentive_description;
                $storeTripIncentive->amount = $incentiveRecord->amount;
                $storeTripIncentive->save();
            }
        }

        $updatedAmountArray = $request->initialAmount;
        foreach($updatedAmountArray as $key => $actualRate) {
            $tripId = $request->tripIdListings[$key];
            $updateRate = trip::WHERE('trip_id', $tripId)->UPDATE([
                'client_rate' => $actualRate
            ]);
        }

        foreach($request->trip_id as $order_id) {
            $completedInvoice = completeInvoice::firstOrNew(['trip_id'=>$order_id]);
            $completedInvoice->invoice_no = $request->invoice_no_counter;
            $completedInvoice->completed_invoice_no = 'INV-'.date('Y').'-'.$request->invoice_no_counter;    
            $completedInvoice->save();

            $fullInvoiceNo = $completedInvoice->completed_invoice_no;

            $tripWaybillStatus = tripWaybillStatus::WHERE('trip_id', $order_id)->UPDATE([
                'invoice_status' => TRUE,
                'date_invoiced' => $request->date_invoiced
            ]);
            
            $tripWaybill = tripWaybill::WHERE('trip_id', $order_id)->UPDATE([
                'invoice_status' => TRUE,
                'date_invoiced' => $request->date_invoiced
            ]);            
        }

        return 'completed'.'`'.$fullInvoiceNo;
    }

    public function allInvoicedTrip() {
        $completedInvoice = completeInvoice::ORDERBY('invoice_no', 'DESC')->distinct('invoice_no')->GET(['invoice_no', 'completed_invoice_no', 'paid_status', 'date_paid', 'acknowledged', 'acknowledged_date']);
        
        return view('finance.invoice.all-invoiced-trip', compact('completedInvoice'));
    }

    public function singleInvoice($invoiceNumber) {
        $getTripById = completeInvoice::SELECT('trip_id', 'created_at')->WHERE('completed_invoice_no', $invoiceNumber)->GET();
        foreach($getTripById as $specificTrip)
        [$trucksAndKaidArray[]] = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.truck_no FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b ON a.truck_id = b.id WHERE a.id = '.$specificTrip->trip_id.' '
            )
        );

        foreach($getTripById as $orderId) {
            $trip_id = $orderId->trip_id;
            [$completedInvoice[]] = DB::SELECT(
                DB::RAW(
                    'SELECT b.id, b.client_rate, b.transporter_rate, b.trip_id, b.client_id, b.customers_name, b.exact_location_id, b.gated_out, c.truck_no, c.truck_type_id, d.product, e.truck_type, e.tonnage, f.state, i.company_name, i.phone_no, i.email, i.address FROM tbl_kaya_trip_waybill_statuses a JOIN tbl_kaya_trips b JOIN tbl_kaya_trucks c JOIN tbl_kaya_products d JOIN tbl_kaya_truck_types e JOIN tbl_regional_state f JOIN tbl_kaya_clients i ON a.trip_id = b.id AND c.id = b.truck_id AND b.product_id = d.id AND c.truck_type_id = e.id AND b.destination_state_id = f.regional_state_id AND b.client_id = i.id  WHERE waybill_status = TRUE AND comment = \'Recieved\' AND invoice_status = TRUE AND b.id = '.$trip_id.' ORDER BY a.trip_id ASC'
                )
            );
        }

        $tripRecord = trip::findOrFail($trip_id);
        $invoiceHeadings = invoiceSubheading::WHERE('client_id', $tripRecord->client_id)->FIRST();
     
        $companyProfile = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name, b.phone_no, b.email FROM tbl_kaya_company_profiles a JOIN users b ON a.authorized_user_id = b.id'
            )
        );
        $waybillinfos = tripWaybill::SELECT('id', 'sales_order_no', 'invoice_no', 'tons', 'trip_id')->ORDERBY('trip_id', 'ASC')->GET();
        $tripIncentives = tripIncentives::GET();

        return view('finance.invoice.invoice-reprint', 
            array(
                'completedInvoice' => $completedInvoice,
                'invoice_no' => $invoiceNumber,
                'companyProfile' => $companyProfile,
                'waybillinfos' => $waybillinfos,
                'dateInvoiced' => $getTripById,
                'incentive' => $tripIncentives,
                'invoiceHeadings' => $invoiceHeadings,
                'trucksAndKaidArray' => $trucksAndKaidArray
            )
        );
    }

    public function paidInvoices(Request $request) {
       $paymentDate = $request->paymentDate;
       $invoiceId = $request->paid_invoices;
       if($request->acknowledgeChecker == 1){
            foreach($paymentDate as $key => $datePaid) {
                if(isset($datePaid) && $datePaid != '' ) {
                    $recid = completeInvoice::WHERE('invoice_no', $invoiceId[$key])->UPDATE(['paid_status' => TRUE, 'date_paid' => $datePaid]);
                }
            }
        }
        
        if($request->acknowledgeChecker == 2) {
            $acknowledgmentDate = $request->acknowledgmentDate;
            $acknowledgedInvoiceId = $request->acknowledgedInvoiceId;
            foreach($acknowledgmentDate as $key=> $dateAcknowledged){
                if(isset($dateAcknowledged) && $dateAcknowledged != '' ) {
                    $recid = completeInvoice::WHERE('invoice_no', $acknowledgedInvoiceId[$key])->UPDATE(['acknowledged' => TRUE, 'acknowledged_date' => $dateAcknowledged]);
                }
            }
        }

        return 'updated';
    }

    public function bulksearchinvoice() {
        return view('finance.invoice.invoice-bulk-search');
    }

    public function multipleinvoicesearch(Request $request) {
        $salesOrderNumber = $request->sales_order_no;

        $response = '
        <table class="table table-bordered">
            <thead class="table-info">
                <tr style="font-size:11px;">
                    <th>#</th>
                    <th>Sales Order No.</th>
                    <th>Waybill No.</th>
                    <th>Invoice No.</th>
                    <th>Status</th>                           
                </tr>
            </thead>
            <tbody>';
        
        $count = 0;

        foreach($salesOrderNumber as $key=> $sales_order_no) {
            $count+=1;
            $count % 2 == 0 ? $css = '' : $css = 'table-success';
            $trip_id = tripWaybill::SELECT('trip_id', 'invoice_no')->WHERE('sales_order_no', $sales_order_no)->GET();
            $response.='
                <tr class="'.$css.'" style="font-size:10px;">
                    <td>'.$count.'</td>
                    <td>'.$sales_order_no.'</td>';
                    $response.='<td>';
                    if(isset($trip_id[0])){
                        $response.= $trip_id[0]->invoice_no;
                    }
                    else{
                        $response.= '<span style="font-size:9px; font-weight:bold; color:red">Invalid S.O. Number</span>';
                    }
                    $response.='</td>';

                    if(isset($trip_id[0])){
                        $trip_id = $trip_id[0]->trip_id;
                       $invoiceStatus = completeInvoice::WHERE('trip_id', $trip_id)->GET();
                       if(count($invoiceStatus)){
                           $invoice = $invoiceStatus[0];
                           $completedInvoiceNo = $invoice->completed_invoice_no;
                           if($invoice->paid_status == true){
                               $invoicing ='<i class="icon-checkmark4 text-primary" title="Invoiced & Paid"></i>';
                           }
                           else{
                               $invoicing ='<i class="icon-spinner2 spinner text-teal-400" title="Invoiced, Awaiting Payment"></i>';
                           }
                       } else {
                           $invoicing ='<i class="icon-x text-danger" title="Not invoiced"></i>';
                           $completedInvoiceNo = 'Not Invoiced';
                       }

                   }

                   $response.= '<td>'.$completedInvoiceNo.'</td><td>'.$invoicing.'</td>';
                    
                    

                $response.='</tr>';
            
            
        }


        $response.='</tbody>
        </table>';

        return $response;
        
    }

    public function addIncentives(Request $request) {
        $tripListings = $request->tripIncentives;
        foreach($tripListings as $trip_id){
            [$tripids[]] =  trip::SELECT('id', 'trip_id')->WHERE('id', $trip_id)->ORDERBY('trip_id', 'ASC')->GET();
        }
        $incentivePerTrips = tripIncentives::ORDERBY('trip_id', 'ASC')->GET();
        return view('finance.invoice.invoice-incentives', compact('tripids', 'incentivePerTrips'));
    }

    public function storeIncentives(Request $request) {
        $tripIdListings = $request->trip_id;
        $incentiveDescriptionLists = $request->incentive_description;
        $amountListings = $request->incentive_amount;

        foreach($tripIdListings as $key => $trip_id) {
            if(isset($incentiveDescriptionLists[$key]) && $amountListings[$key] != '') {
                $incentives = tripIncentives::firstOrNew(['trip_id' => $trip_id]);
                $incentives->incentive_description = $incentiveDescriptionLists[$key];
                $incentives->amount = $amountListings[$key];
                $incentives->save();
            }
        }
        return 'saved';
    }

    public function updateTripAmount(Request $request) {
        $initialAmountArray = $request->initialAmount;
        $tripIds = $request->tripIdListings;
        foreach($initialAmountArray as $key=>$updatedAmount){
            if(isset($updatedAmount) && $updatedAmount != ''){
                $trip_id = $tripIds[$key];
                $recid = trip::findOrFail($trip_id);
                $recid->client_rate = $updatedAmount;
                $recid->save();
            }
        }
        return 'amountUpdated';
    }

    public function removeIncentive($id) {
        $tripRecordId = tripIncentives::findOrFail($id);
        $tripRecordId->delete();
        return 'removed';
    }

    public function financeWaybillUpload(Request $request) {
        $waybillIdListings = $request->waybillIdListings;
        $salesOrderNumberlistings = $request->salesOrderNumber;
        $invoiceNumberlistings = $request->invoiceNumber;
        $tonnage = $request->tonnage;

        foreach($waybillIdListings as $key => $waybill_id){
            $sales_order_no = $salesOrderNumberlistings[$key];
            $invoice_number = $invoiceNumberlistings[$key];
            $tons = $tonnage[$key];

            $tripWaybill = tripWaybill::findOrFail($waybill_id);
            $tripWaybill->sales_order_no = $sales_order_no;
            $tripWaybill->invoice_no = $invoice_number;
            $tripWaybill->tons = $tons;
            $tripWaybill->save();
        }
        return redirect()->back();
    }

    public function deleteInvoice($invoiceNumber) {
        $invoiceLog = completeInvoice::WHERE('completed_invoice_no', $invoiceNumber)->GET();
        $counter = 0;
        foreach($invoiceLog as $invoice){
            $getCountOfIncentives = tripIncentives::WHERE('trip_id', $invoice->trip_id)->GET()->COUNT();
            $counter += $getCountOfIncentives;
            if($counter > 0){
                return 'cant_delete';
            } else {
                $tripWaybill = tripWaybill::WHERE('trip_id', $invoice->trip_id)->UPDATE(['date_invoiced' => null]);
                $tripWaybillStatus = tripWaybillStatus::WHERE('trip_id', $invoice->trip_id)->UPDATE(['invoice_status' => false, 'date_invoiced' => null]);

                $completeInvoices = completeInvoice::findOrFail($invoice->id);
                $completeInvoices->delete();
            }
        }
        return 'deleted';

    }

    
}
