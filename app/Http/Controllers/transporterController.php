<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\transporterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\transporter;
use App\transporterDocuments;
use App\trip;
use App\loadingSite;
use App\truckType;
use App\trucks;
use App\drivers;
use App\product;
use App\client;
use App\transporterRate;
use App\tripPayment;
use App\bulkPayment;
use Mail;
use App\offloadWaybillRemark;
use App\tripWaybill;
use App\PaymentHistory;
use App\User;
use Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class transporterController extends Controller
{
    public function index() {
        $transporters = transporter::ORDERBY('transporter_name')->GET();
        $users = User::GET();
        return view('transportation.transporter', 
            compact(
                'transporters',
                'users'
            )
        );
    }

    public function store(Request $request) {
        $check = transporter::WHERE('email', $request->email)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $record = transporter::CREATE($request->all());
            $transporterId = $record->id;

            $documents = $request->file('document');
            $documentDescriptions = $request->description;

            if($request->hasFile('document')) {
                foreach($documents as $key=> $uploadedDocument){
                    if(isset($uploadedDocument) && $documentDescriptions[$key] != ''){
                        $transporterDocuments = transporterDocuments::CREATE(['transporter_id' => $transporterId, 'description' => $documentDescriptions[$key]]);
                        
                        $name = base64_encode($transporterId).'-'.str_slug($documentDescriptions[$key]).'.'.$uploadedDocument->getClientOriginalExtension();
                        $documents_path = public_path('assets/img/transporters/documents');
                        $documentPath = $documents_path.'/'.$name;
                        $uploadedDocument->move($documents_path, $name);
                        $transporterDocuments->document = $name;
                        $transporterDocuments->save();

                    }
                }
            }
            return 'saved';
        } 
    }

    public function edit($id) {
        $transporters = transporter::ORDERBY('transporter_name')->GET();
        $transporterDocuments = transporterDocuments::WHERE('transporter_id', $id)->GET(); 
        $users = User::GET();
        $recid = transporter::findOrFail($id);
        return view('transportation.transporter', 
            compact(
                'transporters',
                'recid',
                'transporterDocuments',
                'users'
            )
        );
    }

    public function update(Request $request, $id) {
        $check = transporter::WHERE('email', $request->email)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = transporter::findOrFail($id);
            $recid->UPDATE($request->all());

            $documents = $request->file('document');
            $documentDescriptions = $request->description;

            if($documentDescriptions){
                foreach($documentDescriptions as $key => $descriptions){
                    if(isset($descriptions) && $descriptions != ''){
                        $recid = transporterDocuments::firstOrNew(['transporter_id' => $id, 'description' => $descriptions]);
                        // $recid->transporter_id = $id;
                        $recid->description = $descriptions;     
                        $recid->save();
                    }

                    if($request->hasFile('document')) {
                        if(isset($request->document[$key]) && $request->document[$key] != ''){
                            $updateRecord = transporterDocuments::firstOrNew(['description' => $descriptions]);
                            $updateRecord->transporter_id = $id;
                            $name = base64_encode($id).'-'.str_slug($descriptions).'.'.$request->document[$key]->getClientOriginalExtension();
                            $documents_path = public_path('assets/img/transporters/documents');
                            $documentPath = $documents_path.'/'.$name;
                            $request->document[$key]->move($documents_path, $name);
                            $updateRecord->document = $name;
                            $updateRecord->save();
                        }
                    }
                }
            }

            return 'updated';
        }
    }

    public function destroy() {

    }

    public function deleteTransporterDocument($id) {
        $documentName = transporterDocuments::findOrFail($id);
        $documentName->destroy($id);
        $path = $_SERVER['DOCUMENT_ROOT'].'/assets/img/transporters/documents/'.$documentName->document;
        unlink($path);
        return 'deleted';
        
    }

    public function paginate($items, $perPage = 100, $page = null, $options = []) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $pagination = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        $path = url('/').'/request-transporter-payment?page='.$page;
        return $pagination->withPath($path);
    }

    public function requestForPayment() {
        $user = Auth::user();
        $advancePaymentRequests = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, d.bank_name, d.account_name, d.account_number, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id  WHERE  a.trip_status = \'1\' AND advance_paid = \'FALSE\' AND tracker >= 4 AND account_officer_id = "'.$user->id.'" ORDER BY a.trip_id DESC'
            )
        );

        $myCollectionObj = collect($advancePaymentRequests);
        $advancePaymentRequest = $this->paginate($myCollectionObj);
        
        $allpendingbalanceRequests = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.standard_advance_rate, a.balance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.id AS tripid, b.*, c.company_name, d.state, f.transporter_name, f.phone_no, f.bank_name, f.account_name, f.account_number, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id WHERE a.advance_paid = TRUE and a.balance_paid = FALSE AND account_officer_id = "'.$user->id.'" ORDER BY b.trip_id DESC'
            )
        );
        
        $tripsBalance = [];
        $balanceWaybills = [];
        if(count($allpendingbalanceRequests)) {
            foreach($allpendingbalanceRequests as $balanceRequest) {
                $tripsBalance[] = offloadWaybillRemark::WHERE('trip_id', $balanceRequest->tripid)->GET();
            }
            foreach($tripsBalance as $balanceWaybill_lists) {
                foreach($balanceWaybill_lists as $uploadedWaybill) {
                    $balanceWaybills[] = $uploadedWaybill;
                }
            }
        }
        
        return view('finance.transporter-payment-request.request-payment', compact('advancePaymentRequest', 'allpendingbalanceRequests', 'balanceWaybills'));
    }

    public function advanceRequestPayment(Request $request) {
        $advanceRequestedAt = date('d-m-Y, H:i:s A');
        $recid = trip::findOrFail($request->trip_id);
        $transporter = transporter::findOrFail($recid->transporter_id);
        if($transporter->transporter_status == FALSE) {
            return 'blackListed';
        }
        $user_id = $request->user_id;
        $tripRate = $recid->transporter_rate;
        $standardAdvanceRate = $tripRate * 0.7;
        $standardBalanceRate = $tripRate * 0.3;
        $available_balance = 0;

        $recid->advance_request = TRUE;
        $recid->advance_requested_by = $user_id;
        $recid->advance_requested_at = $advanceRequestedAt;
        
        $transporterChunkPayment = bulkPayment::WHERE('transporter_id', $recid->transporter_id)->GET();
        if(sizeof($transporterChunkPayment)>0) {
            $current_balance = $transporterChunkPayment[0]->balance;
            if($current_balance >= $standardAdvanceRate){
                $amountPayable = $standardAdvanceRate;
                $available_balance = $current_balance - $standardAdvanceRate;
            }
            else {
                $amountPayable = $current_balance - $standardAdvanceRate;
            }
        }
        else{
            $current_balance = 0;
            $amountPayable = $standardAdvanceRate;
        }
    
        $client = client::findOrFail($recid->client_id);
        $clientName = $client->company_name;
        $customerAddress = $recid->customer_address;
        $customer_no = $recid->customer_no;
        $customer_name = $recid->customers_name;

        $truck = trucks::findOrFail($recid->truck_id);
        $truckTypeId = $truck->truck_type_id;
        $truckType = truckType::findOrFail($truckTypeId);

        $exactState = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_state_id = '.$recid->destination_state_id.' '));
        $state = $exactState[0]->state;

        $transporter = transporter::FindOrFail($recid->transporter_id);

        $product = product::findOrFail($recid->product_id);

        $payment = tripPayment::firstOrNew(['trip_id' => $recid->id]);
        $payment->client_id = $recid->client_id;
        $payment->transporter_rate_id = $recid->exact_location_id;
        $payment->transporter_id = $recid->transporter_id;
        $payment->amount = $tripRate;
        $payment->standard_advance_rate = $standardAdvanceRate;
        $payment->standard_balance_rate = $standardBalanceRate;
        $payment->advance = $standardAdvanceRate;
        $payment->balance = $standardBalanceRate;
        $payment->save();

        $tripId = $recid->trip_id;

        //create a payment history log here.
        $payment = PaymentHistory::CREATE(['trip_id' => $request->trip_id]);
        $payment->amount = $standardAdvanceRate;
        $payment->payment_mode = 'Advance Requested';
        $payment->save();
        $recid->save();

        try{
            // Mail::send('initiate-payment', array(
            //     'tripid' => $tripId,
            //     'destination' => $recid->exact_location_id,
            //     'transporter_name' => $transporter->transporter_name,
            //     'tonnage' => $truckType->tonnage,
            //     'truck_no' => $truck->truck_no,
            //     'product_name' => $product->product,
            //     'customer_name' => $recid->customers_name,
            //     'current_balance' => $current_balance,
            //     'standardAdvanceRate' => $standardAdvanceRate,
            //     'amountPayable' => $amountPayable,
            //     'available_balance' => $available_balance,
            //     'bank_name' => $transporter->bank_name,
            //     'account_number' => $transporter->account_number,
            //     'account_name' => $transporter->account_name,
            // ), function($message) use ($request, $tripId) {
            //     $message->from('no-reply@kayaafrica.co', 'KAYA-FINACE');
            //     $message->to('kayaafricafin@gmail.com', 'Finance')->subject('Advance Request for : '.$tripId);
            // });
            return 'requestSent';
        } catch(\Throwable $e) {
            throw $e;
        }
        
        
    }

    public function uploadCollectedWaybillProof(Request $request) {
        foreach($request->file as $key => $signedWaybill){
            if($request->hasFile('file')) {
                $signedWaybill = $request->file('file');
                $name = 'signed-waybill-'.$request->trip_id.'-'.$key.'.'.$signedWaybill[$key]->getClientOriginalExtension();
                $destination_path = public_path('assets/img/signedwaybills/');
                $waybillPath = $destination_path."/".$name;
                $signedWaybill[$key]->move($destination_path, $name);
                $collectedWaybill = offloadWaybillRemark::firstOrNew(['trip_id' => $request->trip_id, 'received_waybill' => $name]);
                $collectedWaybill->waybill_collected_status = TRUE;
                $collectedWaybill->waybill_remark = $request->remark;
                $collectedWaybill->save();
            }
        }
        return 'updated';

    }

    public function balanceRequestPayment(Request $request) {
        $balanceRequestedAt = date('d-m-Y, H:i:s A');
        $checkTransporterStatus = trip::SELECT('transporter_id')->WHERE('id', $request->trip_id)->GET()->FIRST();
        $transporter = transporter::findOrFail($checkTransporterStatus->transporter_id);
        if($transporter->transporter_status == FALSE) {
            return 'blackListed';
        } else {
            $checkOffloadWaybill = offloadWaybillRemark::WHERE('trip_id', $request->trip_id)->GET()->COUNT();
            if($checkOffloadWaybill) {
                $id = $request->trip_id;

                $balanceRequest = trip::findOrFail($id);
                $balanceRequest->balance_request = TRUE;
                $balanceRequest->balance_requested_by = $request->user_id;
                $balanceRequest->balance_requested_at = $balanceRequestedAt;
                $balanceRequest->save();

                $newChunkBalance = 0;
                $data = $this->transactQueryBalance($id);
                $balanceRequest = $data[0]->balance;
                $transporter_id = $data[0]->transporter_id;
                $trip_id = str_replace('KAID', '', $data[0]->trip_id);
                $transporterChunkPayment = bulkPayment::WHERE('transporter_id', $transporter_id)->GET();
                if(sizeof($transporterChunkPayment)>0) {
                    $availableBalance = $transporterChunkPayment[0]->balance;
                    if($availableBalance >= $balanceRequest){
                        $amountPayable = $balanceRequest;
                        $newChunkBalance = $availableBalance - $balanceRequest;
                    }
                    else {
                        $amountPayable = $availableBalance - $balanceRequest;
                    }
                    $updateAccountBalance = bulkPayment::firstOrNew(['transporter_id' => $transporter_id]);
                    $updateAccountBalance->balance = $newChunkBalance;
                    $updateAccountBalance->save();
                }
                else{
                    $availableBalance = 0;
                    $amountPayable = $balanceRequest;
                }
                $getWaybillCredentials = tripWaybill::WHERE('trip_id', $trip_id)->GET();
                $tripid = $data[0]->trip_id;

                //create a payment history log here.
                $payment = PaymentHistory::CREATE(['trip_id' => $request->trip_id]);
                $payment->amount = $balanceRequest;
                $payment->payment_mode = 'Balance Requested';
                $payment->save();

                // Mail::send('initiate-balance', array(
                //     'tripid' => $tripid,
                //     'getWaybillCredentials' => $getWaybillCredentials,
                //     'destination' => $data[0]->transporter_destination.', '.$data[0]->state,
                //     'transporter_name' => $data[0]->transporter_name,
                //     'bank_name' => $data[0]->bank_name,
                //     'account_name' => $data[0]->account_name,
                //     'account_number' => $data[0]->account_number,
                //     'tonnage' => $data[0]->tonnage,
                //     'truck_no' => $data[0]->truck_no,
                //     'product_name' => $data[0]->product,
                //     'customer_name' => $data[0]->customers_name,
                //     'current_balance' => $availableBalance,
                //     'balance_request' => $balanceRequest,
                //     'amountPayable' => $amountPayable,
                //     'available_balance' => $newChunkBalance,
                // ), function($message) use ($request, $tripid) {
                //     $message->from('no-reply@kayaafrica.co', 'KAYA-FINACE');
                //     $message->to('kayaafricafin@gmail.com', 'Finance')->subject('Payment for TRIP: '.$tripid);
                // });
                return 'requestSent';
            } else {
                return 'abort';
            }
        }
    }

    function transactQueryBalance($paymentRequestId) {
        $answer = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.balance, a.standard_balance_rate, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, e.transporter_destination, f.transporter_name, f.phone_no, f.bank_name, f.account_number, f.account_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporter_rates e JOIN 
                tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id AND b.client_id = c.id AND b.destination_state_id = d.regional_state_id AND b.exact_location_id = e.transporter_destination AND b.transporter_id = f.id AND b.truck_id = g.id AND g.truck_type_id = h.id AND b.product_id = i.id WHERE b.advance_paid = true AND a.trip_id = '.$paymentRequestId.' ORDER BY b.trip_id ASC LIMIT 1'
            )
        );
        return $answer;
    }

    public function updateTransporterAccountDetails(Request $request, $id) {
        $transporterInfo = transporter::findOrFail($id);
        $transporterInfo->bank_name = $request->bankName;
        $transporterInfo->account_name = $request->accountName;
        $transporterInfo->account_number = $request->accountNumber;
        $transporterInfo->save();
        return 'updated';
    }

    public function updateTripAccountOfficerId() {
       
       $alltrips = DB::SELECT(
           DB::RAW(
               'SELECT a.id, a.transporter_id, a.account_officer_id, b.assign_user_id from tbl_kaya_trips a JOIN tbl_kaya_transporters b ON a.transporter_id = b.id'
           )
        );
        foreach($alltrips as $key=> $recid){
            $recordUpdate = trip::findOrFail($recid->id);
            $recordUpdate->account_officer_id = $recid->assign_user_id;
            $recordUpdate->save();            
        }
        return 'completed...';
    }
    
    public function updateTrRate(Request $request, $tripId) {
        $transporter_rate = $request->newTrValue;
        $tripDetails = trip::WHERE('trip_id', $tripId)->FIRST();
        $tripDetails->transporter_rate = $transporter_rate;
        $tripDetails->save();
        return 'updated';
    }
    
    public function transporterLog() {
        $users = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT(assign_user_id), b.first_name, b.last_name from tbl_kaya_transporters a JOIN users b ON a.assign_user_id = b.id'
            )
        );
        $transporters = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.first_name, b.last_name FROM tbl_kaya_transporters a JOIN users b ON a.assign_user_id = b.id ORDER BY transporter_name ASC'
            )
        );

        foreach($transporters as $specificTransporter) {
            [$transporterTripCount[]] = DB::SELECT(
                DB::RAW(
                    'SELECT COUNT(transporter_id) AS transporterTrips  FROM tbl_kaya_trips WHERE transporter_id = "'.$specificTransporter->id.'" AND trip_status = 1'
                )
            );

            $tripDocuments[] = DB::SELECT(
                DB::RAW(
                    'SELECT * FROM tbl_kaya_transporter_documents WHERE transporter_id = "'.$specificTransporter->id.'"'
                )
            );
        }

        foreach($tripDocuments as $documents) {
            foreach($documents as $document) {
                $transporterVerification[] = $document;
            }
        }
        return view('transportation.transporterlog', compact('transporters', 'transporterTripCount', 'transporterVerification', 'users'));
    }


    public function transporterStatus(Request $request) {
        $transporter_id = $request->id;
        $transporter = transporter::findOrFail($transporter_id);

        $transporter->transporter_status = !$transporter->transporter_status;
        $transporter->save();     
        if($transporter->transporter_status == 0) {
            return 'Blacklisted';
        }
        else {
            return 'Activated';
        }
    }

    public function transporterTripLog(Request $request, $transporter, $transporterId) {
        $tripInformation = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, a.gated_out, a.exact_location_id, a.customers_name, a.customer_no, a.loaded_quantity, a.loaded_weight, a.customer_address, a.tracker,  b.loading_site, c.*, d.product, e.state, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_products d JOIN tbl_regional_state e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.product_id = d.id AND a.destination_state_id = e.regional_state_id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.transporter_id = "'.$transporterId.'" AND trip_status = 1 ORDER BY a.gated_out DESC
                '
            )
        );
        $transporterInfo = transporter::findOrFail($transporterId);
        return view('transportation.transporter-trips', compact('tripInformation', 'transporterInfo'));
    }
    
    public function masterPaymentRequest() {
        $user = Auth::user();
        $advancePaymentRequest = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, d.bank_name, d.account_name, d.account_number, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id  WHERE  a.trip_status = \'1\' AND advance_paid = \'FALSE\' AND tracker >= 4 ORDER BY a.trip_id DESC LIMIT 100'
            )
        );
        
        $allpendingbalanceRequests = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.standard_advance_rate, a.balance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.id AS tripid, b.*, c.company_name, d.state, f.transporter_name, f.phone_no, f.bank_name, f.account_name, f.account_number, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id WHERE a.advance_paid = TRUE and a.balance_paid = FALSE ORDER BY b.trip_id DESC'
            )
        );
        $tripsBalance = [];
        $balanceWaybills = [];
        if(count($allpendingbalanceRequests)) {
            foreach($allpendingbalanceRequests as $balanceRequest) {
                $tripsBalance[] = offloadWaybillRemark::WHERE('trip_id', $balanceRequest->tripid)->GET();
            }
            foreach($tripsBalance as $balanceWaybill_lists) {
                foreach($balanceWaybill_lists as $uploadedWaybill) {
                    $balanceWaybills[] = $uploadedWaybill;
                }
            }
        }
        return view('finance.transporter-payment-request.all-pending-payments', compact('advancePaymentRequest', 'allpendingbalanceRequests', 'balanceWaybills'));
    }
}
