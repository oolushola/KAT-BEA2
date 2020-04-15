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
                'SELECT a.id, a.advance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, f.transporter_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product, j.first_name, j.last_name FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i JOIN users j ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id AND j.id = b.advance_requested_by WHERE a.advance_paid = false ORDER BY b.trip_id ASC'
            )
        );
        $allpendingbalanceRequests = DB::SELECT(
            DB::RAW(
                'SELECT a.id, b.id AS tripid, a.advance, a.balance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, f.transporter_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product, j.first_name, j.last_name FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i JOIN users j ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id WHERE b.balance_requested_by = j.id AND b.advance_paid = TRUE and b.balance_request = TRUE AND a.balance_paid = FALSE ORDER BY b.trip_id ASC'
            )
        );
        $allPendingOutstandingBalance = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.balance, a.outstanding_balance, a.amount, a.advance_paid, a.balance_paid, a.remark, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, f.transporter_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id WHERE a.advance_paid = TRUE and a.balance_paid = TRUE and a.outstanding_balance > 0 ORDER BY b.trip_id ASC'
            )
        );
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
                'offloadedWaybill'
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
        return 'here';
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

    public function approveAdvancePayment(Request $request) {
        $advanceId = $request->approveAdvance;
        foreach($advanceId as $id) {
            $recid = tripPayment::findOrFail($id);
            $recid->advance_paid = TRUE;
            $advance = $recid->advance;
            $newChunkBalance = 0;
            $transporterChunkPayment = bulkPayment::WHERE('transporter_id', $recid->transporter_id)->GET();
            if(sizeof($transporterChunkPayment)>0) {
                $availableBalance = $transporterChunkPayment[0]->balance;
                if($availableBalance >= $advance){
                    $newChunkBalance = $availableBalance - $advance;
                }
                $updateAccountBalance = bulkPayment::firstOrNew(['transporter_id' => $recid->transporter_id]);
                $updateAccountBalance->balance = $newChunkBalance;
                $updateAccountBalance->save();
            }
            $recid->save();
            $getTrip = trip::findOrFail($recid->trip_id);
            $getTrip->advance_paid = TRUE;
            $getTrip->save();

            //create a payment history log here.
            $payment = PaymentHistory::CREATE(['trip_id' => $recid->trip_id]);
            $payment->amount = $advance;
            $payment->payment_mode = 'Advance';
            $payment->save();
        }

        return 'approved';
    }

   
    function transactQuery($paymentRequestId) {
        $answer = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.advance, a.standard_advance_rate, b.trip_id, b.transporter_id, b.truck_id, b.product_id, b.destination_state_id, b.exact_location_id, b.customers_name, c.company_name, d.state, e.transporter_destination, f.transporter_name, g.truck_no, g.truck_type_id, h.truck_type, h.tonnage, i.product FROM tbl_kaya_trip_payments a JOIN tbl_kaya_trips b JOIN tbl_kaya_clients c JOIN tbl_regional_state d JOIN tbl_kaya_transporter_rates e JOIN 
                tbl_kaya_transporters f JOIN tbl_kaya_trucks g JOIN tbl_kaya_truck_types h JOIN tbl_kaya_products i ON a.trip_id = b.id and b.client_id = c.id AND b.destination_state_id = d.regional_state_id AND b.exact_location_id = e.transporter_destination and b.transporter_id = f.id and b.truck_id = g.id and g.truck_type_id = h.id and b.product_id = i.id WHERE advance_paid = false AND a.id = '.$paymentRequestId.' ORDER BY b.trip_id ASC '
            )
        );
        return $answer;
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
