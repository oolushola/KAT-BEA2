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
use App\clientProduct;
use App\completeInvoice;
use Mail;
use App\truckAvailability;
use App\tripChanges;
use App\IssueType;
use Auth;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ordersController extends Controller
{
    public function getinitialrequirement() {
        $transporters = transporter::SELECT('id', 'transporter_name')->WHERE('transporter_status', TRUE)->ORDERBY('transporter_name', 'ASC')->GET();
        $driversBank = drivers::SELECT('driver_first_name', 'driver_last_name', 'licence_no')->ORDERBY('driver_first_name', 'ASC')->GET();
        $truckTypes = truckType::SELECT('id', 'truck_type', 'tonnage')->ORDERBY('truck_type', 'ASC')->GET();
        $truckBank = trucks::ORDERBY('truck_no')->GET();
        
        return view('orders.trip-initial-requirement',
            compact(
                'transporters',
                'driversBank',
                'truckTypes',
                'truckBank'
            )
        );
    }

    public function index() {
        $loadingsites = loadingSite::ORDERBY('loading_site', 'ASC')->GET();
        $transporters = transporter::WHERE('transporter_status', TRUE)->ORDERBY('transporter_name', 'ASC')->GET();
        $trucks = trucks::ORDERBY('truck_no', 'ASC')->GET();
        $drivers = drivers::ORDERBY('driver_first_name', 'ASC')->GET();
        $products = product::ORDERBY('product', 'ASC')->GET();
        $clients = client::ORDERBY('company_name')->GET();
        $states = DB::SELECT(DB::RAW(
                'SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $truckTypes = truckType::SELECT('truck_type')->ORDERBY('truck_type', 'ASC')->DISTINCT()->GET();
        return view('orders.create-trip',
            compact(
                'loadingsites',
                'transporters',
                'trucks',
                'drivers',
                'products',
                'states',
                'clients',
                'truckTypes'
            )
        );
    }

    public function clientLoadingSite(Request $request) {
        $answer = '<label class="font-weight-semibold">Loading Site</label>
                    <select class="form-control" name="loading_site_id" id="loadingSite">
                        <option value="">View all loading site</option>';
        
        $loadingsites = DB::SELECT(
            DB::RAW(
                'select b.* from tbl_kaya_client_loading_sites a join tbl_kaya_loading_sites b on a.loading_site_id = b.id where client_id = '.$request->client_id.''
            )
        );
        foreach($loadingsites as $loadingsite){
            $answer.='<option value="'.$loadingsite->id.'">'.$loadingsite->loading_site.'</option>';
        }
        
        $answer.='</select>';

        $clientproducts = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_kaya_products WHERE id IN (SELECT product_id FROM tbl_kaya_client_products WHERE client_id = "'.$request->client_id.'")'
            )
        );

        $response = '<label class="font-weight-semibold">Product</label>
                        <select class="form-control" name="product_id" id="productId">
                            <option value="">View all products</option>';
            foreach($clientproducts as $product) {
                $response.='<option value="'.$product->id.'">'.$product->product.'</option>';
            }
        $response.='</select>';

        return $answer.'`'.$response;
    }

    public function getTransporterNumber(Request $request) {
        $phoneNumber = transporter::SELECT('phone_no')->WHERE('id', $request->transporter_id)->GET();
        return $phoneNumber;
    }

    public function getTruckInformation(request $request) {
        $trucktypeid = trucks::SELECT('truck_type_id')->WHERE('id', $request->truck_id)->GET();
        $truck_type_id = $trucktypeid[0]['truck_type_id'];
        $truckInfo = truckType::WHERE('id', $truck_type_id)->GET();
        return $truckInfo;
    }

    public function getDriversInformation(request $request) {
        $driverInfo = drivers::WHERE('id', $request->driver_id)->GET();
        return $driverInfo;
    }

    public function getExactDestination(request $request) {
        $answer = '<label class="font-weight-semibold">Destination</label>
                    <select class="form-control" name="exact_location_id" id="exactLocation">
                        <option value="">Exact destination</option>';
        
        $exactdestions = transporterRate::SELECT('transporter_destination')->WHERE('transporter_to_state_id', $request->state_id)->distinct()->GET();
        foreach($exactdestions as $destination){
            $answer.='<option value="'.$destination->transporter_destination.'">'.$destination->transporter_destination.'</option>';
        }
        
        $answer.='</select>';

        return $answer;
    }

    public function createTripByAvailability($truck_no, $availabilityId) {
        $loadingsites = loadingSite::ORDERBY('loading_site', 'ASC')->GET();
        $transporters = transporter::WHERE('transporter_status', TRUE)->ORDERBY('transporter_name', 'ASC')->GET();
        $trucks = trucks::ORDERBY('truck_no', 'ASC')->GET();
        $drivers = drivers::ORDERBY('driver_first_name', 'ASC')->GET();
        $products = product::ORDERBY('product', 'ASC')->GET();
        $clients = client::ORDERBY('company_name')->GET();
        $states = DB::SELECT(DB::RAW(
                'SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $truckAvailabilityId = base64_decode($availabilityId);
        $exactdestinations = transporterRate::SELECT('transporter_destination', 'transporter_to_state_id')->ORDERBY('transporter_destination', 'ASC')->DISTINCT()->GET();
        $truckTypes = truckType::SELECT('truck_type')->ORDERBY('truck_type', 'ASC')->DISTINCT()->GET();

        $recid = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.loading_site, d.truck_no, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no, g.product, h.state, i.first_name, i.last_name, j.tonnage, j.truck_type FROM tbl_kaya_truck_availabilities a JOIN tbl_kaya_clients b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f JOIN tbl_kaya_products g JOIN tbl_regional_state h JOIN users i JOIN tbl_kaya_truck_types j ON a.client_id = b.id AND a.loading_site_id = c.id AND a.truck_id = d.id AND a.transporter_id = e.id and a.driver_id = f.id and a.product_id = g.id and a.destination_state_id = h.regional_state_id and a.reported_by = i.id AND d.truck_type_id = j.id  WHERE a.status = FALSE AND a.id = "'.$truckAvailabilityId.'"'
            )
        );

        return view('orders.create-trip-availability',
            compact(
                'loadingsites',
                'transporters',
                'trucks',
                'drivers',
                'products',
                'states',
                'clients',
                'recid',
                'truck_no',
                'exactdestinations',
                'truckAvailabilityId',
                'truckTypes'
            )
        );
    }

    public function storeMovedInTruck(Request $request) {
        $validatedata = $request->validate([
            'gate_in' => 'required | string',
            'loading_site_id' => 'required | integer',
            'transporter_id' => 'required | integer',
            'truck_id' => 'required | integer',
            'driver_id' => 'required | integer',
            'product_id' => 'required | integer',
            'destination_state_id' => 'required | integer',
            'exact_location_id' => 'required | string',
        ]);


        $truckNumberChecker = $request->truckNumberChecker;
        if($truckNumberChecker != 1){
            $getTruckTypeId = truckType::SELECT('id')->WHERE('tonnage', $request->tonnage)->WHERE('truck_type', $request->truck_type)->GET();
            if(count($getTruckTypeId))
            {
                $truck_type_id = $getTruckTypeId[0]->id;
                $transporter_id = $request->transporter_id;
                $addNewTruck = trucks::firstOrNew(['transporter_id' => $transporter_id, 'truck_type_id' => $truck_type_id, 'truck_no' => $request->truck_no]);
                $addNewTruck->save();
                $request->truck_id = $addNewTruck->id;

            } else {
                return 'invalidTruckType';
            }
        }

        $driverChecker = $request->driverChecker;
        if($driverChecker != 1){
            $checkDriversPhoneNumber = drivers::WHERE('driver_phone_number', $request->drivers_phone_no)->exists();
            if($checkDriversPhoneNumber)
            {
                return 'driversNumberExists';
            }
            else{
                $driversName = explode(' ', $request->drivers_name);
                $drivers_first_name = $driversName[0];
                if(isset($driversName[1]))
                {
                    $drivers_last_name = $driversName[1];
                } else {
                    $drivers_last_name = '';
                }
                $addNewDriver = drivers::firstOrNew(['driver_first_name' => $drivers_first_name, 'driver_last_name' => $drivers_last_name]);
                $addNewDriver->driver_phone_number = $request->drivers_phone_no;
                $addNewDriver->motor_boy_first_name = $request->motor_boy_name;
                $addNewDriver->motor_boy_phone_no = $request->motor_boy_number;
                $addNewDriver->save();
                $request->driver_id = $addNewDriver->id;
            }
        }

        $check = trip::WHERE('gate_in', $request->gate_in)->WHERE('loading_site_id', $request->loading_site_id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            
            $getLastTripId = trip::SELECT('trip_id')->LATEST()->FIRST();
            $lastTripId = str_replace('KAID', '', $getLastTripId->trip_id);
            $counter = intval('0000') + intval($lastTripId) + 1;
            $kaya_id = 'KAID'.sprintf('%04d', $counter);

            $trip = trip::CREATE($request->all());

            $id = $trip->id;
            $recid = $trip::findOrFail($id);
            $recid->trip_id = $kaya_id;

            $transporterRecord = transporter::findOrFail($recid->transporter_id);
            $userIdentity = $transporterRecord->assign_user_id;
            $recid->account_officer_id = $userIdentity;

            $recid->save();

            $updateTruckAvailabilityStatus = truckAvailability::findOrFail($request->truck_availability_id);
            $updateTruckAvailabilityStatus->status = TRUE;
            $updateTruckAvailabilityStatus->save();
            return 'saved';
        }

    }

    public function store(Request $request) {
        $validatedata = $request->validate([
            'gate_in' => 'required | string',
            'loading_site_id' => 'required | integer',
            'destination_state_id' => 'required | integer',
            'exact_location_id' => 'required | string',
        ]);

        $truckNumberChecker = $request->truckNumberChecker;
        if($truckNumberChecker != 1){
            $getTruckTypeId = truckType::SELECT('id')->WHERE('tonnage', $request->tonnage)->WHERE('truck_type', $request->truck_type)->GET();
            if(count($getTruckTypeId))
            {
                $truck_type_id = $getTruckTypeId[0]->id;
                $transporter_id = $request->transporter_id;
                $addNewTruck = trucks::firstOrNew(['transporter_id' => $transporter_id, 'truck_type_id' => $truck_type_id, 'truck_no' => $request->truck_no]);
                $addNewTruck->save();
                $request->truck_id = $addNewTruck->id;
            } else {
                return 'invalidTruckType';
            }
        }

        $driverChecker = $request->driverChecker;
        if($driverChecker != 1){
            $checkDriversPhoneNumber = drivers::WHERE('driver_phone_number', $request->drivers_phone_no)->exists();
            if($checkDriversPhoneNumber)
            {
                return 'driversNumberExists';
            }
            else{
                $driversName = explode(' ', $request->drivers_name);
                $drivers_first_name = $driversName[0];
                if(isset($driversName[1]))
                {
                    $drivers_last_name = $driversName[1];
                } else {
                    $drivers_last_name = '';
                }
                $addNewDriver = drivers::firstOrNew(['driver_first_name' => $drivers_first_name, 'driver_last_name' => $drivers_last_name]);
                $addNewDriver->driver_phone_number = $request->drivers_phone_no;
                $addNewDriver->motor_boy_first_name = $request->motor_boy_name;
                $addNewDriver->motor_boy_phone_no = $request->motor_boy_number;
                $addNewDriver->save();
                $request->driver_id = $addNewDriver->id;
            }
        }
        $check = trip::WHERE('truck_id', $request->truck_id)->WHERE('tracker', '<', 8)->WHERE('trip_status', '!=', 0)->exists();
        if($check) {
            return 'exists';
        }
        else {

            $getLastTripId = trip::SELECT('trip_id')->LATEST()->FIRST();
            $lastTripId = str_replace('KAID', '', $getLastTripId->trip_id);
            $counter = intval('0000') + intval($lastTripId) + 1;
            $kaya_id = sprintf('%04d', $counter);

            $transporterRecord = transporter::findOrFail($request->transporter_id);
            $userIdentity = $transporterRecord->assign_user_id;
            $tripId = 'KAID'.$kaya_id;
            $addNewTrip = trip::CREATE([
                'gate_in' => $request->gate_in, 
                'client_id' => $request->client_id, 
                'loading_site_id' => $request->loading_site_id,
                'truck_id' => $request->truck_id,
                'transporter_id' => $request->transporter_id,
                'driver_id' => $request->driver_id,
                'product_id' => $request->product_id,
                'destination_state_id' => $request->destination_state_id,
                'exact_location_id' => $request->exact_location_id,
                'user_id' => $request->user_id,
                'trip_id' => $tripId,
                'account_officer' => $request->account_officer,
                'tracker' => $request->tracker,
                'trip_status' => TRUE
            ]);
            $addNewTrip->trip_id = 'KAID'.$kaya_id;
            $addNewTrip->account_officer_id = $userIdentity;
            $addNewTrip->save();

            $changes = tripChanges::CREATE(['trip_id' => $addNewTrip->id, 'user_id' => $request->user_id, 'changed_keys' => 1, 'changed_values' => 'Created']);

            return 'saved';

        }       
    }

    public function edit($id) {
        $loadingsites = loadingSite::ORDERBY('loading_site', 'ASC')->GET();
        $transporters = transporter::WHERE('transporter_status', TRUE)->ORDERBY('transporter_name', 'ASC')->GET();
        $trucks = trucks::ORDERBY('truck_no', 'ASC')->GET();
        $drivers = drivers::ORDERBY('driver_first_name', 'ASC')->GET();
        $products = product::ORDERBY('product', 'ASC')->GET();
        $clients = client::ORDERBY('company_name')->GET();
        $states = DB::SELECT(DB::RAW(
                'SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $recid = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.loading_site, d.truck_no, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no, g.product, h.state, i.first_name, i.last_name, j.tonnage, j.truck_type FROM tbl_kaya_trips a JOIN tbl_kaya_clients b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f JOIN tbl_kaya_products g JOIN tbl_regional_state h JOIN users i JOIN tbl_kaya_truck_types j ON a.client_id = b.id AND a.loading_site_id = c.id AND a.truck_id = d.id AND a.transporter_id = e.id and a.driver_id = f.id and a.product_id = g.id and a.destination_state_id = h.regional_state_id and a.user_id = i.id AND d.truck_type_id = j.id  WHERE a.id = "'.$id.'"'
            )
        );
        $transporterNumber = transporter::findOrFail($recid[0]->transporter_id);
        $getTruckTypeId = trucks::SELECT('truck_type_id')->WHERE('id', $recid[0]->truck_id)->GET();
        $truckType = truckType::findOrFail($getTruckTypeId)->last();
        $exactdestinations = transporterRate::SELECT('transporter_destination', 'transporter_to_state_id')->ORDERBY('transporter_destination', 'ASC')->DISTINCT()->GET();
        $driver = drivers::findOrFail($recid[0]->driver_id);
        $truckTypes = truckType::SELECT('truck_type')->ORDERBY('truck_type', 'ASC')->DISTINCT()->GET();
        
        $waybillUploadCount = tripWaybill::WHERE('trip_id', $id)->GET()->COUNT();

        return view('orders.create-trip',
            compact('loadingsites',
                'transporters',
                'trucks',
                'drivers',
                'products',
                'states',
                'recid',
                'transporterNumber',
                'truckType',
                'driver',
                'exactdestinations',
                'clients',
                'truckTypes',
                'waybillUploadCount'
            )
        );
    }

    public function update(request $request, $id) {
        if($request->tracker == "") {
            $validatedata = $request->validate([
                'tracker' => 'required | integer',
            ]);
        }
        if($request->tracker == 2) {
            $validatedata = $request->validate([
                'arrival_at_loading_bay' => 'required | string',
            ]);
        }
        
        if($request->tracker == 3) {
            $validatedata = $request->validate([
                'loading_start_time' => 'required | string',
            ]);
        }

        if($request->tracker == 4 && $request->loading_end_time == '') {
            $validatedata = $request->validate([
                'loading_end_time' => 'required | string'
            ]);
        }

        if($request->tracker == 4) {
            $validatedata = $request->validate([
                'departure_date_time' => 'required | string',
            ]);
        }

        if($request->tracker == 5) {
            $validatedata = $request->validate([
                'gated_out' => 'required | string',
                'customers_name' => 'required | string',
                'customer_no' => 'required | string',
                'loaded_quantity' => 'required | integer',
                'loaded_weight' => 'required',
                'customer_address' => 'required | string',
            ]);  
        }

        $check = trip::WHERE('truck_id', $request->truck_id)->WHERE('tracker', '<', 8)->WHERE('trip_status', '!=', 0)->WHERE('id', '<>', $id)->exists();
        if($check) {
            return 'exists';
        }
        else{
            $recid = trip::findOrFail($id);
            if($request->tracker <= 3){
                $previousTripRecord = trip::SELECT('client_rate', 'transporter_rate')->WHERE('client_id', $request->client_id)->WHERE('loading_site_id', $request->loading_site_id)->WHERE('destination_state_id', $request->destination_state_id)->WHERE('exact_location_id', $request->exact_location_id)->WHERE('trip_status', '!=', 0)->WHERE('tracker', '>=', 5)->LATEST()->FIRST();

                if($previousTripRecord) {
                    $recid->client_rate = $previousTripRecord->client_rate;
                    $recid->transporter_rate = $previousTripRecord->transporter_rate;
                }
                else {
                    $recid->client_rate = 0;
                    $recid->transporter_rate = 0;
                }
            }
            $recid->UPDATE($request->all());

            $changes = tripChanges::CREATE(['trip_id' => $id, 'user_id' => $request->user_id, 'changed_keys' => $request->tracker, 'changed_values' => 'Update']);


            // if($request->tracker == 5) {
            //     $tripRate = $recid->transporter_rate;
            //     $standardAdvanceRate = $tripRate * 0.7;
            //     $standardBalanceRate = $tripRate * 0.3;
            //     $available_balance = 0;

            //     $transporterChunkPayment = bulkPayment::WHERE('transporter_id', $recid->transporter_id)->GET();
            //     if(sizeof($transporterChunkPayment)>0) {
            //         $current_balance = $transporterChunkPayment[0]->balance;
            //         if($current_balance >=    $standardAdvanceRate){
            //             $amountPayable = $standardAdvanceRate;
            //             $available_balance = $current_balance - $standardAdvanceRate;
            //         }
            //         else {
            //             $amountPayable = $current_balance - $standardAdvanceRate;
            //         }
            //     }
            //     else{
            //         $current_balance = 0;
            //         $amountPayable = $standardAdvanceRate;
            //     }
            
            //     $client = client::findOrFail($recid->client_id);
            //     $clientName = $client->company_name;
            //     $customerAddress = $recid->customer_address;
            //     $customer_no = $recid->customer_no;
            //     $customer_name = $recid->customers_name;

            //     $driver = drivers::findOrFail($recid->driver_id);
            //     $driverName = ucwords($driver->driver_first_name.' '.$driver->driver_last_name);
            //     $motorBoyName = ucwords($driver->motor_boy_first_name.' '.$driver->motor_boy_last_name);

            //     $truck = trucks::findOrFail($recid->truck_id);
            //     $truckTypeId = $truck->truck_type_id;
            //     $truckType = truckType::findOrFail($truckTypeId);

            //     $exactState = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_state_id = '.$recid->destination_state_id.' '));
            //     $state = $exactState[0]->state;

            //     $getWaybillCredentials = tripWaybill::WHERE('trip_id', $recid->id)->GET();

            //     $transporter = transporter::FindOrFail($recid->transporter_id);

            //     $product = product::findOrFail($recid->product_id);
            //     $tripid = $recid->trip_id;

            //     $payment = tripPayment::firstOrNew(['trip_id' => $recid->id]);
            //     $payment->client_id = $recid->client_id;
            //     $payment->transporter_rate_id = $recid->exact_location_id;
            //     $payment->transporter_id = $recid->transporter_id;
            //     $payment->amount = $tripRate;
            //     $payment->standard_advance_rate = $standardAdvanceRate;
            //     $payment->standard_balance_rate = $standardBalanceRate;
            //     $payment->advance = $standardAdvanceRate;
            //     $payment->balance = $standardBalanceRate;
            //     $payment->save();

            //     Mail::send('initiate-payment', array(
            //         'tripid' => $tripid,
            //         'getWaybillCredentials' => $getWaybillCredentials,
            //         'destination' => $recid->exact_location_id,
            //         'transporter_name' => $transporter->transporter_name,
            //         'tonnage' => $truckType->tonnage,
            //         'truck_no' => $truck->truck_no,
            //         'product_name' => $product->product,
            //         'customer_name' => $recid->customers_name,
            //         'current_balance' => $current_balance,
            //         'standardAdvanceRate' => $standardAdvanceRate,
            //         'amountPayable' => $amountPayable,
            //         'available_balance' => $available_balance,
            //         'bank_name' => $transporter->bank_name,
            //         'account_number' => $transporter->account_number,
            //         'account_name' => $transporter->account_name,
            //     ), function($message) use ($request, $tripid) {
            //         $message->from('no-reply@kayaafrica.co', 'KAYA-FINACE');
            //         $message->to('kayaafricafin@gmail.com', 'Finance')->subject('Payment for TRIP: '.$tripid);
            //     });
            // }

            return 'updated';
        }
    }

    public function show(Request $request) {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        $loadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.first_name, h.last_name FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN users h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND h.id = a.user_id ORDER BY a.trip_id DESC'
            )
        );
        $collection = new Collection($orders);
        $perPage = 100;
        $currentPage =  $request->get('page');
        $pagedData = $collection->slice($currentPage * $perPage, $perPage)->all();

        $path = url('/').'/view-orders?'.$currentPage;
        
        $pagination = new LengthAwarePaginator(($pagedData), count($collection), $perPage );
        $pagination = $pagination->withPath($path);
        

        foreach($pagination as $trip){
            $waybillListings[] = tripWaybill::SELECT('trip_id', 'sales_order_no', 'invoice_no', 'photo')->WHERE('trip_id', $trip->id)->GET();
        }
        if(count($waybillListings)){
            foreach($waybillListings as $waybills) {
               foreach($waybills as $waybillsArray) {
                   $tripWaybills[] = $waybillsArray;
               }
            }
        }
        else{
            $tripWaybills = [];
        }
        
        foreach($pagination as $trip){
            $tripEventListing[] = tripEvent::WHERE('trip_id', $trip->id)->GET();
        }
        
        $tripEvents = [];
        foreach($tripEventListing as $events) {
           foreach($events as $tripEvent) {
               $tripEvents[] = $tripEvent;
           }
        }
        
        
        $waybillstatuses = tripWaybillStatus::GET();
        // $clientRates = DB::SELECT(
        //     DB::RAW(
        //         'SELECT a.client_id, a.amount_rate, b.* FROM `tbl_kaya_client_fare_rates` a LEFT JOIN `tbl_kaya_transporter_rates` b ON a.from_state_id = b.transporter_from_state_id AND a.to_state_id = b.transporter_to_state_id AND a.destination = b.transporter_destination'
        //     )
        // );
        //$trippayments = tripPayment::GET();
        $products = product::SELECT('id', 'product')->ORDERBY('product')->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        //$invoiceCriteria = tripWaybillStatus::GET();

        return view('orders.view-orders',
            compact(
                'orders',
                'tripWaybills',
                'tripEvents',
                'waybillstatuses',
                // 'clientRates',
                // 'trippayments',
                'clients',
                'transporters',
                'loadingSites',
                'products',
                'states',
                // 'invoiceCriteria',
                'pagination'
            )
        );
    }

    public function fieldOpsUpdate() {
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker < \'5\' ORDER BY a.trip_id ASC
                '

            )
        );
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();
        $countAvailableTrucks = truckAvailability::WHERE('status', FALSE)->GET()->COUNT();
        
        return view('orders.field-ops-update',
            compact(
                'orders',
                'tripWaybills',
                'tripEvents',
                'waybillstatuses',
                'countAvailableTrucks'
            )
        );   
    }

    //Copy From here to the production server!

    public function eventTrip($orderId, $clientName) {
        $client_name = str_replace('-', ' ', $clientName);
        $tracker_ = trip::SELECT('id', 'tracker', 'destination_state_id')->WHERE('trip_id', $orderId)->GET();
        $tracker = $tracker_[0]['tracker'];
        $tripId = $tracker_[0]['id'];
        $tripEvents = tripEvent::WHERE('trip_id', $tripId)->ORDERBY('current_date', 'DESC')->GET();
        $onjourneyIssueTypes = IssueType::WHERE('issue_category', 1)->GET();
        $offloadIssueTypes = IssueType::WHERE('issue_category', 2)->GET();
        $lgas = DB::SELECT(
            DB::RAW(
                'SELECT lga_name FROM tbl_regional_local_govt WHERE regional_country_id = \'94\' ORDER BY lga_name ASC '
            )
        );

        $tripDestinationchecker = tripEvent::WHERE('trip_id', $tripId)->GET()->LAST();
    
        return view('orders.trip-events',
            compact(
                'orderId',
                'client_name',
                'tracker',
                'tripId',
                'tripEvents',
                'onjourneyIssueTypes',
                'offloadIssueTypes',
                'lgas',
                'tripDestinationchecker'
            )
        );
    }

    public function storeOrderEvent(Request $request) {
        $check = tripEvent::WHERE('current_date', $request->current_date)->WHERE('trip_id', $request->trip_id)->exists();
        if($check) {
            return 'cant_add';
        }
        else{ 
            if($request->tracker == 6) { $onjourney_status = 1; $destination_status = 0; $offload_status = 0; }
            if($request->tracker == 7) { $onjourney_status = 1; $destination_status = 1; $offload_status = 0;}
            if($request->tracker == 8) { $onjourney_status = 1; $destination_status = 1; $offload_status = 1; }

            if($request->tracker == 7 || $request->tracker == 8) {
                $lastTripEvent = tripEvent::WHERE('trip_id', $request->trip_id)->GET()->LAST();
                if($lastTripEvent) {
                    $event = tripEvent::CREATE([
                        'afternoon_issue_type' => $lastTripEvent->afternoon_issue_type,
                        'afternoon_lga'=> $lastTripEvent->afternoon_lga,
                        'current_date' => $request->current_date, 
                        'destination_status' => $destination_status,
                        'journey_status' => $onjourney_status,
                        'location_check_one' => $lastTripEvent->location_check_one,
                        'location_check_two' => $lastTripEvent->location_check_two,
                        'location_one_comment' => $lastTripEvent->location_one_comment,
                        'location_two_comment' => $lastTripEvent->location_two_comment,
                        'morning_issue_type' => $lastTripEvent->morning_issue_type,
                        'morning_lga' => $lastTripEvent->morning_lga,
                        'offload_end_time' => $request->offload_end_time,
                        'offload_issue_type' => $request->offload_issue_type,
                        'offload_start_time' => $request->offload_start_time,
                        'offloaded_location' => $request->offloaded_location,
                        'offloading_status' =>  $offload_status,
                        'time_arrived_destination' => $request->tad,
                        'trip_id' => $request->trip_id,
                        'gate_in_time_destination' => $request->gate_in_destination_timestamp,

                    ]);
                    $event->save();
                }
                else {
                    $tripEvent = tripEvent::CREATE([
                        'morning_lga' => $request->lga,
                        'current_date' => $request->current_date, 
                        'morning_issue_type' => $request->morning_issue_type,
                        'location_check_one' => $request->location_check_one,
                        'location_one_comment' => $request->location_one_comment,
                        'trip_id' => $request->trip_id,
                        'journey_status' => $onjourney_status,
                        'destination_status' => $destination_status,
                        'offloading_status' => $offload_status,
                        'afternoon_issue_type' => $request->afternoon_issue_type,
                        'location_two_comment' => $request->location_two_comment,
                        'location_check_two' => $request->location_check_two,
                        'afternoon_lga' => $request->lga,
                        'offload_end_time' => $request->offload_end_time,
                        'offload_issue_type' => $request->offload_issue_type,
                        'offload_start_time' => $request->offload_start_time,
                        'offloaded_location' => $request->offloaded_location,
                        'time_arrived_destination' => $request->tad,
                        'gate_in_time_destination' => $request->gate_in_destination_timestamp,

                    ]);
                    $tripEvent->save();
                }
            }

            if($request->tracker == 6) {
                $tripEvent = tripEvent::CREATE([
                    'morning_lga' => $request->lga,
                    'current_date' => $request->current_date, 
                    'morning_issue_type' => $request->morning_issue_type,
                    'location_check_one' => $request->location_check_one,
                    'location_one_comment' => $request->location_one_comment,
                    'morning_issue_type' => $request->morning_issue_type,
                    'trip_id' => $request->trip_id,
                    'journey_status' => $onjourney_status,
                    'destination_status' => $destination_status,
                    'offloading_status' => $offload_status,
                    'afternoon_issue_type' => $request->afternoon_issue_type,
                    'location_two_comment' => $request->location_two_comment,
                    'location_check_two' => $request->location_check_two,
                    'afternoon_lga' => $request->lga
                ]);
                $tripEvent->save();
            }
            $recid = trip::findOrFail($request->trip_id);
            $recid->tracker = $request->tracker;
            $recid->save();
            
            $trip_id = $request->trip_id;
            $order_id = $request->orderId;
            $loading_site = $request->loading_site;
            $result = 'saved`'.$this->eventLogRecord($trip_id, $order_id, $loading_site); 
            
            return $result;  
        }
    }

    public function editeventTrip($orderId, $clientName, $event_id) {
        return $recid = tripEvent::findOrFail($event_id);
    }

    public function updateTripEvent(Request $request, $id){
        $check = tripEvent::WHERE('current_date', $request->current_date)->WHERE('trip_id', $request->trip_id)->WHERE('id', '<>', $id)->exists();
        if($check) {
            return 'cant_add';
        }
        else{ 
            if($request->tracker == 6) { $onjourney_status = 1; $destination_status = 0; $offload_status = 0; }
            if($request->tracker == 7) { $onjourney_status = 1; $destination_status = 1; $offload_status = 0;}
            if($request->tracker == 8) { $onjourney_status = 1; $destination_status = 1; $offload_status = 1; }

            //return $request->all();

            $recid = tripEvent::findOrFail($id);
            $recid->UPDATE([
                'morning_lga' => $request->lga,
                'current_date' => $request->current_date, 
                'morning_issue_type' => $request->morning_issue_type,
                'location_check_one' => $request->location_check_one,
                'location_one_comment' => $request->location_one_comment,
                'morning_issue_type' => $request->morning_issue_type,
                'trip_id' => $request->trip_id,
                'journey_status' => $onjourney_status,
                'destination_status' => $destination_status,
                'offloading_status' => $offload_status,
                'afternoon_issue_type' => $request->afternoon_issue_type,
                'location_two_comment' => $request->location_two_comment,
                'location_check_two' => $request->location_check_two,
                'time_arrived_destination' => $request->time_arrived_destination,
                'gate_in_time_destination' => $request->gate_in_destination_timestamp,
                'destination_status' => $destination_status,
                'offload_issue_type' => $request->offload_issue_type,
                'offload_start_time' => $request->offload_start_time,
                'offload_end_time' => $request->offload_end_time,
                'offload_status' => $offload_status,
                'offloaded_location' => $request->offloaded_location
            ]);
            // $recid->UPDATE($request->all());
            $updateTracker = trip::findOrFail($request->trip_id);
            $updateTracker->tracker = $request->tracker;
            $updateTracker->save();

            $changes = tripChanges::CREATE(['trip_id' => $recid->trip_id, 'user_id' => $request->user_id, 'changed_keys' => $request->tracker, 'changed_values' => 'On Journey Details']);
            
            $trip_id = $request->trip_id;
            $order_id = $request->orderId;
            $loading_site = $request->loading_site;
            $result = 'updated`'.$this->eventLogRecord($trip_id, $order_id, $loading_site);
            return $result;
        }

    }

    public function waybill($orderId, $clientName) {
        $client_name = str_replace('-', ' ', $clientName);
        $tracker_ = trip::SELECT('id', 'tracker')->WHERE('trip_id', $orderId)->GET();
        $tracker = $tracker_[0]['tracker'];
        $tripId = $tracker_[0]['id'];
        $tripwaybill = tripWaybill::WHERE('trip_id', $tripId)->GET();
        $waybillstatus = tripWaybillStatus::WHERE('trip_id', $tripId)->GET();
        return view('orders.waybill',
            compact(
                'orderId',
                'client_name',
                'tracker',
                'tripId',
                'tripwaybill',
                'waybillstatus'
            )
        );
    }

    public function storewaybilldetails(Request $request) {
        foreach($request->invoice_no as $key=> $invoice_number) {
            if(isset($invoice_number) && $invoice_number != ''){
                $salesorderandinvoice = tripWaybill::CREATE([
                    'trip_id' => $request->trip_id, 
                    'sales_order_no' => strtoupper(str_replace('/', '', $request->sales_order_no[$key])),
                ]);
                $salesorderandinvoice->waybill_status = 0;
                $salesorderandinvoice->invoice_status = 0;
                $salesorderandinvoice->invoice_no = strtoupper(str_replace('/', '', $invoice_number));
                $salesorderandinvoice->save();
            }

            $id = $salesorderandinvoice->id;
            if($request->hasFile('photo')){
                $recid = tripWaybill::findOrFail($id);
                $photo = $request->file('photo');
                $name = $recid->sales_order_no.'.'.$photo[$key]->getClientOriginalExtension();
                $destination_path = public_path('assets/img/waybills/');
                $waybillPath = $destination_path."/".$name;
                $photo[$key]->move($destination_path, $name);
                $recid->photo = $name;
                $recid->remark = 'uploaded';
                $recid->approve_waybill = 0;
                $recid->waybill_status = 1;
                $recid->moment_uploaded = date('Y-m-d\TH:i');
                $recid->save();
            }
        }
        $waybillstatus = tripWaybillStatus::firstOrNew(['trip_id' => $request->trip_id]);
        $waybillstatus->waybill_status = FALSE;
        $waybillstatus->comment = 'With Driver';
        $waybillstatus->save();

        $changes = tripChanges::CREATE(['trip_id' => $request->trip_id, 'user_id' => $request->user_id, 'changed_keys' => 9, 'changed_values' => 'Waybill Details Entered']);

        return 'saved';
    }

    public function editwaybill($orderId, $clientName, $id) {
        $client_name = str_replace('-', ' ', $clientName);
        $tracker_ = trip::SELECT('id', 'tracker')->WHERE('trip_id', $orderId)->GET();
        $tracker = $tracker_[0]['tracker'];
        $tripId = $tracker_[0]['id'];
        $tripwaybill = tripWaybill::WHERE('trip_id', $tripId)->GET();
        $recid = tripWaybill::findOrFail($id);
        $waybillstatus = tripWaybillStatus::WHERE('trip_id', $tripId)->GET();
        return view('orders.waybill',
            compact(
                'orderId',
                'client_name',
                'tracker',
                'tripId',
                'tripwaybill',
                'recid',
                'waybillstatus'
            )
        );
    }

    public function updatewaybill(Request $request, $id) {
        $salesOrderNumber = $request->sales_order_no;
        $invoiceNumber = $request->invoice_no;
        foreach($invoiceNumber as $key=> $invoice_no) {
            $recid = tripWaybill::findOrFail($id);
            $updatedInvoiceNumber = $invoiceNumber[$key];

            DB::UPDATE(
                DB::RAW(
                    'UPDATE tbl_kaya_trip_waybills SET sales_order_no = "'.strtoupper(str_replace('/', '', $salesOrderNumber[$key])).'", invoice_no = "'.strtoupper(str_replace('/', '', $invoice_no)).'" WHERE id = "'.$id.'" '
                )
            );
            
            if($request->hasFile('photo')){
                $photo = $request->file('photo');
                $name = $recid->sales_order_no.'.'.$photo[$key]->getClientOriginalExtension();
                $destination_path = public_path('assets/img/waybills/');
                $waybillPath = $destination_path."/".$name;
                $photo[$key]->move($destination_path, $name);
                $recid->photo = $name;
                $recid->remark = 'uploaded';
                $recid->approve_waybill = 0;
                $recid->waybill_status = 1;
                $recid->moment_uploaded = date('Y-m-d\TH:i');
            }
            $recid->save();
        }

        $changes = tripChanges::CREATE(['trip_id' => $request->trip_id, 'user_id' => $request->user_id, 'changed_keys' => 10, 'changed_values' => 'Waybill Details Updated']);
        
        return 'updated';
    }

    public function approveWaybill(Request $request, $id) {
        $recid = tripWaybill::findOrFail($id);
        $recid->approve_waybill = 1;
        $recid->moment_approved = date('Y-m-d\TH:i');
        $recid->save();
        return 'approved';
    }

    public function waybillRemark(Request $request) {
        $tripwaybillstatus = tripWaybillStatus::firstOrNew(['trip_id'=>$request->trip_id]);
        $tripwaybillstatus->waybill_status = $request->waybill_status;
        if($request->waybill_status == 1){
            $tripwaybillstatus->comment = 'Recieved';
        }
        else{
            $tripwaybillstatus->comment = $request->comment;
        }
        $tripwaybillstatus->save();

        $changes = tripChanges::CREATE(['trip_id' => $request->trip_id, 'user_id' => $request->user_id, 'changed_keys' => 12, 'changed_values' => 'Waybill Remark Updated']);


        return 'saved';


    }

    public function clientReport() {
        $clientlistings = client::SELECT('id', 'company_name')->GET();
        return view('orders.client-report', compact('clientlistings'));
    }

    public function displayClientReport(Request $request) {
        $client_id = $request->client_id;
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND a.tracker <= 8 AND a.client_id = '.$client_id.' AND a.completed_trip_report = FALSE '
            )
        );
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $tabledata = '<div class="card-header header-elements-inline">
                        <h5 class="card-title"><button class="btn btn-primary" id="downloadClientReport"><i class="icon icon-file-download"></i> Download Report</button></h5>
                    </div>';

        $tabledata.= '<table class="table table-bordered" id="exportTableData">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
                <tr class="font-weigth-semibold">
                    <td>#</td>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO.</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th>CUSTOMER</th>
                    <th>DESTINATION</th>
                    <th>PRODUCT</th>
                    <th>GATE IN</th>
                    <th>ARRIVAL AT LOADING BAY</th>
                    <th class="text-center">GATE OUT</th>
                    <th>LAST KNOWN LOCATION 1</th>
                    <th class="text-center">TIME & DATE</th>
                    <th class="text-center">LAST KNOWN LOCATION 2</th>
                    <th class="text-center">TIME & DATE </th>
                    <th>TIME ARRIVED DESTINATION</th>
                    <th class="text-center">DESTINATION GATE IN TIME</th>
                    <th>CURRENT STAGE</th>
                    <th>REMARKS</th>
                    <th><button class="btn btn-primary" id="sendForComplete">COMPLETED?</button></th>                  
                </tr>
            </thead>';

            $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.'hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>    
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.strtoupper($trip->exact_location_id).'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_one_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'gate_in_time_destination').'</td>
                    <td>'.$current_stage.'</td>
                    <td class="font-weight-semibold"></td>
                    <td class="text-center"><input type="checkbox" name="markAsCompleted[]" value="'.$trip->id.'"></td>                        
                    
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No pending trip.</td>
                </tr>';
            }       

        $tabledata.='</tbody>
        </table>';

        return $tabledata;
    }

    public function markCompletedReport(Request $request) {
        if(isset($request->markAsCompleted)) {
            foreach($request->markAsCompleted as $key=> $completedTripReport){
                $recid = trip::findOrFail($completedTripReport);
                $recid->completed_trip_report = TRUE;
                $recid->save();
            }
            return 'saved';
        }
    }

    public function showOnlyOnJourneyTrip() {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        $loadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();

        $onJourneyTrips = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.first_name, h.last_name FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN users h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.user_id = h.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND a.tracker BETWEEN \'5\' AND \'7\' ORDER BY a.trip_id DESC '
            )
        );
        
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();
        $products = product::SELECT('id', 'product')->ORDERBY('product')->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $invoiceCriteria = tripWaybillStatus::GET();
        $trippayments = tripPayment::GET();
        
        $onjourneyIssueTypes = IssueType::WHERE('issue_category', 1)->GET();
        $offloadIssueTypes = IssueType::WHERE('issue_category', 2)->GET();
        $lgas = DB::SELECT(
            DB::RAW(
                'SELECT lga_name FROM tbl_regional_local_govt WHERE regional_country_id = \'94\' ORDER BY lga_name ASC '
            )
        );
        $truckTypes = truckType::SELECT('truck_type')->DISTINCT()->GET();
        $tonnages = truckType::ORDERBY('tonnage', 'ASC')->SELECT('tonnage')->DISTINCT()->GET();


        return view('orders.on-journey', compact('onJourneyTrips', 'tripWaybills', 'tripEvents', 'waybillstatuses', 'clients', 'loadingSites', 'transporters', 'products', 'states', 'invoiceCriteria', 'trippayments', 'onjourneyIssueTypes', 'offloadIssueTypes', 'lgas', 'truckTypes', 'tonnages'));
    }
    
    public function eventLog(Request $request) {
        $trip_id = $request->tripId;
        $loading_site = $request->loadingSite;
        $order_id = $request->kaid;
        return $this->eventLogRecord($trip_id, $order_id, $loading_site);
    }
    
    function eventLogRecord($tripId, $kaid, $loadingSite) {

        $response = '<div class="card-header header-elements-inline">
            <h5 class="card-title">Events Log of  '.$kaid.' at '.strtoupper($loadingSite).'</h5>
        </div>';

        $tripEvents = tripEvent::ORDERBY('created_at', 'DESC')->WHERE('trip_id', $tripId)->GET();

        $response.='<div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-info">
                    <tr style="font-size:11px;">
                        <th>#</th>
                        <th>Date of Event</th>
                        <th>Morning Visibility</th>
                        <th>Afternoon Visibility</th>
                        <th>Destination</th>
                        <th colspan="2">Offload Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';
                    $counter = 0; $current_date = date("Y-m-d");
                    if(count($tripEvents)) {
                        foreach($tripEvents as $tripevent) {
                            $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                            
                        $response.='<tr class="{{$css}}" style="font-size:11px">
                            <td>'.$counter.'</td>
                            <td>'.$tripevent->current_date.'</td>
                            <td>
                                <p class="m-0">'.date('d/m/Y - h:iA', strtotime($tripevent->location_check_one)).' 
                                ('.$tripevent->location_one_comment.')</p>
                                <span class="badge badge-primary block">Lga: '.$tripevent->morning_lga.'</span>';

                                if($tripevent->morning_issue_type) {
                                 $response.='<span class="badge badge-danger block">Issue: '.$tripevent->morning_issue_type.'</span>';
                                }
                            $response.='</td>
                            <td>';
                                if($tripevent->location_check_two) {
                                    $response.='<p class="m-0"> '.date('d/m/Y - h:iA', strtotime($tripevent->location_check_two)).' 
                                    ('.$tripevent->location_two_comment.')</p>
                                    <span class="badge badge-primary block">Lga: '.$tripevent->afternoon_lga.'</span>';
                                }
                                if($tripevent->afternoon_issue_type) {
                                    $response.='<span class="badge badge-danger block">Issue: '.$tripevent->afternoon_issue_type.'</span>';
                                }
                            $response.='</td>  
                            <td>';
                                if($tripevent->time_arrived_destination) {
                                    $response.= date('d/m/Y - h:iA', strtotime($tripevent->time_arrived_destination));
                                }
                                if($tripevent->gate_in_time_destination) {
                                    $response.='<span class="d-block badge badge-primary">'.date('d/m/Y - h:iA', strtotime($tripevent->gate_in_time_destination)).'</span>';
                                }
                            $response.='</td>
                            <td colspan="2">';
                                if($tripevent->offload_end_time) {
                                    $response.='<p class="m-0">'.date('d/m/Y - h:iA', strtotime($tripevent->offload_start_time)).' - 
                                    '.date('d/m/Y - h:iA', strtotime($tripevent->offload_end_time)).'</p>';
                                }
                                if($tripevent->offload_issue_type ) {
                                $response.='<span class="badge badge-danger">Issue: '.$tripevent->offload_issue_type.'</span>';
                                }
                            $response.='</td>
                            <td>
                                <div class="list-icons">';
                                    if(($counter == 1) && ($current_date == $tripevent->current_date) || Auth::user()->role_id == 1) {
                                        $response.="<i class=\"icon-pencil7 text-primary pointer updateTripEvent \" id=".$tripevent->id." value=".$loadingSite." name=".$kaid."></i>";
                                    }
                                    else{
                                        $response.= '<span class="error">Access denied</span>';
                                    }
                                $response.='</div>
                            </td>
                        </tr>';   
                        }
                    } else {
                        $response.='<tr>
                            <td class="table-success" colspan="10">You\'ve not add any event for this trip</td>
                        </tr>';
                    }
                $response.='</tbody>
            </table>
        </div>';

        return $response;

    }

    function voidTrip($id){
        $recid = trip::findOrFail($id);
        $recid->trip_status = FALSE;
        $recid->save();
        return 'voided';
    }

    public function showVoidedTrips(){
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->ORDERBY('transporter_name', 'ASC')->GET();
        $loadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'0\' AND tracker > \'0\' ORDER BY a.trip_id DESC LIMIT 50'
            )
        );
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();
        $clientRates = DB::SELECT(
            DB::RAW(
                'SELECT a.client_id, a.amount_rate, b.* FROM `tbl_kaya_client_fare_rates` a LEFT JOIN `tbl_kaya_transporter_rates` b ON a.from_state_id = b.transporter_from_state_id AND a.to_state_id = b.transporter_to_state_id AND a.destination = b.transporter_destination'
            )
        );
        $trippayments = tripPayment::GET();
        $products = product::SELECT('id', 'product')->ORDERBY('product')->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $invoiceCriteria = tripWaybillStatus::GET();

        return view('orders.voided-trips',
            compact(
                'orders',
                'tripWaybills',
                'tripEvents',
                'waybillstatuses',
                'clientRates',
                'trippayments',
                'clients',
                'transporters',
                'loadingSites',
                'products',
                'states',
                'invoiceCriteria'
            )
        );
    }


    function timeDifference($gatedIn, $timeArrivedLoadingBay){
        if($gatedIn && $timeArrivedLoadingBay != '') {
            $mydate1 = new DateTime($gatedIn);
            $mydate2 = new DateTime($timeArrivedLoadingBay);
            $interval = $mydate1->diff($mydate2);
            $elapsed = $interval->format('%a days %h hours %i minutes');
        }
        else{
            $elapsed = '';
        }
        return $elapsed;
    }
    
    function eventdetails($arrayRecord, $master, $field){
        foreach($arrayRecord as $object) {
            if($master->id == $object->trip_id) {
                if(($field == 'location_check_one' && $field!='')){
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'location_check_two' && $object->$field!='')) {
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'time_arrived_destination' && $object->$field!='')) {
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'offload_start_time' && $object->$field!='')){
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'offload_end_time' && $object->$field!='')){
                     return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'gate_in_time_destination' && $object->$field!='')){
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                else{
                    return $object->$field;
                }
                break;
            }
            continue;
        }
    }

    public function deleteSpecificWaybill(Request $request) {
        $id = $request->id;
        $user = $request->user;
        $recid = tripWaybill::findOrFail($id);
        $recid->delete();

        $changes = tripChanges::CREATE(['trip_id' => $recid->trip_id, 'user_id' => $user, 'changed_keys' => 11, 'changed_values' => 'Waybill Deleted']);


        return 'deleted';
    }

    public function viewTripThread(Request $request) {
        $tripDetails = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT a.id, a.trip_id, a.gated_out, b.truck_no FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_trip_changes c ON a .truck_id = b.id AND a.id = c.trip_id ORDER BY a.trip_id  DESC'
            )
        );
        $collection = new Collection($tripDetails);
        $perPage = 100;
        $currentPage =  $request->get('page');
        $pagedData = $collection->slice($currentPage * $perPage, $perPage)->all();

        $path = url('/').'/view-trip-thread?'.$currentPage;
        
        $pagination = new LengthAwarePaginator(($pagedData), count($collection), $perPage );
        $pagination = $pagination->withPath($path);

        return view('orders.trip-thread', compact('pagination'));
    }

    public function specificTripThread(Request $request) {
        $trip_id = $request->id;

        $specificTripLog = DB::SELECT(
            DB::RAW(
                'SELECT a.first_name, a.last_name, a.photo, b.id, b.changed_keys, b.changed_values, b.updated_at FROM users a JOIN tbl_kaya_trip_changes b ON a.id = b.user_id WHERE trip_id = "'.$trip_id.'" ORDER BY updated_at DESC'
            )
        );
        if(count($specificTripLog)){
            foreach($specificTripLog as $thread) {
                if($thread->changed_keys == "0") { $operation = 'Voided'; }
                if($thread->changed_keys == "1") { $operation = 'Gate In'; }
                if($thread->changed_keys == "2") { $operation = 'Arrival at Loading Bay'; }
                if($thread->changed_keys == "3") { $operation = 'At Loading Bay'; }
                if($thread->changed_keys == "4") { $operation = 'Departed Loading Bay'; }
                if($thread->changed_keys == "5") { $operation = 'Gated Out'; }
                if($thread->changed_keys == "6") { $operation = 'On Journey'; }
                if($thread->changed_keys == "7") { $operation = 'Arrived Destination'; }
                if($thread->changed_keys == "8") { $operation = 'Offloaded'; }
                if($thread->changed_keys == "9") { $operation = 'Waybill Entered'; }
                if($thread->changed_keys == "10") { $operation = 'Waybill details Updated'; }
                if($thread->changed_keys == "11") { $operation = 'Waybill Deleted'; }
                if($thread->changed_keys == "12") { $operation = 'Waybill comment'; }

                
                echo '<ul style="margin:0; padding:0">
                <li style="list-style-type:none">
                    <div style="width:50px; height:50px; border-radius:50%; overflow:hidden; padding:0;">';
                        if($thread->photo) {
                            echo "<img src=\"/assets/img/users/$thread->photo\" alt=".$thread->first_name." class=\"img img-rounded\" width=\"50\" height=\"50\">";
                        } else {
                            echo "<img src='/assets/img/no-photo.jpg' class=\"img img-rounded\" width=\"50\" height=\"50\">";
                        }     
                    echo '</div>
                </li>
                <li style="border-left:2px dashed #ccc; padding:4px; list-style-type:none; margin-left:25px;">
                    <span style="color:#333; font-size:11px; font-weight:bold;">Action by:</span>'.ucwords($thread->first_name).' '.ucwords($thread->last_name).' -> <span class="font-weight-bold">'.$operation.'</span>
                </li>
                ';
                
                
                $convertodate = strtotime($thread->updated_at);
                $readable_date = date('d-m-Y', $convertodate);

                $timestamp = $thread->updated_at;
                
                $time = '';
                
                echo '<li style="border-left:2px dashed #ccc; padding:4px; list-style-type:none; margin-left:25px;">
                    <span style="color:green; font-size:11px; font-weight:bold;">On:</span>'.$readable_date.'
                    <span style="color:#333; font-size:11px; font-weight:bold;"> at </span>'.date('h:i s A', strtotime($thread->updated_at)).'
                </li>
                
            </ul>';
            }
        }
        else{
            echo 'Sorry, we do not have any log for this trip.';
        }
        
    }
    
}
