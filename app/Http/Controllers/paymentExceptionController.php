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

class paymentExceptionController extends Controller
{
    public function advanceException(Request $request) {
        //return $request->all();
        
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
            $recid->balance = true;
            $recid->exception = $advanceExceptionType;
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
        return 'updated';
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

    public function approveBalanceRequest(Request $request) {

        $balanceId = $request->approveBalance;
        foreach($balanceId as $id) {
            $recid = tripPayment::findOrFail($id);
            $recid->balance_paid = TRUE;
            $balance = $recid->balance;
            $newChunkBalance = 0;
            $transporterChunkPayment = bulkPayment::WHERE('transporter_id', $recid->transporter_id)->GET();
            if(sizeof($transporterChunkPayment)>0) {
                $availableBalance = $transporterChunkPayment[0]->balance;
                if($availableBalance >= $balance){
                    $newChunkBalance = $availableBalance - $balance;
                }
                $updateAccountBalance = bulkPayment::firstOrNew(['transporter_id' => $recid->transporter_id]);
                $updateAccountBalance->balance = $newChunkBalance;
                $updateAccountBalance->save();
            }
            $recid->save();
        }
        return 'approved';
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
}