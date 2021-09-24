<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\KayaPayPaymentBreakdown;
use App\ClientArrangement;
use App\client;

class KayaPayPaymentBreakdownController extends Controller
{
    public function allPaymentBreakdown() {
        $paymentBreakdownListings = DB::SELECT(
            DB::RAW(
                'SELECT a.*, client_alias as client FROM tbl_kaya_pay_payment_breakdowns a JOIN tbl_kaya_clients b ON a.client_id = b.id ORDER BY created_at ASC'
            )
        );
        return view(
            'kaya-pay/all-payment-breakdown',
            compact(
                'paymentBreakdownListings'
            )
        );
    }


    public function index() {
        $clients = DB::SELECT(
            DB::RAW(
                'SELECT b.* FROM tbl_kaya_pay_client_arrangements a JOIN tbl_kaya_clients b ON a.client_id = b.id WHERE client_status = "1" ORDER BY company_name ASC '
            )
        );
        $states = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_regional_state WHERE regional_country_id = "94" ORDER BY state ASC '
            )
        );
        $paymentBreakdownListings = DB::SELECT(
            DB::RAW(
                'SELECT a.*, client_alias as client FROM tbl_kaya_pay_payment_breakdowns a JOIN tbl_kaya_clients b ON a.client_id = b.id ORDER BY created_at ASC'
            )
        );
        return view('kaya-pay/payment-breakdown', 
            compact(
                'clients',
                'states',
                'paymentBreakdownListings'
            )
        );
    }

    public function store(Request $request) {
        $validatePaymentBreakdown = $request->validate([
            'client_id' => 'required | integer',
            'loading_site' => 'required | string',
            'gated_in' => 'required',
            'truck_no' => 'required | string',
            'destination_state' => 'required | string',
            'destination_city' => 'required | string',
            'at_loading_bay' => 'required',
            'payment_disbursed' => 'required',
            'waybill_no' => 'required | string',
            'loaded_weight' => 'required | string',
            'finance_cost' => 'required'
        ]);

        $getLastTripId = KayaPayPaymentBreakdown::SELECT('kaya_pay_id')->LATEST()->FIRST();
        if($getLastTripId) {
            $lastTripId = str_replace('KPID', '', $getLastTripId->kaya_pay_id);
            $counter = intval('0000') + intval($lastTripId) + 1;
            $kayaPayId = sprintf('%04d', $counter);
            $kaya_pay_id = 'KPID'.$kayaPayId;
        }
        else{
            $kaya_pay_id = 'KPID0001';
        }
        $getClientInfo = ClientArrangement::WHERE('client_id', $request->client_id)->GET()->FIRST();
        $financeCost = $request->finance_cost;
        $financeIncome = $financeCost + ($financeCost * ($getClientInfo->interest_rate / 100));
        $netIncome = $financeIncome - $financeCost;

        if($request->gated_out) {
            $gatedOut = $request->gated_out;
        }
        else{
            $gatedOut = $request->at_loading_bay;
        }
        $paymentDisbursedDate = strtotime($request->payment_disbursed);
        $valid_until = date('Y-m-d', strtotime("+".$getClientInfo->payback_in." day", $paymentDisbursedDate));
        
        $checkTrip = KayaPayPaymentBreakdown::WHERE('waybill_no', $request->waybill_no)->exists();
        if($checkTrip) {
            return 'exists';
        }
        $createPaymentBreakdown = KayaPayPaymentBreakdown::CREATE([
            'kaya_pay_id' => $kaya_pay_id,
            'client_id' => $request->client_id,
            'loading_site' => $request->loading_site,
            'gated_in' => $request->gated_in,
            'truck_no' => str_replace(" ", "", $request->truck_no),
            'driver_name' => $request->driver_name,
            'driver_phone_no' => $request->driver_phone_no,
            'motor_boy_name' => $request->motor_boy_name,
            'motor_boy_phone_no' => $request->motor_boy_phone_no,
            'transporter_name' => $request->transporter_name,
            'transporter_phone_no' => $request->transporter_phone_no,
            'destination_state' => $request->destination_state,
            'destination_city' => $request->destination_city,
            'at_loading_bay' => $request->at_loading_bay,
            'gated_out' => $gatedOut,
            'payment_disbursed' => $request->payment_disbursed,
            'valid_until' => $valid_until,
            'customer_name' => $request->customer_name,
            'customer_phone_no' => $request->customer_phone_no,
            'waybill_no' => $request->waybill_no,
            'loaded_weight' => $request->loaded_weight,
            'finance_cost' => $request->finance_cost,
            'finance_income' => $financeIncome,
            'net_income' => $netIncome,
            'percentage_rate' => $getClientInfo->interest_rate,
            'overdue_charge' => $getClientInfo->overdue_charge
        ]);

        return 'saved';
    }

    public function edit(Request $request, $id) {
        $clients = DB::SELECT(
            DB::RAW(
                'SELECT b.* FROM tbl_kaya_pay_client_arrangements a JOIN tbl_kaya_clients b ON a.client_id = b.id WHERE client_status = "1" ORDER BY company_name ASC '
            )
        );
        $states = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_regional_state WHERE regional_country_id = "94" ORDER BY state ASC '
            )
        );
        $paymentBreakdownListings = DB::SELECT(
            DB::RAW(
                'SELECT a.*, client_alias as client FROM tbl_kaya_pay_payment_breakdowns a JOIN tbl_kaya_clients b ON a.client_id = b.id ORDER BY created_at ASC'
            )
        );
        $recid = KayaPayPaymentBreakdown::findOrFail($id);
        return view('kaya-pay/payment-breakdown', 
            compact(
                'clients',
                'states',
                'paymentBreakdownListings',
                'recid'
            )
        );
    }

    public function update(Request $request, $id) {
        $validatePaymentBreakdown = $request->validate([
            'client_id' => 'required | integer',
            'loading_site' => 'required | string',
            'gated_in' => 'required',
            'truck_no' => 'required | string',
            'destination_state' => 'required | string',
            'destination_city' => 'required | string',
            'at_loading_bay' => 'required',
            'payment_disbursed' => 'required',
            'waybill_no' => 'required | string',
            'loaded_weight' => 'required | string',
            'finance_cost' => 'required'
        ]);

        $getClientInfo = ClientArrangement::WHERE('client_id', $request->client_id)->GET()->FIRST();
        $financeCost = $request->finance_cost;
        $financeIncome = $financeCost + ($financeCost * ($getClientInfo->interest_rate / 100));
        $netIncome = $financeIncome - $financeCost;

        if($request->gated_out) {
            $gatedOut = $request->gated_out;
        }
        else{
            $gatedOut = $request->at_loading_bay;
        }
        $gateOutDate = strtotime($gatedOut);
        $valid_until = date('Y-m-d', strtotime("+".$getClientInfo->payback_in." day", $gateOutDate));
        
        $checkTrip = KayaPayPaymentBreakdown::WHERE('waybill_no', $request->waybill_no)->WHERE('id', '!=', $id)->exists();
        if($checkTrip) {
            return 'exists';
        }
        $recid = KayaPayPaymentBreakdown::findOrFail($id);
        $recid->client_id = $request->client_id;
        $recid->loading_site = $request->loading_site;
        $recid->gated_in = $request->gated_in;
        $recid->truck_no = str_replace(" ", "", $request->truck_no);
        $recid->driver_name = $request->driver_name;
        $recid->driver_phone_no = $request->driver_phone_no;
        $recid->motor_boy_name = $request->motor_boy_name;
        $recid->motor_boy_phone_no = $request->motor_boy_phone_no;
        $recid->transporter_name = $request->transporter_name;
        $recid->transporter_phone_no = $request->transporter_phone_no;
        $recid->destination_state = $request->destination_state;
        $recid->destination_city = $request->destination_city;
        $recid->at_loading_bay = $request->at_loading_bay;
        $recid->gated_out = $gatedOut;
        $recid->payment_disbursed = $request->payment_disbursed;
        $recid->valid_until = $valid_until;
        $recid->customer_name = $request->customer_name;
        $recid->customer_phone_no = $request->customer_phone_no;
        $recid->waybill_no = $request->waybill_no;
        $recid->loaded_weight = $request->loaded_weight;
        $recid->finance_cost = $request->finance_cost;
        $recid->finance_income = $financeIncome;
        $recid->net_income = $netIncome;
        $recid->percentage_rate = $getClientInfo->interest_rate;
        $recid->overdue_charge = $getClientInfo->overdue_charge;
        
        $recid->save();

        return 'updated';
    }

    public function uploadBulkPaymentBreakdown(Request $request) {
        $upload = $request->file('uploadBulkPayment');
        $filePath = $upload->getRealPath();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $escapedHeader = [];
        foreach($header as $key => $value) {
            $lowercaseheader  = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '_', $lowercaseheader);
            array_push($escapedHeader, $escapedItem);
        }
        $k = 0;
        while($columns = fgetcsv($file))
        {
            $k++;
            if($columns[0] == "") {
                continue;
            }
            $data = array_combine($escapedHeader, $columns);
            foreach($data as $key =>  $value) {
            [$value] = ($key=="finance_cost" || $key=="finance_income" || $key=="net_income")?(float)$value:(integer)$value;
            }
            if(isset($data['___client_id'])) {
                $client = $data['___client_id'];
            }
            else {
                $client = $data['client_id'];
            }
        
            $clientId = $client;
            $gatedIn = $data['gated_in'];
            $loadingSite = $data['loading_site'];
            $truckNo = $data['truck_no'];
            $driverName = $data['driver_name'];
            $driverPhoneNo = $data['driver_phone_no'];
            $motorBoyName = $data['motorboy_name'];
            $motorBoyPhoneNo = $data['motor_boy_phone_no'];
            $transporterName = $data['transporter_name'];
            $transporterPhoneNo = $data['transporter_phone_no'];
            $destinationState = $data['destination_state'];
            $destinationCity = $data['destination_city'];
            $atLoadingBay = $data['at_loading_bay'];
            $gatedOut = $data['gated_out'];
            $paymentDisbursed = $data['payment_disbursed'];            
            $customerName = $data['customer_name'];
            $customerPhoneNo = $data['customer_phone_no'];
            $waybillNo = $data['waybill_no'];
            $loadedWeight = $data['loaded_weight'];
            $customerAddress = $data['customer_address'];
            $financeCost = str_replace(',', '', $data['finance_cost']);  

            $getLastTripId = KayaPayPaymentBreakdown::SELECT('kaya_pay_id')->LATEST()->FIRST();
            if($getLastTripId) {
                $lastTripId = str_replace('KPID', '', $getLastTripId->kaya_pay_id);
                $counter = intval('0000') + $lastTripId + 1;
                $kayaPayId = sprintf('%04d', $counter);
                $kaya_pay_id = 'KPID'.$kayaPayId;
            }
            else{
                $counter = intval('0000') + 1;
                $kayaPayId = sprintf('%04d', $counter);
                $kaya_pay_id = 'KPID'.$kayaPayId;
            }
            $getClientInfo = ClientArrangement::WHERE('client_id', $clientId)->GET()->FIRST();
            $financeIncome = $financeCost + ($financeCost * ($getClientInfo->interest_rate / 100));
            $netIncome = $financeIncome - $financeCost;

            if($gatedOut) {
                $gatedOut = $gatedOut;
            }
            else{
                $gatedOut = $atLoadingBay;
            }
            $paymentDisbursedDate = strtotime($paymentDisbursed);
            $valid_until = date('Y-m-d', strtotime("+".$getClientInfo->payback_in." day", $paymentDisbursedDate));
                

            $checkTripExistence = KayaPayPaymentBreakdown::WHERE('client_id', $clientId)->WHERE('waybill_no', $waybillNo)->exists();
            if(!$checkTripExistence) {
                $trip = KayaPayPaymentBreakdown::firstOrNew(['client_id' => $clientId, 'waybill_no' => $waybillNo]);
                $trip->kaya_pay_id = $kaya_pay_id;
                $trip->client_id = $clientId;
                $trip->loading_site = $loadingSite;
                $trip->gated_in = $gatedIn;
                $trip->truck_no = str_replace(" ", "", $truckNo);
                $trip->driver_name = $driverName;
                $trip->driver_phone_no = $driverPhoneNo;
                $trip->motor_boy_name = $motorBoyName;
                $trip->motor_boy_phone_no = $motorBoyPhoneNo;
                $trip->transporter_name = $transporterName;
                $trip->transporter_phone_no = $transporterPhoneNo;
                $trip->destination_state = $destinationState;
                $trip->destination_city = $destinationCity;
                $trip->at_loading_bay = $atLoadingBay;
                $trip->gated_out = $gatedOut;
                $trip->payment_disbursed = $paymentDisbursed;
                $trip->valid_until = $valid_until;
                $trip->customer_name = $customerName;
                $trip->customer_phone_no = $customerPhoneNo;
                $trip->waybill_no = $waybillNo;
                $trip->loaded_weight = $loadedWeight;
                $trip->finance_cost = $financeCost;
                $trip->finance_income = $financeIncome;
                $trip->net_income = $netIncome;
                $trip->percentage_rate = $getClientInfo->interest_rate;
                $trip->overdue_charge = $getClientInfo->overdue_charge;
                
                $trip->save();
            }
        }
       return 'populated';

    }
}
