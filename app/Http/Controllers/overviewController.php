<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\driversRequest;
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
use Mail;
use App\offloadWaybillRemark;
use App\PaymentHistory;
use Auth;
use App\PaymentNotification;
use App\incentives;
use App\tripIncentives;

class overviewController extends Controller
{
    public function displaytripoverview($kayaid) {
        $trip_id = trip::SELECT('id', 'transporter_rate')->WHERE('trip_id', $kayaid)->GET();
        $client_id = trip::SELECT('client_id')->WHERE('trip_id', $kayaid)->GET();
        $exact_location_id = trip::SELECT('exact_location_id')->WHERE('trip_id', $kayaid)->GET();
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, i.company_name, i.email, i.address FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_clients i ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.client_id = i.id WHERE a.trip_id = "'.$kayaid.'" '
            )
        );
        $tripWaybills = tripWaybill::WHERE('trip_id', $trip_id[0]->id)->GET();
        $tripEvents = tripEvent::WHERE('trip_id', $trip_id[0]->id)->ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::WHERE('trip_id', $trip_id[0]->id)->GET();
        $transporterRate = $trip_id[0]->transporter_rate;
        $trippay = tripPayment::WHERE('trip_id', $trip_id[0]->id)->GET();
        
        return view('orders.trip-overview',
            compact(
                'orders',
                'tripWaybills',
                'tripEvents',
                'waybillstatuses',
                'kayaid',
                'transporterRate',
                'trip_id',
                'trippay'
            )
        );
    }

    public function paymentRequest() {
        $allpendingadvanceRequests = DB::SELECT(
            DB::RAW(
                'SELECT a.id, b.id AS tripid, a.advance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.advance_requested_at, b.destination_state_id, b.exact_location_id, b.customers_name, b.client_rate, b.transporter_rate, b.loaded_weight, b.advance_comment, c.company_name, d.state, f.account_number, f.transporter_name, f.bank_name, f.account_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product, j.first_name, j.last_name, k.loading_site FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i JOIN users j JOIN tbl_kaya_loading_sites k ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id AND j.id = b.advance_requested_by AND b.loading_site_id = k.id WHERE a.advance_paid = false ORDER BY b.trip_id DESC'
            )
        );

        $distinctAdvanceClients = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT a.client_id, company_name, COUNT(a.client_id) AS pending_advance FROM tbl_kaya_trips a JOIN tbl_kaya_trip_payments b JOIN tbl_kaya_clients c ON a.id = b.trip_id AND a.client_id = c.id WHERE b.advance_paid = FALSE GROUP BY client_id, company_name'
            )
        );
        $distinctBalanceClients = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT a.client_id, company_name, COUNT(a.client_id) AS pending_balance FROM tbl_kaya_trips a JOIN tbl_kaya_trip_payments b JOIN tbl_kaya_clients c ON a.id = b.trip_id and a.client_id = c.id  WHERE a.advance_paid = TRUE and a.balance_request = TRUE AND b.balance_paid = FALSE GROUP BY client_id, company_name'
            )
        );

        $allpendingbalanceRequests = DB::SELECT(
            DB::RAW(
                'SELECT a.id, b.id AS tripid, a.advance, a.balance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.trip_id, b.transporter_id, b.truck_id, b.balance_requested_at, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, b.transporter_rate, c.company_name, d.state, f.account_number, f.transporter_name, f.bank_name, f.account_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product, j.first_name, j.last_name, k.loading_site FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i JOIN users j JOIN tbl_kaya_loading_sites k ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id AND k.id = b.loading_site_id WHERE b.balance_requested_by = j.id AND b.advance_paid = TRUE and b.balance_request = TRUE AND a.balance_paid = FALSE ORDER BY b.trip_id DESC'
            )
        );
        $allPendingOutstandingBalance = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.balance, a.outstanding_balance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, f.account_number, f.transporter_name, f.bank_name, f.account_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id WHERE a.advance_paid = TRUE and a.balance_paid = TRUE and a.outstanding_balance > 0 ORDER BY b.trip_id DESC'
            )
        );
        
        
        $tripWaybills = [];
        $advanceWaybillInfos = [];
        
        if(count($allpendingadvanceRequests)) {
            foreach($allpendingadvanceRequests as $advanceRequest) {
                $waybillCounter =  tripWaybill::WHERE('trip_id', $advanceRequest->tripid)->GET();
                    $tripWaybills[] = $waybillCounter;
            }
        }
        
        foreach($tripWaybills as $waybills) {
            foreach($waybills as $waybill) {
                $advanceWaybillInfos[] = $waybill;
            }
        }
        
        
        if(count($allpendingbalanceRequests)){
            foreach($allpendingbalanceRequests as $balance) {
                [$waybillStatuses[]] = tripWaybillStatus::WHERE('trip_id', $balance->tripid)->GET();
            }
        }
        else{
            $waybillStatuses = [];
        }
        
        $incentives = incentives::GET();
        $waybillInfos = tripWaybill::GET();
        $chunkPayments = bulkPayment::GET();
        $statesQuery = 'SELECT * FROM tbl_regional_state WHERE regional_country_id  = \'94\' ORDER BY state ASC ';
        $states = DB::SELECT(DB::RAW($statesQuery));
        $waybillStatus = tripWaybillStatus::GET();
        $offloadedWaybill = offloadWaybillRemark::GET();

        return view('finance.payment-request', 
            compact(
                'allpendingadvanceRequests',
                'allpendingbalanceRequests',
                'waybillInfos',
                'chunkPayments',
                'states',
                'waybillStatus',
                'allPendingOutstandingBalance',
                'offloadedWaybill',
                'advanceWaybillInfos',
                'waybillStatuses',
                'incentives',
                'distinctAdvanceClients',
                'distinctBalanceClients'
            )
        );   
    }

    public function initiatePayment(Request $request, $id) {
        $newChunkBalance = 0;
        $data = $this->transactQuery($id);
        $advanceRequest = $data[0]->advance;
        $transporter_id = $data[0]->transporter_id;
        $trip_id = str_replace('KAID', '', $data[0]->trip_id);
        $standardAdvanceRate = $data[0]->standard_advance_rate;
        $available_balance = 0;

        $transporterChunkPayment = bulkPayment::WHERE('transporter_id', $data[0]->transporter_id)->GET();
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
        
        $tripid = $data[0]->trip_id;
        $getWaybillCredentials = tripWaybill::WHERE('trip_id', $trip_id)->GET();

        $transporter = transporter::FindOrFail($transporter_id);

        $payment = tripPayment::findOrFail($id);
        $payment->save();

        Mail::send('initiate-payment', array(
            'tripid' => $tripid,
            'getWaybillCredentials' => $getWaybillCredentials,
            'destination' => $data[0]->state.', '.$data[0]->transporter_destination,
            'transporter_name' => $data[0]->transporter_name,
            'tonnage' => $data[0]->tonnage,
            'truck_no' => $data[0]->truck_no,
            'product_name' => $data[0]->product,
            'customer_name' => $data[0]->customers_name,
            'current_balance' => $current_balance,
            'standardAdvanceRate' => $standardAdvanceRate,
            'amountPayable' => $amountPayable,
            'available_balance' => $available_balance,
            'bank_name' => $transporter->bank_name,
            'account_number' => $transporter->account_number,
            'account_name' => $transporter->account_name,

        ), function($message) use ($request, $tripid) {
            $message->from('no-reply@kayaafrica.co', 'KAYA-FINACE');
            $message->to('kayaafricafin@gmail.com', 'Finance')->subject('Payment for TRIP: '.$tripid);
        });

        return 'approved';
    }

    public function initiateBalance(Request $request, $id) {
        $salesOrderNo = '';
        $invoiceNo = '';
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
        $payment = tripPayment::findOrFail($id);
        // $payment->balance_status = '2';
        $payment->save();

        Mail::send('initiate-balance', array(
            'tripid' => $tripid,
            'getWaybillCredentials' => $getWaybillCredentials,
            'destination' => $data[0]->transporter_destination.', '.$data[0]->state,
            'tranporter_name' => $data[0]->transporter_name,
            'tonnage' => $data[0]->tonnage,
            'truck_no' => $data[0]->truck_no,
            'product_name' => $data[0]->product,
            'customer_name' => $data[0]->customers_name,
            'current_balance' => $availableBalance,
            'balance_request' => $balanceRequest,
            'amount_payable' => $amountPayable,
            'available_balance' => $newChunkBalance,
        ), function($message) use ($request, $tripid) {
            $message->from('no-reply@kayaafrica.co', 'KAYA-FINACE');
            $message->to('kayaafricafin@gmail.com', 'Finance')->subject('Payment for TRIP: '.$tripid);
        });

        return 'approved';
    }

    private $SMS_SENDER = 'Kaya';
    private $RESPONSE_TYPE = 'json';
    private $SMS_USERNAME = 'odejobi.olushola@kayaafrica.co';
    private $SMS_PASSWORD = 'Likemike009@@';

    public function approveAdvancePayment(Request $request) {
        $advanceId = $request->approveAdvance;
        foreach($advanceId as $id) {
            $recid = tripPayment::findOrFail($id);
            $recid->advance_paid = TRUE;
            $advance = $recid->advance;
            $getTrip = trip::findOrFail($recid->trip_id);
            $getTrip->advance_paid = TRUE;
            $transporterInfo = transporter::findOrFail($getTrip->transporter_id);
            $truckInfo = trucks::findOrFail($getTrip->truck_id);
            $truckType = truckType::findOrFail($truckInfo->truck_type_id);
            
            $getAccountName = explode(' ', $transporterInfo->account_name);
            if(count($getAccountName) <= 1) {
                $transporter = $getAccountName[0];
            }
            else {
                list($firstName, $lastNameInitial) = $getAccountName;
            }

            //create a payment history log here.
            $payment = PaymentNotification::firstOrNew(['trip_id' => $recid->trip_id, 'payment_for' => 'Advance']);
            $payment->amount = $advance;
            $payment->uploaded_at = DATE('Y-m-d H:i:s');
            $payment->uploaded_by = Auth::user()->id;

            $recid->save();
            $getTrip->save();
            $payment->save();

            $transporterPhoneNo = $transporterInfo->phone_no;
            $messageContent = 'Hi '.$firstName.', Advance of NGN'.number_format($advance).' for '.$truckInfo->truck_no.'; '.$truckType->tonnage/1000 .'T, '.$getTrip->exact_location_id.' has been processed.';

            $this->initiateSms($transporterPhoneNo, $messageContent);
        }

        return 'approved';
    }


    public function initiateSms($receiver, $content) {
        $isError = 0;
        $errorMessage = true;

        //preparing post paramters
        $postData = array(
            'username' => $this->SMS_USERNAME,
            'password' => $this->SMS_PASSWORD,
            'message' => $content,
            'sender' => $this->SMS_SENDER,
            'mobiles' => $receiver,
            'response' => $this->RESPONSE_TYPE
        );
        $url = 'https://portal.nigeriabulksms.com/api/';
        $ch  = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData 
        ));
        // Ignore SSL Certificate Verication
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        //get response
        $output = curl_exec($ch);
        
        //print error if there are any
        if(curl_errno($ch)) {
            $isError = true;
            $errorMessage = curl_error($ch);
        }
        curl_close($ch);
        if($isError) {
            return array('error' => 1, 'message' => $errorMessage);
        }
        else{
            return array('error' => 0);
        }
    }

    public function sendPaymentNotification() {
        $requests = $this->initiateSms('+2348099453540', 'Hi Timi, there are some payments awaiting your approval.');
        return $requests;

    }

   
    function transactQuery($paymentRequestId) {
        $answer = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.standard_advance_rate, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, e.transporter_destination, f.*, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporter_rates e JOIN 
                tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id AND b.exact_location_id = e.transporter_destination and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id WHERE advance_paid = false AND a.id = '.$paymentRequestId.' ORDER BY b.trip_id ASC '
            )
        );
        return $answer;
    }

    function transactQueryBalance($paymentRequestId) {
        $answer = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.balance, a.standard_balance_rate,  b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, e.transporter_destination, f.*, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporter_rates e JOIN 
                tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id AND b.client_id = c.id AND b.destination_state_id = d.regional_state_id AND b.exact_location_id = e.transporter_destination AND b.transporter_id = f.id AND b.truck_id = g.id AND g.truck_type_id = h.id AND b.product_id = i.id WHERE advance_paid = true AND a.id = '.$paymentRequestId.' ORDER BY b.trip_id ASC '
            )
        );
        return $answer;
    }
    
    public function updateClientRate(Request $request, $tripId) {
        $updateClientRate = trip::WHERE('trip_id', $tripId)->FIRST();
        $updateClientRate->client_rate = $request->client_rate;
        $updateClientRate->save();
        return 'updated';
    }

    public function updateTransporterRate(Request $request, $tripId) {
        $updateTransporterRate = trip::WHERE('trip_id', $tripId)->FIRST();
        $updateTransporterRate->transporter_rate = $request->transporter_rate;

        $updatedAdvance = 0.7 * $request->transporter_rate;
        $updatedBalance = 0.3 * $request->transporter_rate;

        $tripPaymentRecord = tripPayment::WHERE('trip_id', $updateTransporterRate->id)->FIRST();
        $tripPaymentRecord->amount = $request->transporter_rate;
        $tripPaymentRecord->advance = $updatedAdvance;
        $tripPaymentRecord->balance = $updatedBalance;
        $tripPaymentRecord->standard_advance_rate = $updatedAdvance;
        $tripPaymentRecord->standard_balance_rate = $updatedBalance;
        $tripPaymentRecord->save();
        $updateTransporterRate->save();

        return 'updated';
    }

    public function updateTransporterRateOnBalance(Request $request) {
        $payment = tripPayment::findOrFail($request->payment_id);
        $payment->balance = $request->transporter_rate - $payment->advance;

        $trip = trip::findOrFail($payment->trip_id);
        $trip->transporter_rate = $request->transporter_rate;
        
        $trip->save();
        $payment->save();

        return 'updated';
    }

    public function updateTransporterRateTopup(Request $request, $tripId) {
        
        $updateTransporterRate = trip::WHERE('trip_id', $tripId)->FIRST();
        $updateTransporterRate->transporter_rate = $request->transporter_rate;

        $tripPaymentRecord = tripPayment::WHERE('trip_id', $updateTransporterRate->id)->FIRST();
        $tripPaymentRecord->amount = $request->transporter_rate;

        $updateTransporterRate->save();
        $tripPaymentRecord->save();

        return 'updated';

    }

    public function tripIncentives(Request $request) {
        $tripId = $request->trip_id;
        return $this->showIncentives($tripId);
    }

    public function addIncentives(Request $request) {
        $incentiveLabel = $request->incentive_desc;
        $incentiveAmount = $request->incentiveAmount;
        $paymentId = tripPayment::findOrFail($request->id);
        $tripId = $paymentId->trip_id;
        
        tripIncentives::CREATE([
            'trip_id' => $tripId,
            'incentive_description' => $incentiveLabel,
            'amount' => $incentiveAmount
        ]);
        return $this->showIncentives($tripId);
    }

    public function removeIncentive(Request $request, $incentiveId) {
        $tripId = $payment->trip_id;
        $tripIncentive = tripIncentives::findOrFail($incentiveId);
        $tripIncentive->DELETE();
        return $this->showIncentives($tripId);
    }

    function showIncentives($paymentId) {
        $thisPayment = tripPayment::findOrFail($paymentId);
        $tripId = $thisPayment->trip_id;
        $tripIncentives = tripIncentives::WHERE('trip_id', $tripId)->GET();
        $response = '
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';
        
        if(count($tripIncentives) > 0) {
            $count = 0;
            foreach($tripIncentives as $incentive) {
                $count++;
                $response.='
                    <tr class="text-center">
                        <td>'.$count.'</td>
                        <td>'.$incentive->incentive_description.'</td>
                        <td>'.number_format($incentive->amount, 2).'</td>
                        <td>
                            <i class="icon-trash text-danger pointer removeIncentive" id="'.$incentive->id.'" attr="'.$incentive->trip_id.'"></i>
                        </td>
                    </tr>
                ';
            }
        }
        else{
            $response.= '
                <tr>
                    <td colspan="4">You have not added any incentive for this trip</td>
                </tr>
            ';
        }
        $response.='
            <tbody>
            </table>
        ';

        return $response;
        
    }
}
