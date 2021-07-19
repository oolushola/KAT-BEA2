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
use App\vatRate;
use App\invoiceClientRename;
use App\product;
use App\invoiceSpecialRemark;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\PaymentBreakdown;

class invoiceController extends Controller
{
    public function invoiceArchive() {
        $invoiceList = $this->detailedInvoiceInformation();
        $clientList = client::ORDERBY('company_name', 'ASC')->GET();
        $waybillinfos = tripWaybill::SELECT('id', 'sales_order_no', 'trip_id', 'tons')->ORDERBY('trip_id', 'ASC')->GET();

        foreach($invoiceList as $key => $tripsById) {
            $waybills[] = tripWaybill::SELECT('id', 'sales_order_no', 'invoice_no', 'tons', 'trip_id')->WHERE('trip_id', $tripsById->id)->ORDERBY('trip_id', 'ASC')->GET();
        }

        foreach($waybills as $key => $waybillListings) {
            foreach($waybillListings as $waybill) {
                $waybillinfos[] = $waybill;
            }
        }

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
                'SELECT b.id, b.client_rate, b.transporter_rate, b.trip_id, b.client_id, b.customers_name, b.exact_location_id, c.truck_no, c.truck_type_id, d.product, b.gated_out, e.truck_type, e.tonnage, f.state FROM tbl_kaya_trip_waybill_statuses a JOIN tbl_kaya_trips b JOIN tbl_kaya_trucks c JOIN tbl_kaya_products d JOIN tbl_kaya_truck_types e JOIN tbl_regional_state f ON a.trip_id = b.id AND c.id = b.truck_id AND b.product_id = d.id AND c.truck_type_id = e.id AND b.destination_state_id = f.regional_state_id    WHERE waybill_status = TRUE AND comment = \'Recieved\' AND invoice_status = FALSE ORDER BY a.trip_id ASC LIMIT 50'
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
        $vatRate = vatRate::WHERE('client_id', $clientId)->first();
        
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
                'trucksAndKaidArray' => $trucksAndKaidArray,
                'vatRateInfos' => $vatRate
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
            $completedInvoice->vat_used = $request->vat_used;
            $completedInvoice->withholding_tax_used = $request->withholding_vat_used; 
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

    public function allInvoicedTrip(Request $request) {
        $invoiced = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT a.invoice_no, a.paid_status, a.date_paid, a.acknowledged, a.acknowledged_date, b.client_id, c.company_name, a.completed_invoice_no, a.amount_paid_dfferent, invoice_status FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_kaya_trip_waybill_statuses d ON a.trip_id = b.id AND b.client_id = c.id AND d.trip_id = b.id ORDER BY invoice_no DESC'
            )
        );

        $myCollectionObj = collect($invoiced);
        $completedInvoice = $this->paginate($myCollectionObj);
        $invoiceBillers = invoiceClientRename::GET();
        return view('finance.invoice.all-invoiced-trip', compact('completedInvoice', 'invoiceBillers'));
    }

    public function paginate($items, $perPage = 300, $page = null, $options = []) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $pagination = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        $path = url('/').'/all-invoiced-trips?page='.$page;
        return $pagination->withPath($path);
    }

    public function singleInvoice($invoiceNumber) {
        $getTripById = completeInvoice::SELECT('trip_id', 'created_at', 'paid_status')->WHERE('completed_invoice_no', $invoiceNumber)->GET();
        foreach($getTripById as $specificTrip) {
            [$trucksAndKaidArray[]] = DB::SELECT(
                DB::RAW(
                    'SELECT a.*, b.truck_no FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b ON a.truck_id = b.id WHERE a.id = '.$specificTrip->trip_id.' '
                )
            );
        }

        foreach($getTripById as $orderId) {
            $trip_id = $orderId->trip_id;
            [$completedInvoice[]] = DB::SELECT(
                DB::RAW(
                    'SELECT b.id, b.loaded_weight, b.client_rate, b.transporter_rate, b.trip_id, b.client_id, b.customers_name, b.exact_location_id, b.gated_out, c.truck_no, c.truck_type_id, d.product, e.truck_type, e.tonnage, f.state, i.company_name, i.phone_no, i.email, i.address FROM tbl_kaya_trip_waybill_statuses a JOIN tbl_kaya_trips b JOIN tbl_kaya_trucks c JOIN tbl_kaya_products d JOIN tbl_kaya_truck_types e JOIN tbl_regional_state f JOIN tbl_kaya_clients i ON a.trip_id = b.id AND c.id = b.truck_id AND b.product_id = d.id AND c.truck_type_id = e.id AND b.destination_state_id = f.regional_state_id AND b.client_id = i.id  WHERE waybill_status = TRUE AND comment = \'Recieved\' AND invoice_status = TRUE AND b.id = '.$trip_id.' ORDER BY a.trip_id ASC'
                )
            );
        }

        $preferedBankDetails = client::findOrFail($completedInvoice[0]->client_id);


        $tripRecord = trip::findOrFail($trip_id);
        $invoiceHeadings = invoiceSubheading::WHERE('client_id', $tripRecord->client_id)->FIRST();
     
        $companyProfile = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name, b.phone_no, b.email FROM tbl_kaya_company_profiles a JOIN users b ON a.authorized_user_id = b.id'
            )
        );
        
        
        foreach($getTripById as $key => $tripsById) {
            $waybills[] = tripWaybill::SELECT('id', 'sales_order_no', 'invoice_no', 'tons', 'trip_id')->WHERE('trip_id', $tripsById->trip_id)->ORDERBY('trip_id', 'ASC')->GET();
        }

        foreach($waybills as $key => $waybillListings) {
            foreach($waybillListings as $waybill) {
                $waybillinfos[] = $waybill;
            }
        }

        $tripIncentives = tripIncentives::GET();
        //$vatRate = vatRate::first();
        $invoiceBiller = invoiceClientRename::WHERE('invoice_no', $invoiceNumber)->first();
        $allProducts = product::get();
        $clientListings = client::ORDERBY('company_name', 'ASC')->GET();

        $vatRate = completeInvoice::SELECT('vat_used', 'withholding_tax_used')->WHERE('completed_invoice_no', $invoiceNumber)->DISTINCT()->GET()->FIRST();

        //$invoiceList = $this->detailedInvoiceInformation();

        $invoiceList = DB::SELECT(
            DB::RAW(
                'SELECT b.id, b.client_rate, b.transporter_rate, b.trip_id, b.client_id, b.customers_name, b.exact_location_id, c.truck_no, c.truck_type_id, d.product, b.gated_out, e.truck_type, e.tonnage, f.state FROM tbl_kaya_trip_waybill_statuses a JOIN tbl_kaya_trips b JOIN tbl_kaya_trucks c JOIN tbl_kaya_products d JOIN tbl_kaya_truck_types e JOIN tbl_regional_state f ON a.trip_id = b.id AND c.id = b.truck_id AND b.product_id = d.id AND c.truck_type_id = e.id AND b.destination_state_id = f.regional_state_id    WHERE waybill_status = TRUE AND comment = \'Recieved\' AND invoice_status = FALSE  AND client_id = "'.$completedInvoice[0]->client_id.'" ORDER BY a.trip_id ASC LIMIT 50'
            )
        );

        $clientList = client::ORDERBY('company_name', 'ASC')->GET();

        $invoiceSpecialRemark = invoiceSpecialRemark::WHERE('invoice_no', $invoiceNumber)->GET()->FIRST();
        
        $incentives = incentives::GET();
        $poNumber = completeInvoice::SELECT('po_number')->WHERE('completed_invoice_no', $invoiceNumber)->GET()->FIRST();

        return view('finance.invoice.invoice-reprint', 
            array(
                'completedInvoice' => $completedInvoice,
                'invoice_no' => $invoiceNumber,
                'companyProfile' => $companyProfile,
                'waybillinfos' => $waybillinfos,
                'dateInvoiced' => $getTripById,
                'incentive' => $tripIncentives,
                'invoiceHeadings' => $invoiceHeadings,
                'trucksAndKaidArray' => $trucksAndKaidArray,
                'vatRateInfos' => $vatRate,
                'invoiceBiller' => $invoiceBiller,
                'products' => $allProducts,
                'clients' => $clientListings,
                'paidStatus' => $getTripById,
                'invoiceList' => $invoiceList,
                'clientList' => $clientList,
                'waybillinfos' => $waybillinfos,
                'invoiceSpecialRemark' => $invoiceSpecialRemark,
                'incentives' => $incentives,
                'preferedBankDetails' => $preferedBankDetails,
                'po_number' => $poNumber
               
            )
        );
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
    
    public function invoiceBiller(Request $request) {
        $invoiceBiller = invoiceClientRename::firstOrNew(['invoice_no' => $request->invoice_no]);
        $invoiceBiller->client_name = $request->client_name;
        $invoiceBiller->client_address = $request->client_address;
        $invoiceBiller->save();
        return 'changed';
    }

    public function alterTripInformation(Request $request) {
        foreach($request->tripIdListings as $key => $trip_id){
            
            $gated_out = $request->gatedOut[$key];
            $customers_name = $request->customersName[$key];
            $exact_location_id = $request->exactLocation[$key];
            $product_id = $request->product[$key];

            $specificRecordId = trip::findOrFail($trip_id);

            $specificRecordId->gated_out = $gated_out;
            $specificRecordId->customers_name = $customers_name;
            $specificRecordId->exact_location_id = $exact_location_id;
            $specificRecordId->product_id = $product_id;
            $specificRecordId->save();
        }
        return 'updated';
    }

    public function clientAddress(Request $request) {
        $getAddress = client::SELECT('address')->WHERE('company_name', $request->client_name)->GET()->FIRST();
        return $getAddress->address;
    }

    public function cancelAcknowledgement(Request $request) {
        $invoice_no = $request->value;
        $cancelAcknowledgment = completeInvoice::WHERE('invoice_no', $invoice_no)->UPDATE([
            'acknowledged' => FALSE, 
            'acknowledged_date' => NULL, 
            'paid_status' => FALSE, 
            'date_paid' => NULL
        ]);
        return 'removed';
    }

    public function removePayment(Request $request) {
        $invoice_no = $request->value;
        $cancelledPayment = completeInvoice::WHERE('invoice_no', $invoice_no)->UPDATE([
            'paid_status' => FALSE, 
            'date_paid' => NULL
        ]);
        return 'removed';
    }

    public function removeSpecificTripOnInvoice(Request $request) {
        $trip_id = $request->value;
        $tripRecord = completeInvoice::WHERE('trip_id', $trip_id)->GET()->LAST();
        $tripRecord->DELETE();

        $tripWaybillStatus = tripWaybillStatus::WHERE('trip_id', $trip_id)->GET()->LAST();
        $tripWaybillStatus->invoice_status = FALSE;
        $tripWaybillStatus->date_invoiced = NULL;
        $tripWaybillStatus->SAVE();

        $tripWaybill = tripWaybill::WHERE('trip_id', $trip_id)->GET()->LAST();
        $tripWaybill->invoice_status = FALSE;
        $tripWaybill->date_invoiced = NULL;
        $tripWaybill->SAVE();

        $tripIncentive = tripIncentives::WHERE('trip_id', $trip_id)->GET()->LAST();
        if(count($tripIncentive) > 0){
            $tripIncentive->DELETE();        
        }

        return 'removed';
    }

    public function addMoreTripToSpecificInvoice(Request $request) {
        $getDistinctInvoiceNo = completeInvoice::WHERE('completed_invoice_no', $request->invoice_no)->GET()->LAST();
        $date_invoiced = date('d-m-Y');

        foreach($request->trips as $trip_id) {
            completeInvoice::firstOrNew(['trip_id' => $trip_id, 'invoice_no' => $getDistinctInvoiceNo->invoice_no, 'completed_invoice_no' => $request->invoice_no, 'vat_used' => $getDistinctInvoiceNo->vat_used, 'withholding_tax_used' => $getDistinctInvoiceNo->withholding_tax_used, 'created_at' => $getDistinctInvoiceNo->created_at]);

            $tripWaybillStatus = tripWaybillStatus::WHERE('trip_id', $trip_id)->GET()->FIRST();
            $tripWaybillStatus->invoice_status = TRUE;
            $tripWaybillStatus->date_invoiced = $date_invoiced;
            $tripWaybillStatus->SAVE();

            $tripWaybill = tripWaybill::WHERE('trip_id', $trip_id)->GET()->FIRST();
            $tripWaybill->invoice_status = TRUE;
            $tripWaybill->date_invoiced = $date_invoiced;
            $tripWaybill->SAVE();

            // Update the acknowledgement status and date and date created to the updated date.
            $acknowledgement = completeInvoice::WHERE('completed_invoice_no', $request->invoice_no)->UPDATE(['acknowledged' => false, 'acknowledged_date' => NULL]);
        }
        return redirect()->back();
    }

    public function addSpecialRemark(Request $request) {
        $validateData = $this->validate($request, [
            'condition' => 'required | string',
            'invoice_no' => 'required | string',
            'amount' => 'required | integer',
            'description' => 'string | required'
        ]);

        $storeData = invoiceSpecialRemark::firstOrNew(['invoice_no' => $request->invoice_no]);
        $storeData->amount = $request->amount;
        $storeData->description = $request->description;
        $storeData->condition = $request->condition;
        $storeData->save();

        return 'saved';
    }

    public function updateTripIncentive(Request $request) {
        $incentive = incentives::findOrFail($request->incentive_id);
        $tripIncentive = tripIncentives::firstORNEW(['trip_id' => $request->trip_id]);
        $tripIncentive->incentive_description = $incentive->incentive_description;
        $tripIncentive->amount = $incentive->amount;
        $tripIncentive->save();
        return 'added';
    }

    public function updateInvoiceNumberAndDate(Request $request) {
        $newInvoiceNo = $request->complete_invoice_no;
        $newInvoiceDate = str_replace('T', ' ', $request->date_invoiced);
        $previousInvoiceNo = $request->previos_invoice_no;

        $invoiceNo = explode('-', $newInvoiceNo);
        $specificInvoiceNo = abs($invoiceNo[2]);

        $checkInvoiceNoValidity = completeInvoice::WHERE('invoice_no', $specificInvoiceNo)->exists();
        if($checkInvoiceNoValidity) {
            //change the date only.
            DB::UPDATE(
                DB::RAW(
                    'UPDATE tbl_kaya_complete_invoices SET created_at = "'.$newInvoiceDate.'", updated_at = "'.$newInvoiceDate.'" WHERE completed_invoice_no = "'.$previousInvoiceNo.'"  '
                )
            );
            return 'invoiceNoExists';
        }
        else {
            DB::UPDATE(
                DB::RAW(
                    'UPDATE tbl_kaya_complete_invoices SET invoice_no = "'.$specificInvoiceNo.'", completed_invoice_no = "'.$newInvoiceNo.'", created_at = "'.$newInvoiceDate.'", updated_at = "'.$newInvoiceDate.'" WHERE completed_invoice_no = "'.$previousInvoiceNo.'"  '
                )
            );
            
            return 'updated';
        }
    }


    public function invoiceCollage($invoiceNumber) {
        $companyProfile = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name, b.phone_no, b.email FROM tbl_kaya_company_profiles a JOIN users b ON a.authorized_user_id = b.id'
            )
        );
        $vatRate = vatRate::first();
        $dateInvoiced = completeInvoice::SELECT('created_at')->WHERE('completed_invoice_no', $invoiceNumber)->FIRST();

        $billingTo = DB::SELECT(
            DB::RAW('SELECT * FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c ON a.trip_id = b.id AND b.client_id = c.id WHERE a.completed_invoice_no = "'.$invoiceNumber.'" LIMIT 1 ')
        );

        


        $tripCounts = completeInvoice::WHERE('completed_invoice_no', $invoiceNumber)->GET()->COUNT();


        $tripsOrginRateAndWeight = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT c.loading_site, b.loaded_weight, client_rate  FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b JOIN tbl_kaya_loading_sites c ON a.trip_id = b.id AND b.loading_site_id = c.id WHERE a.completed_invoice_no = "'.$invoiceNumber.'"'
            )
        );

        foreach($tripsOrginRateAndWeight as $something) {
            [$noOfUnits[]] = DB::SELECT(
                DB::RAW(
                    'SELECT COUNT(*) AS no_of_unit FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b JOIN tbl_kaya_loading_sites c ON a.trip_id = b.id AND b.loading_site_id = c.id WHERE a.completed_invoice_no = "'.$invoiceNumber.'" AND c.loading_site = "'.$something->loading_site.'" AND b.loaded_weight = "'.$something->loaded_weight.'" '
                )
            );
        }

        return view('finance.invoice.invoice-collage',
            array(
                'companyProfile' => $companyProfile,
                'invoice_no' => $invoiceNumber,
                'vatRateInfos' => $vatRate,
                'dateInvoiced' => $dateInvoiced,
                'biller' => $billingTo,
                'tripsOrginRateAndWeight' => $tripsOrginRateAndWeight,
                'noOfUnits' => $noOfUnits
            
            )
        );
    }

    public function invoicePreview(Request $request) {
        $invoiceNo = $request->invoice_no;
        $acknowledgement = $request->acknowledgement;
        $payment_status = $request->payment_status;

        $clientInfo = DB::SELECT(
            DB::RAW(
                'SELECT b.id, b.trip_id, b.gated_out, a.vat_used, a.payment_type, a.withholding_tax_used, b.client_rate, b.amount_paid, c.address, c.company_name, c.phone_no, c.email, d.truck_no, invoice_status FROM tbl_kaya_complete_invoices a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_kaya_trucks d JOIN tbl_kaya_trip_waybill_statuses e ON a.trip_id = b.id AND b.client_id = c.id AND b.truck_id = d.id AND e.trip_id = b.id WHERE a.invoice_no = "'.$invoiceNo.'"'
            )
        );
        $companyProfile = companyProfile::GET()->FIRST();
        foreach($clientInfo as $key => $clientDetails) {
            $tripIncentives[] = tripIncentives::WHERE('trip_id', $clientDetails->id)->GET()->FIRST();
        }        
        $invoicePreview ='<div class="card-body" style="font-family:tahoma; font-size:11px;">
        <input type="hidden" name="thisInvoiceNo" value="'.$invoiceNo.'" />
        <div class="row">
            <div class="mb-4 col-md-6">
                <span class="text-muted">Client</span>
                <ul class="list list-unstyled mb-0">
                    <li>
                        <h5 class="my-2">'.$clientInfo[0]->company_name.'</h5>
                    </li>
                    <li>'.$clientInfo[0]->address.'</li>
                    <li>Nigeria</li>
                    <li>'.$clientInfo[0]->phone_no.'</li>
                </ul>
            </div>
            
            <div class="mb-2  col-md-6">
                <div class="d-flex flex-wrap wmin-md-400">
                    <ul class="list list-unstyled mb-0">
                        <li>
                            <h6 class="my-1 font-weight-bold font-size-sm">Acknowledged? ';
                                if($acknowledgement == TRUE) {
                                    $acknowledgementStatus = 'disabled checked';
                                    $iconState = '<i class="icon-checkmark4 text-primary"></i>';
                                    $paymentApplicable = '';
                                }
                                else {
                                    $acknowledgementStatus = '';
                                    $iconState = '<i class="icon-x text-danger"></i>';
                                    $paymentApplicable = 'd-none';
                                }
                                $invoicePreview.='<input type="checkbox" class="acknowledgementChecker ml-2" '.$acknowledgementStatus.'  />
                                <input type="date" style="width:120px; font-size:10px" class="d-none" id="acknowledgementDateChecker" name="'.$invoiceNo.'"  />
                                <span id="acknowledgmentPlaceholder"></span>
                            </h6>
                        </li>

                        <li>
                            <h6 class="my-1 font-weight-bold font-size-sm mt-2">Recognize Payment';
                                if($payment_status == TRUE && $clientInfo[0]->payment_type == TRUE) {
                                    $paymentStatus = 'disabled checked';
                                    $paymentState = '<i class="icon-checkmark4 text-success"></i>';
                                }
                                else {
                                    $paymentStatus = '';
                                    $paymentState = '<i class="icon-x text-danger"></i>';
                                }
                                $invoicePreview.='<input type="checkbox" class="paidChecker ml-1 '.$paymentApplicable.'" '.$paymentStatus.' />
                                <input type="date" style="width:120px; font-size:10px" class="d-none" id="paidDateChecker" title="'.$invoiceNo.'" name="paidDateChecker"  />
                                <span id="paidPlaceholder"></span>
                            </h6>
                                <div class="d-none mt-2" id="paymentTypeHolder">
                                    Full Payment <input type="radio" name="paymentType" class="paymentType" value="1">
                                    <span class="ml-2">Part Payment <input type="radio" name="paymentType" class="paymentType" value="0"></span>
                                    <span class="ml-4 font-weight-bold d-none" id="partPaymentCompleted">
                                        Payment Completed
                                        <input type="checkbox" name="paymentCompleted" class="partPaymentComplete ml-2" value="1">
                                    </span>
                                    <input type="hidden" id="paymentType" class="">
                                </div>
                            
                        </li>';
                        if($clientInfo[0]->invoice_status == TRUE) {
                            $classes = 'btn btn-danger font-size-xs font-weight-semibold';
                            $label = '<i class="icon-lock4"></i>';
                            $title = 'CHANGE TO UNINVOICE';
                        }
                        else{
                            $classes = 'btn btn-primary font-size-xs font-weight-semibold';
                            $label = '<i class="icon-unlocked"></i>';
                            $title = 'CHANGE TO INVOICED';
                        }
                        $invoicePreview.='<li>
                        <button data-id="'.$invoiceNo.'" id="changeInvoiceStatus" class="'.$classes.'">'.$title.' '.$label.'</button>
                        <span id="statusPreviewHolder" class="ml-2"></span>
                        </li>                        
                    </ul>

                    <ul class="list list-unstyled text-right mb-0 ml-auto">
                        <li><h6 class="font-weight-bold my-1" id="acknowledgmentState">'.$iconState.'</h6></li>
                        <li><h6 class="font-weight-bold my-1" id="paymentState">'.$paymentState.'</h6></li>
                    </ul>
                </div>
            </div>
        </div>
        <span class="font-weight-bold text-primary pointer" value="'.$invoiceNo.'" id="viewPaymentHistory" >View Payment History</span>
        <div id="paymentHistoryLoader" class="mb-3 mt-2"></div>
        ';
    
    $invoicePreview.='<div class="table-responsive mt-3">
        <table class="table table-striped" >
            <thead>
                <tr id="partPaymentBtn" class="d-none">
                    <th colspan="4"></th>
                    <th class="text-center">
                        <button class="font-size-xs btn btn-danger font-weight-bold" id="updatePayment" type="button">UPDATE PAYMENT</button>
                    </th>
                    <th>&nbsp;</th>
                </tr>
                <tr style="font-size:12px; font-family:tahoma; font-weight:bold">
                    <th class="text-center"><b>Trip ID</b></th>
                    <th class="text-center"><b>Invoice Date</b></th>
                    <th><b>Truck No.</b></th>
                    <th class="text-center"><b>Expected Rate</b> </th>
                    <th class="text-center"><b>Amount Paid</b></th>
                    <th class="text-center"><b>Difference</b></th>
                </tr>
            </thead>
            <tbody style="font-size:12px; font-family:tahoma">';
                $subtotal = 0;
                $total = 0;
                $sumOfActualRateIncentive = 0;
                $sumOfAmountPaidIncentive = 0;
                $sumOfAmountPaid = 0;
                foreach($clientInfo as $key=> $tripDetails) {
                    $difference = 0;
                    $subtotal += $tripDetails->client_rate;
                    if($tripDetails->amount_paid == "") {
                        $demo = $tripDetails->client_rate * (($tripDetails->vat_used - $tripDetails->withholding_tax_used)/100);
                        $amountPaid = $tripDetails->client_rate + $demo;
                    }
                    else {
                        $amountPaid = $tripDetails->amount_paid;
                    }
                    $sumOfAmountPaid += $amountPaid;
                    $invoicePreview.='<tr>
                        <td class="font-weight-bold text-center">
                            <span class="defaultInfo">'.$tripDetails->trip_id.'</span>
                        </td>
                        <td class="text-center">'.date('d/m/Y', strtotime($tripDetails->gated_out)).'</td>
                        <td>'.$tripDetails->truck_no.'</td>
                        
                        <td class="text-center">';
                           
                            $singleIncentive = 0; 
                            if($tripIncentives[$key] && $tripIncentives[$key]->trip_id == $tripDetails->id) {
                                
                                $sumOfActualRateIncentive+=$tripIncentives[$key]->amount;

                                $amount_ = $tripIncentives[$key]->amount + $tripDetails->client_rate;
                                $wht = ($amount_) * (($tripDetails->vat_used - $tripDetails->withholding_tax_used) / 100);
                                $exr = $amount_ + $wht;
                                $expectedRate = '&#x20a6;'.number_format($exr, 2).'<span class="icon-coins font-size-xs ml-1"></span>';
                            }
                            else {
                                $amount_ = $tripDetails->client_rate; + $singleIncentive;
                                $wht = ($amount_) * (($tripDetails->vat_used - $tripDetails->withholding_tax_used) / 100);
                                $exr = $amount_ + $wht;
                                $expectedRate = '&#x20a6;'.number_format($exr, 2).'<span class="icon font-size-xs ml-1"></span>';
                            }
                            $invoicePreview.= $expectedRate; 
                            
                        $invoicePreview.='</td>
                        <td class="text-center">
                            
                            <input type="hidden" name="tripIdListings[]" value="'.$tripDetails->trip_id.'" />
                            <input type="text" value="" class="d-none amountPaid" name="amountPaid[]" id="amountPaid'.$tripDetails->trip_id.'" style="width:80px; font-size:10px; outline:none">
                            <span id="loader'.$tripDetails->trip_id.'"></span>';
                            
                            $invoicePreview.='<span id="incentive'.$tripDetails->trip_id.'">';
                            $singleIncentiveAP = 0;
                            if($tripIncentives[$key] && $tripIncentives[$key]->trip_id == $tripDetails->id && $tripDetails->amount_paid == "") {
                                $amount_ap = $tripIncentives[$key]->amount + $tripDetails->client_rate;
                                $wht_ap = ($amount_ap) * (($tripDetails->vat_used - $tripDetails->withholding_tax_used) / 100);
                                $exr_ap = $amount_ap + $wht_ap;
                                $expectedRate_ap = '&#x20a6;'.number_format($exr_ap, 2).'<span class="icon-coins font-size-xs ml-1"></span>';
                                $sumOfAmountPaidIncentive+=$tripIncentives[$key]->amount;                                
                            }
                            else {
                                if(!$tripIncentives[$key] && $tripDetails->amount_paid == "") {
                                    $amount_ap = $tripDetails->client_rate;
                                    $wht_ap = ($amount_ap) * (($tripDetails->vat_used - $tripDetails->withholding_tax_used) / 100);
                                    $exr_ap = $amount_ap + $wht_ap;
                                    $expectedRate_ap = '&#x20a6;'.number_format($exr_ap, 2).'<span class="icon font-size-xs ml-1"></span>';

                                } else {
                                    $exr_ap = $tripDetails->amount_paid;
                                    $expectedRate_ap = '&#x20a6;'.number_format($tripDetails->amount_paid, 2).'<span class="icon font-size-xs ml-1"></span>';
                                }
                            }
                            
                            $invoicePreview.= '<span id="'.$tripDetails->id.'" value="'.$tripDetails->trip_id.'" class="initialRatePlaceholder">'.$expectedRate_ap.'</span>';
                            
                        $invoicePreview.='</td>
                        <td class="text-center">';
                            $invoicePreview.= number_format($exr - $exr_ap, 2);
                        $invoicePreview.='</td>
                    </tr>';
                }
                $sumOfAmountPaid;
                
                $vat = $tripDetails->vat_used;
                $withholdingTax = $tripDetails->withholding_tax_used;
                $taxDifference = ($vat - $withholdingTax) / 100;

                $sumActualRateAndIncentive = $subtotal + $sumOfActualRateIncentive;
                $withholdingTaxofActualRate = $sumActualRateAndIncentive * $taxDifference;
                $amountPayableExpectedRate = $sumActualRateAndIncentive + $withholdingTaxofActualRate;
                
                $sumOfAmountPaidIncentive += $sumOfAmountPaidIncentive * $taxDifference ;

                $amountPayableofAmountPaid = $sumOfAmountPaid + $sumOfAmountPaidIncentive;

            $invoicePreview.='
                <tr>
                    <td colspan="3"></td>
                    <td class="text-center font-weight-bold">Total: &#x20a6;'.number_format($amountPayableExpectedRate, 2).'</td>
                    <td class="text-center font-weight-bold">Total: &#x20a6;'.number_format($amountPayableofAmountPaid, 2).'</td>
                    <td class="text-center font-weight-bold">Total: &#x20a6;'.number_format($amountPayableExpectedRate - $amountPayableofAmountPaid, 2).'</td>
                </tr>
            </tbody>
        </table>
    </div>';

    

    return $invoicePreview;
    }

    public function updateAmountPaid(Request $request) {
        $amountPaid = $request->amount_paid;
        $id = $request->id;

        $tripDetails = trip::findOrFail($id);
        $getTripInvoiceNum = completeInvoice::SELECT('invoice_no')->WHERE('trip_id', $id)->GET()->FIRST();
        if($tripDetails->client_rate == $amountPaid) {
            $tripDetails->amount_paid = NULL;
            $invoices = DB::table('tbl_kaya_complete_invoices')
                ->WHERE('invoice_no', $getTripInvoiceNum->invoice_no)
                ->UPDATE(array('amount_paid_dfferent' => 0));
            
        }
        else {
            $tripDetails->amount_paid = $amountPaid;
            $invoices = DB::table('tbl_kaya_complete_invoices')
            ->WHERE('invoice_no', $getTripInvoiceNum->invoice_no)
            ->UPDATE(array('amount_paid_dfferent' => 1));
        }
        $tripDetails->save();
        return 'updated';
    }

    public function paidInvoices(Request $request) {
        $paymentType = $request->paymentType;
        $invoiceNo = $request->thisInvoiceNo;
        $amountPaidListings = $request->amountPaid;
        if($paymentType == 0 && $request->paymentCompleted == 1) {
            $paymentType = 1;
        }
        if($request->checker == 1) {
            completeInvoice::WHERE('invoice_no', $invoiceNo)->UPDATE([
                'paid_status' => TRUE,
                'date_paid' => $request->paidDateChecker,
                'payment_type' => $paymentType
            ]);
            foreach($request->tripIdListings as $key => $tripId) {
                if(isset($amountPaidListings[$key]) && $amountPaidListings[$key] != '') {
                    $tripInfo = trip::WHERE('trip_id', $tripId)->GET()->FIRST();
                    $tripInfo->amount_paid = $tripInfo->amount_paid + $amountPaidListings[$key];
                    PaymentBreakdown::CREATE([
                        'trip_id' => $tripInfo->id,
                        'invoice_no' => $invoiceNo,
                        'date_paid' => $request->paidDateChecker,
                        'amount' => $amountPaidListings[$key]
                    ]);
                    $tripInfo->save();
                }
            }
            // return $request->all();   
        }
        if($request->checker == 2) {
            completeInvoice::WHERE('invoice_no', $request->invoice_no)->UPDATE(['acknowledged' => TRUE, 'acknowledged_date' => $request->acknowledgement_date]);
        }
 
        return 'updated';
     }


    public function yetToReceiveWaybill(Request $request) {
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.gated_out, a.trip_id, a.exact_location_id, a.tracker, b.truck_no, c.waybill_status, d.company_name FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_trip_waybill_statuses c JOIN tbl_kaya_clients d ON a.truck_id = b.id AND a.client_id = d.id WHERE trip_status = 1 AND tracker BETWEEN 5 AND 8 AND a.id = c.trip_id AND c.waybill_status = FALSE'
            )
        );
        foreach($trips as $specificTrip) {
            $waybillListings[] = tripWaybill::WHERE('trip_id', $specificTrip->id)->GET();
        }

        foreach($waybillListings as $waybills) {
            foreach($waybills as $waybill) {
                $waybillCollections[] = $waybill; 
            }
        }
        $response = '<table class="table table-condensed">
            <thead class="table-success font-size-sm text-primary">
                <tr>
                    <td colspan="8">
                        <input type="text" placeholder="SEARCH" style="font-size:11px; border: 1px solid #ccc; outline:none; padding:5px; width:200px" id="searchTrips" />
                        <span class="pointer ml-2" id="exportUnreceivedWaybill"><i class="icon-download4 mr-1"></i>Export</span>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-success font-size-xs font-weight-bold" id="receiveSelectedWaybill">RECEIVE SELECTED</button>
                    </td>
                </tr>
                <tr class="text-center">
                    <th>SN</th>
                    <th>TRIP ID</th>
                    <th>CLIENT</th>
                    <th>GATE OUT</th>
                    <th>TRUCK NO</th>
                    <th>DESTINATION</th>
                    <th>SALES ORDER NO</th>
                    <th>INVOICE NO</th>
                    <th><input type="checkbox" id="checkAllTrips" /></th>
                </tr>
            </thead>
            
            <tbody class="font-size-xs" id="tripsToBeReceivedDB">';
            if(count($trips)) {
                $count = 1;
                foreach($trips as $key => $trip) {
                    if($count % 2 == 0) {
                        $css = 'table-info';
                    }
                    else {
                        $css = '';
                    }
                    if($trip->tracker == 5 || $trip->tracker == 6) { $status = 'On Journey'; $className = 'icon-spinner2 spinner'; }
                    if($trip->tracker == 7) { $status = 'At Destination'; $className = 'icon-truck'; }
                    if($trip->tracker == 8) { $status = 'Offloaded'; $className = 'icon-checkmark2'; }

                    $response.='<tr class="'.$css.' text-center">
                        <td>'.$count++.'</td>
                        <td>
                            '.$trip->trip_id.'
                            <i class="font-size-xs ml-1 '.$className.'" title="'.$status.'"></i>
                            <span class="d-none">'.$status.'</span>
                        </td>
                        <td>'.$trip->company_name.'</td>
                        <td>'.date('d-m-Y', strtotime($trip->gated_out)).'</td>
                        <td>'.$trip->truck_no.'</td>
                        <td>'.$trip->exact_location_id.'</td>
                        <td>';
                            foreach($waybillCollections as $waybillCollected) {
                                if($waybillCollected->trip_id == $trip->id) {
                                    $response.= $waybillCollected->sales_order_no.'<br>';
                                }
                            }
                        $response.='</td>
                        <td>';
                        foreach($waybillCollections as $waybillCollected) {
                            if($waybillCollected->trip_id == $trip->id) {
                                $response.= $waybillCollected->invoice_no.'<br>';
                            }
                        }
                        $response.='</td>
                        <td>
                            <input type="checkbox" class="receivedTripsSelected" name="tripIds[]" value="'.$trip->id.'" />
                        </td>
                    </tr>';
                }
            }
            else {
                $response.='<tr class="font-size-sm font-weight-semibold">
                    <td colspan="7">You have no pending trip to invoice.</td>
                </tr>';
            }

            $response.'</tbody>
            </table>';
            

        return $response;
    }

    public function receiveWaybillsBulk(Request $request) {
        if(!count($request->tripIds)) {
            return 'aborted';
        }
        else {
            $tripIds = $request->tripIds;
            foreach($tripIds as $key => $trip_id) {
                $waybillStatus = tripWaybillStatus::WHERE('trip_id', $trip_id)->GET()->FIRST();
                $waybillStatus->comment = 'Recieved';
                $waybillStatus->waybill_status = TRUE;
                $waybillStatus->save();
            }
            return 'received';
        }
    }

    public function changeInvoiceStatus(Request $request) {
        $invoice_no = $request->invoice_no;
        $trips = completeInvoice::SELECT('trip_id')->WHERE('invoice_no', $invoice_no)->GET();
        foreach($trips as $key=> $tripId) {
            $tripInvoiceStatus = tripWaybillStatus::WHERE('trip_id', $tripId->trip_id)->GET()->FIRST();
            $tripInvoiceStatus->invoice_status = !$tripInvoiceStatus->invoice_status;
            $tripInvoiceStatus->save();
        }
        
        return 'statusChanged';
    }

    public function updatePoNumber(Request $request) {
        $checker = completeInvoice::WHERE('completed_invoice_no', $request->invoice_no)->GET();
        if(count($checker) > 0) {
            DB::UPDATE(
                DB::RAW(
                    'UPDATE tbl_kaya_complete_invoices SET po_number = "'.$request->po_number.'" WHERE completed_invoice_no = "'.$request->invoice_no.'"'
                )
            );
            return 'updated';
        }
        else {
            return 'not_found';
        }
    }

    public function getInvoicePaymentHistory(Request $request) {
        $invoiceNo = $request->invoice_no;
        $paymentDates = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT date_paid FROM tbl_kaya_trip_payment_breakdowns WHERE invoice_no = "'.$invoiceNo.'"'
            )
        );
        $paymentHistory = '';
        foreach($paymentDates as $paymentDate) {
            $getAmountPaid = PaymentBreakdown::WHERE('invoice_no', $invoiceNo)->WHERE('date_paid', $paymentDate->date_paid)->GET()->SUM('amount');
            $paymentHistory.='
                <span class="bg-primary p-2 font-weight-bold">'.$paymentDate->date_paid.'</span>
                <span class="bg-success p-2 font-weight-bold" style="margin-left:-5px">
                    &#x20a6;'.number_format($getAmountPaid, 2).'
                </span>
                <span title="DELETE payment made on '.$paymentDate->date_paid.'" id="'.$invoiceNo.'" class="removePayment" name="'.$paymentDate->date_paid.'">
                    <i class="icon-cancel-circle2 p-1  mr-2 pointer text-danger"></i>
                </span>
            ';
        }
        return $paymentHistory;
    }

    public function deletePaymentBreakDown(Request $request) {
        $paymentDate = $request->paymentDate;
        $invoiceNo = $request->invoice_no;

        $payments = PaymentBreakdown::WHERE('date_paid', $paymentDate)->WHERE('invoice_no', $invoiceNo)->GET();
        foreach($payments as $payment) {
            $trip = trip::findOrFail($payment->trip_id);
            $trip->amount_paid -= $payment->amount;
            $trip->save();
            $pay = PaymentBreakdown::findOrFail($payment->id);
            $pay->delete();
        }
        return 'updated';
    }

    public function addMoreIncentivesOnInvoice(Request $request) {
        $invoiceNo = $request->invoiceNo;

        $trips = DB::SELECT(
            DB::RAW(
                'SELECT b.id, b.trip_id, a.invoice_no, b.`exact_location_id`, c.incentive_description, c.amount FROM `tbl_kaya_complete_invoices` a JOIN tbl_kaya_trips b ON a.trip_id = b.id JOIN tbl_kaya_incentives c ON b.exact_location_id = c.exact_location WHERE completed_invoice_no = "'.$invoiceNo.'"'
            )
        );

        
        if(count($trips) > 0) {
            $response = '
            <div class="row">';
            foreach($trips as $trip) {
                $response.='
                <input type="hidden" name="tripIds[]" value="'.$trip->id.'" />
                <div class="col-md-4">
                    <p class="mt-2 font">'.$trip->trip_id.' - '.$trip->exact_location_id.'</p>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control amounts" name="amount[]" placeholder="Amount">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="description[]" placeholder="Description">
                </div>';
                
            }
            $response.='
            <div class="col-md-12">
                <button class="btn-primary addMoreIncentives_">Add Incentive</button>
            </div>
            <div class="col-md-12 mt-3 table-responsive" id="responsePlaceholder">
                '.$this->allIncentiveOnInvoice($invoiceNo).'
            </div>
            </div>';
        }
        else {
            $response = 'None of the destination on this invoice have incentive set up for.';
        }
        return $response;
    }


    public function allIncentiveOnInvoice($invoiceNo) {
        $incentives = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.incentive_description AS dcp, b.trip_id, b.exact_location_id, a.amount FROM tbl_kaya_trip_incentives a JOIN tbl_kaya_trips b ON a.trip_id = b.id WHERE a.trip_id IN (SELECT trip_id FROM tbl_kaya_complete_invoices WHERE completed_invoice_no = "'.$invoiceNo.'") ORDER BY b.trip_id ASC'
            )
        );
        $response ='
            <table class="table table-condensed">
                <tr>
                    <th class="text-center">#</th>
                    <th>Trip ID</th>
                    <th class="text-center">Destination</th>
                    <th class="text-center">Remark</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">Remove</th>
                </tr>
                <tbody>';
                if(count($incentives) > 0) {
                    $count = 1;
                    foreach($incentives as $incentive) {
                        $response.='
                            <tr>
                                <td class="text-center">'.$count++.'</td>
                                <td>'.$incentive->trip_id.'</td>
                                <td class="text-center">'.$incentive->exact_location_id.'</td>
                                <td class="text-center">'.$incentive->dcp.'</td>
                                <td class="text-center">'.number_format($incentive->amount, 2).'</td>
                                <td class="text-center pointer">
                                    <i class="icon-x removeIncentive" id="'.$incentive->id.'"></i>
                                </td>
                            </tr>
                        ';
                    }
                }
                else {
                    $response.='
                        <tr>
                            <td colspan="5">No incentive as been added.</td>
                        </tr>
                    ';
                }
            
        $response.='
                </tbody>
            </table>';

        return $response;
    }

    public function addMoreIncentiveOnInvoice(Request $request) {
        $tripIdListings = $request->tripIds;
        $amountListings = $request->amount;
        $descriptionListings = $request->description;

        foreach($amountListings as $key => $amount) {
            if( (isset($amount) && $amount != '')  && 
                (isset($descriptionListings[$key]) && $descriptionListings[$key] !== '' ) 
            ) {
                tripIncentives::CREATE([
                    'trip_id' => $tripIdListings[$key],
                    'amount' => $amount,
                    'incentive_description' => $descriptionListings[$key]
                ]);
            }
        }
        return $this->allIncentiveOnInvoice($request->completeInvoiceNo_);
    }

    public function removeAddedIncentiveOnInvoice(Request $request) {
        $tripIncentiveId = $request->id;
        $tripIncentive = tripIncentives::findOrFail($tripIncentiveId);
        $tripIncentive->DELETE();
        return $this->allIncentiveOnInvoice($request->invoiceNo);
    }
}
