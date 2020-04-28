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
use App\client;
use App\truckAvailability;




class truckAvailabilityController extends Controller
{
    public function index() {
        $loadingsites = loadingSite::ORDERBY('loading_site', 'ASC')->GET();
        $transporters = transporter::ORDERBY('transporter_name', 'ASC')->GET();
        $trucks = trucks::ORDERBY('truck_no', 'ASC')->GET();
        $drivers = drivers::ORDERBY('driver_first_name', 'ASC')->GET();
        $products = product::ORDERBY('product', 'ASC')->GET();
        $clients = client::ORDERBY('company_name')->GET();
        $states = DB::SELECT(DB::RAW(
                'SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $truckTypes = truckType::SELECT('truck_type')->ORDERBY('truck_type', 'ASC')->DISTINCT()->GET();
        return view('truck-availability.create', 
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

    public function store(Request $request){
        $validatedata = $request->validate([
            'client_id' => 'required | integer',
            'loading_site_id' => 'required | integer',
            'product_id' => 'required | integer',
            'destination_state_id' => 'required | integer',
            'exact_location_id' => 'required | string',
            'truck_status' => 'required'
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

        $checker = truckAvailability::WHERE('truck_id', $request->truck_id)->WHERE('status', FALSE)->exists();
        if($checker){
            return 'exists';
        }
        else{
            $addTruckAvailability = truckAvailability::firstOrNew(['client_id' => $request->client_id, 'loading_site_id' => $request->loading_site_id, 'truck_id' => $request->truck_id, 'transporter_id' => $request->transporter_id, 'status' => false]);
            $addTruckAvailability->driver_id = $request->driver_id;
            $addTruckAvailability->product_id = $request->product_id;
            $addTruckAvailability->destination_state_id = $request->destination_state_id;
            $addTruckAvailability->exact_location_id = $request->exact_location_id;
            $addTruckAvailability->truck_status = $request->truck_status;
            $addTruckAvailability->reported_by = $request->reported_by;
            $addTruckAvailability->dated = $request->dated;
            $addTruckAvailability->save();
            return 'saved';
        }
    }
    
    public function update(Request $request) {
        $recid = truckAvailability::findOrFail($request->truck_availability_id);
        $recid->truck_status = $request->truck_status;
        $recid->save();
        return 'updated';
    }

    public function destroy($id){
        $recid = truckAvailability::findOrFail($id);
        $recid->delete();
        return 'deleted';
    }

    public function show(){
        $availableTrucks = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.loading_site, d.truck_no, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no, g.product, h.state, i.first_name, i.last_name, j.tonnage, j.truck_type FROM tbl_kaya_truck_availabilities a JOIN tbl_kaya_clients b JOIN tbl_kaya_loading_sites c JOIN tbl_kaya_trucks d JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f JOIN tbl_kaya_products g JOIN tbl_regional_state h JOIN users i JOIN tbl_kaya_truck_types j ON a.client_id = b.id AND a.loading_site_id = c.id AND a.truck_id = d.id AND a.transporter_id = e.id and a.driver_id = f.id and a.product_id = g.id and a.destination_state_id = h.regional_state_id and a.reported_by = i.id AND d.truck_type_id = j.id  WHERE a.status = FALSE'
            )
        );
        return view('truck-availability.show-truck-availability', 
            compact(
                'availableTrucks'
            )
        );
    }
}
