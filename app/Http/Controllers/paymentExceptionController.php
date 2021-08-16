<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\tripPayment;
use App\transporterRate;
use App\trip;
use App\tripWaybillStatus;
use Illuminate\Support\Facades\DB;
use App\bulkPayment;
use App\tripWaybill;
use Mail;
use App\transporter;
use App\PaymentHistory;
use Auth;
use App\PaymentNotification;
use App\trucks;
use App\truckType;

class paymentExceptionController extends Controller
{
    public function advanceException(Request $request) {        
        $validatedata = $request->validate([
            'advance_exception' => 'required',
            'total_amount' => 'required',
            'payid' => 'required'
        ]);

        $recid = tripPayment::findOrFail($request->payid);
        $advanceExceptionType = $request->advance_exception;
        if($advanceExceptionType == 2) {
            $validatedata = $request->validate([
                'new_advance_rate' => 'required | integer',
                'percentile_remark' => 'required|string'
            ]);
            $percentage = 100;
            $amount = $request->total_amount;
            $advance = ($request->new_advance_rate / $percentage) * $amount;
            $balanceRate = $percentage - $request->new_advance_rate;
            $balance = ($balanceRate / $percentage) * $amount;

            $recid->advance = $advance;
            $recid->balance = $balance;
            $recid->exception = $advanceExceptionType;
            $recid->remark = $request->percentile_remark;
            $recid->save();
            return 'updated';
        }

        if($advanceExceptionType == 3) {
            $validatedata = $request->validate([
                'pay_in_full' => 'required|boolean',
                'fullpayment_remarks' => 'required|string',
            ]);
            
            $recid->advance = $request->total_amount;
            $recid->remark = $request->fullpayment_remarks;
            $recid->balance = 0;
            $recid->balance_paid = true;
            $recid->exception = $advanceExceptionType;
            $recid->save();
            return 'updated';
        }

        if($advanceExceptionType == 4) {
            $validatedata = $request->validate([
                'advanceTobeManuallyPaid' => 'required | string' 
            ]);
           $recid->advance = $request->advanceTobeManuallyPaid;
           $recid->balance = $request->probableBalance;
           $recid->save();

           return 'updated';
        }
    }

    public function getStates(Request $request) {

        $answer = '<select class="form-control" name="exact_location_id" id="exactLocationId">
            <option value="0">Choose New Location</option>
        ';
        $getLocations = transporterRate::WHERE('transporter_to_state_id', $request->regional_state_id)->GET();
        foreach($getLocations as $exactlocation) {
            $answer.='<option value="'.$exactlocation->id.'">'.$exactlocation->transporter_destination.'</option>';
        }
        $answer.='</select>';
        return $answer;
    }

    public function getNewAmount(Request $request) {
        $recid = transporterRate::findOrFail($request->exact_location_id);
        return $recid->transporter_amount_rate;
    }

    public function balanceException(Request $request) {
        
        $recid = tripPayment::findOrFail($request->balanceTripId);
        $newRateToBeUsed = $request->newTransportRateAmount;
        $advancePaidBefore = $request->advancePaid;
        $trip_id = str_replace("KAID", "", $request->trip_id);
        $transporter_id = $request->transporter_id;
        if($request->balanceExceptionChecker == 1){
            if($newRateToBeUsed < $advancePaidBefore) {
                $indebtedBalance = $newRateToBeUsed - $advancePaidBefore;
                $recid->remark = 'Full payment! Transporter to refund '.abs($indebtedBalance);
                $recid->exception = '3';
                $recid->balance = 0;
                $recid->balance_paid = true;
                $recid->amount = $newRateToBeUsed;
                $recid->standard_advance_rate = 0.7 * $newRateToBeUsed;
                $recid->standard_balance_rate = 0.3 * $newRateToBeUsed;
                $recid->save();

                $tripRecord = trip::findOrFail($trip_id);
                $tripRecord->exact_location_id = $request->exact_location_id;
                $tripRecord->destination_state_id = $request->state_id;
                $tripRecord->save();

            } else {
                $newUpdatedBalance = $newRateToBeUsed - $advancePaidBefore;
                $recid->amount = $newRateToBeUsed;
                $recid->standard_advance_rate = 0.7 * $newRateToBeUsed;
                $recid->standard_balance_rate = 0.3 * $newRateToBeUsed;
                $recid->balance = $newUpdatedBalance;
                $recid->exception = '4';
                $recid->save();
                $tripRecord = trip::findOrFail($trip_id);
                $tripRecord->exact_location_id = $request->exact_location_id;
                $tripRecord->destination_state_id = $request->state_id;
                $tripRecord->save();
            }
        }

        if($request->balanceExceptionChecker == 2) {
            $actualBalanceBalance = $request->actualBalanceAmount;
            $balanceBitPartPay = $request->balancePartPayment;
            $outstandingBalance = $request->outstandingBalance;
            $actualBalanceBalance.' '.$balanceBitPartPay.' '.$outstandingBalance;
            
            $recid->balance = $balanceBitPartPay;
            $recid->outstanding_balance = $outstandingBalance;
            $recid->save();

            if($outstandingBalance < 0) {
                $bulkPayment = bulkPayment::WHERE('transporter_id', $transporter_id)->first();
                if($bulkPayment) {
                    $bulkPayment->balance = $bulkPayment->balance + $outstandingBalance;
                    $bulkPayment->save();
                } else {
                    $bulkPayment = bulkPayment::CREATE(['transporter_id' => $transporter_id, 'balance' => $outstandingBalance, 'amount_credited' => $outstandingBalance, 'date_uploaded' => date('Y-m-d, h:i A'), 'date_approved' => date('Y-m-d, h:i A'), 'approval_status' => true]);
                }

            }
            
        }
        return 'saved';
    }

    public function balanceInitiation(Request $request) {
        $trip_id = str_replace('KAID', '', $request->tripIdofBalanceInitiate);
        $getWaybillStatus = tripWaybillStatus::WHERE('trip_id', $trip_id)->GET();
        $proceed = $request->proceed_confirmation;
        if(count($getWaybillStatus) <= 0 && $proceed == 0) {
            return 'no_record';
        } 
         
        else {
            
            $newChunkBalance = 0;
            $id = $request->balanceInitiateId;
            $data = $this->transactQueryBalance($id);
            $balanceRequest = $data[0]->balance;
            $transporter_id = $data[0]->transporter_id;
            $available_balance = 0;
            $standardBalanceRate = $data[0]->standard_balance_rate;
           
            $transporterChunkPayment = bulkPayment::WHERE('transporter_id', $data[0]->transporter_id)->GET();
                if(sizeof($transporterChunkPayment)>0) {
                    $current_balance = $transporterChunkPayment[0]->balance;
                    if($current_balance >= $balanceRequest){
                        $amountPayable = $balanceRequest;
                        $available_balance = $current_balance - $balanceRequest;
                    }
                    else {
                        $amountPayable = $current_balance - $balanceRequest;
                    }
                }
                else{
                    $current_balance = 0;
                    $amountPayable = $balanceRequest;
                }

            $getWaybillCredentials = tripWaybill::WHERE('trip_id', $trip_id)->GET();
            $transporter = transporter::FindOrFail($transporter_id);

            $tripid = $data[0]->trip_id;
            $payment = tripPayment::findOrFail($id);
            $payment->save();

            Mail::send('initiate-balance', array(
                'tripid' => $tripid,
                'getWaybillCredentials' => $getWaybillCredentials,
                'destination' => $data[0]->state.', '.$data[0]->transporter_destination,
                'transporter_name' => $data[0]->transporter_name,
                'tonnage' => $data[0]->tonnage,
                'truck_no' => $data[0]->truck_no,
                'product_name' => $data[0]->product,
                'customer_name' => $data[0]->customers_name,
                'current_balance' => $current_balance,
                'standardBalanceRate' => $standardBalanceRate,
                'balance_request' => $balanceRequest,
                'amountPayable' => $amountPayable,
                'available_balance' => $available_balance,
                'bank_name' => $transporter->bank_name,
                'account_number' => $transporter->account_number,
                'account_name' => $transporter->account_name,

            ), function($message) use ($request, $tripid) {
                $message->from('no-reply@kayaafrica.co', 'KAYA-FINACE');
                $message->to('kayaafricafin@gmail.com', 'Finance')->subject('Payment for TRIP: '.$tripid);
            });

            return 'uploaded';

        }

    }

    private $SMS_SENDER = 'Kaya';
    private $RESPONSE_TYPE = 'json';
    private $SMS_USERNAME = 'odejobi.olushola@kayaafrica.co';
    private $SMS_PASSWORD = 'Likemike009@@';

    public function approveBalanceRequest(Request $request) {
        $balanceId = $request->approveBalance;
        foreach($balanceId as $id) {
            $recid = tripPayment::findOrFail($id);
            $recid->balance_paid = TRUE;
            $balance = $recid->balance;
            $getTrip = trip::findOrFail($recid->trip_id);
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
            
            $recid->save();

            $payment = PaymentNotification::firstOrNew(['trip_id' => $recid->trip_id, 'payment_for' => 'Balance']);
            $payment->amount = $balance;
            $payment->uploaded_at = DATE('Y-m-d H:i:s');
            $payment->uploaded_by = Auth::user()->id;
            $payment->save();

            $transporterPhoneNo = $transporterInfo->phone_no;
            $messageContent = 'Hi '.$firstName.', Balance of NGN'.number_format($balance).' for '.$truckInfo->truck_no.'; '.
            $truckType->tonnage/1000 .'T, '.$getTrip->exact_location_id.' has been processed.';

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

    function transactQueryBalance($paymentRequestId) {
        $answer = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.balance, a.standard_balance_rate,  b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, e.transporter_destination, f.transporter_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporter_rates e JOIN 
                tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id AND b.client_id = c.id AND b.destination_state_id = d.regional_state_id AND b.exact_location_id = e.transporter_destination AND b.transporter_id = f.id AND b.truck_id = g.id AND g.truck_type_id = h.id AND b.product_id = i.id WHERE advance_paid = true AND a.id = '.$paymentRequestId.' ORDER BY b.trip_id ASC '
            )
        );
        return $answer;
    }

    public function updateOutstandingBalance(Request $request) {
        $recid = tripPayment::findOrFail($request->outstandingBalanceId);
        if($request->outstandBalanceChecker == 2){
            $previousBalance = $recid->balance;
            $recid->balance += $request->outstandingPartPayment; 
            $recid->outstanding_balance -= $request->outstandingPartPayment;
            $recid->remark = 'outstanding of '.$request->newOutstanding;
            $recid->save();
        }
        if($request->outstandBalanceChecker == 1) {
            $recid->balance += $request->outstandingBalanceUpdate;
            $recid->outstanding_balance = NULL;
            $recid->remark = NULL;
            $recid->save();
        }
        //create a payment history log here.
        $payment = PaymentHistory::CREATE(['trip_id' => $recid->trip_id]);
        if($request->outstandBalanceChecker == 1){
            $payment->amount = $request->outstandingBalanceUpdate;
        }
        else{
            $payment->amount = $request->outstandingPartPayment;
        }
        $payment->payment_mode = 'Outstanding Balance';
        $payment->save();

        return 'saved';
    }

    public function bulkPayment(Request $request) {
        foreach($request->approveAdvance as $key=> $requestedPaymentId) {
            [$trips[]] = DB::SELECT(
                DB::RAW(
                    'SELECT a.*, b.trip_id, b.exact_location_id, b.client_rate, b.transporter_rate, c.loading_site, d.truck_no FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d ON a.trip_id = b.id AND b.loading_site_id = c.id AND b.truck_id = d.id WHERE a.id = "'.$requestedPaymentId.'" '
                )
            );
        }
        return view('finance.bulk-full-payment', compact('trips'));
    }

    public function updateBulkFullPayment(Request $request) {
        $trip_payments_lists = $request->tripPaymentIds;
        foreach($trip_payments_lists as $key=> $paymentId) {
            // $getInitialPayment = tripPayment::SELECT('trip_id')->WHERE('id', $paymentId)->FIRST();
            $getInitialPayment = tripPayment::findOrFail($paymentId);

            $trip = trip::FIND($getInitialPayment->trip_id);
            $trip->client_rate = $request->clientRate[$key];
            $trip->transporter_rate = $request->transporterRate[$key];
            $trip->advance_paid = TRUE;

            $getInitialPayment->amount = $request->transporterRate[$key];
            $getInitialPayment->standard_advance_rate = 0.7 * $request->transporterRate[$key];
            $getInitialPayment->standard_balance_rate = 0.3 * $request->transporterRate[$key];
            $getInitialPayment->exception = 3;
            $getInitialPayment->advance_paid = TRUE;
            $getInitialPayment->balance_paid = TRUE;
            $getInitialPayment->advance = $request->transporterRate[$key];
            $getInitialPayment->balance = 0;
            $getInitialPayment->remark = $request->remark[$key];

            $trip->save();
            $getInitialPayment->save();

        }
       
        return 'updated';
    }

    public function paymentTopUp() {
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.trip_id, a.client_rate, c.truck_no, b.id, b.amount, b.advance, b.balance, b.exception, d.transporter_name FROM tbl_kaya_trips a JOIN tbl_kaya_trip_payments b JOIN tbl_kaya_trucks c JOIN tbl_kaya_transporters d ON a.id = b.trip_id AND a.truck_id = c.id AND a.transporter_id = d.id WHERE a.tracker BETWEEN 4 AND 8 AND b.advance_paid = TRUE AND b.balance_paid = FALSE AND b.exception <> 3 ORDER BY a.trip_id DESC'
            )
        );
        return view('finance.payment-top-up', compact('trips'));
    }

    public function advanceTopUp(Request $request, $id) {
        $specificTripPayment = tripPayment::findOrFail($id);
        $newAdvance = $specificTripPayment->advance + $request->advance;
        $newBalance = $specificTripPayment->amount - $newAdvance;
        $specificTripPayment->advance = $newAdvance;
        $specificTripPayment->balance = $newBalance;
        $specificTripPayment->save();
        return 'updated';
    }

    public function updateMultipleZeroAdvance(Request $request) {
        $trip_payments_lists = $request->approveAdvance;
        foreach($trip_payments_lists as $key=> $paymentId) {
            $getInitialPayment = tripPayment::findOrFail($paymentId);
            $trip = trip::FIND($getInitialPayment->trip_id);
            $trip->advance_paid = TRUE;
            $tripAmount = $trip->transporter_rate;
            $getInitialPayment->advance_paid = TRUE;
            $getInitialPayment->advance = 0;
            $getInitialPayment->balance = $tripAmount;
            $trip->save();
            $getInitialPayment->save();

        }
       
        return 'updated';
    }

    public function declineAdvanceRequest(Request $request) {
        $recid = trip::WHERE('trip_id', $request->id)->GET()->LAST();
        $recid->advance_request = FALSE;
        $recid->advance_requested_by = NULL;
        $recid->advance_requested_at = NULL;
        $recid->save();
        return 'declined';

    }

    public function declineBalanceRequest(Request $request) {
        $recid = trip::WHERE('trip_id', $request->id)->GET()->LAST();
        $recid->balance_requested_by = NULL;
        $recid->balance_requested_at = NULL;
        $recid->balance_request = FALSE;
        $recid->save();
        return 'declined';
    }
}
