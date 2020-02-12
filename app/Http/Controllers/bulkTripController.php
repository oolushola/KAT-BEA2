<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\driversRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\trip;
use App\tripWaybill;
use App\drivers;
use App\truckDriver;



class bulkTripController extends Controller
{
    public function uploadBulkTrip(Request $request) {
        
        $upload = $request->file('bulktrip');
        $filePath = $upload->getRealPath();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $escapedHeader = [];
        foreach($header as $key => $value) {
            $lowercaseheader  = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '_', $lowercaseheader);
            array_push($escapedHeader, $escapedItem);
        }
            while($columns = fgetcsv($file))
            {
                if($columns[0] == "") {
                    continue;
                }
                $data = array_combine($escapedHeader, $columns);
                foreach($data as $key =>  $value) {
                [$value] = ($key=="client_rate" || $key="transporter_rate")?(integer)$value:(float)$value;
                }  
                        
            $trip_id = $data['trip_id'];
            $gate_in = $data['gate_in'];
            $client_id = $data['client_id'];
            $loading_site_id = $data['loading_site_id'];
            $transporter_id = $data['transporter_id'];
            $truck_id = $data['truck_id'];
            $driver_id = $data['driver_id'];
            $product_id = $data['product_id'];
            $destination_state_id = $data['destination_state_id'];
            $exact_location_id = $data['exact_location_id'];
            $account_officer = $data['account_officer'];
            $arrival_at_loading_bay = $data['arrival_at_loading_bay'];
            $loading_start_time = $data['loading_start_time'];
            $loading_end_time = $data['loading_end_time'];
            $departure_date_time = $data['departure_date_time'];
            $gated_out = $data['gated_out'];
            $customers_name = $data['customers_name'];
            $customer_no = $data['customer_no'];
            $loaded_quantity = $data['loaded_quantity'];
            $loaded_weight = $data['loaded_weight'];
            $customer_address = $data['customer_address'];
            $tracker = $data['tracker'];
            $trip_status = $data['trip_status'];
            $day = $data['day'];
            $month = $data['month'];
            $year = $data['year'];
            $user_id = $data['user_id'];
            $client_rate = $data['client_rate'];
            $transporter_rate = $data['transporter_rate'];
            
            $bulkTrip = trip::firstOrNew(['trip_id'=>$trip_id]);
            $bulkTrip->trip_id = $trip_id;
            $bulkTrip->gate_in = $gate_in;
            $bulkTrip->client_id = $client_id;
            $bulkTrip->loading_site_id = $loading_site_id;
            $bulkTrip->transporter_id = $transporter_id;
            $bulkTrip->truck_id = $truck_id;
            $bulkTrip->driver_id = $driver_id;
            $bulkTrip->product_id = $product_id;
            $bulkTrip->destination_state_id = $destination_state_id;

            $bulkTrip->destination_state_id = $destination_state_id;
            $bulkTrip->exact_location_id = $exact_location_id;
            $bulkTrip->account_officer = $account_officer;
            $bulkTrip->arrival_at_loading_bay = $arrival_at_loading_bay;
            $bulkTrip->loading_start_time = $loading_start_time;
            $bulkTrip->loading_end_time = $loading_end_time;
            $bulkTrip->departure_date_time = $departure_date_time;
            $bulkTrip->gated_out = $gated_out;
            $bulkTrip->customers_name = $customers_name;
            $bulkTrip->customer_no = $customer_no;
            $bulkTrip->loaded_quantity = $loaded_quantity;
            $bulkTrip->loaded_weight = $loaded_weight;
            $bulkTrip->customer_address = $customer_address;
            $bulkTrip->tracker = $tracker;
            $bulkTrip->trip_status = $trip_status;
            $bulkTrip->day = $day;
            $bulkTrip->month = $month;
            $bulkTrip->year = $year;
            $bulkTrip->user_id = $user_id;
            $bulkTrip->client_rate = $client_rate;
            $bulkTrip->transporter_rate = $transporter_rate;

            $bulkTrip->save();
            }
        return redirect()->back();
    }

    public function bulksalesOrder(Request $request) {
        $upload = $request->file('bulksalesOrder');
        $filePath = $upload->getRealPath();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $escapedHeader = [];
        foreach($header as $key => $value) {
            $lowercaseheader  = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '_', $lowercaseheader);
            array_push($escapedHeader, $escapedItem);
        }
            while($columns = fgetcsv($file))
            {
                if($columns[0] == "") {
                    continue;
                }
                $data = array_combine($escapedHeader, $columns);
                        
            $trip_id = $data['trip_id'];
            $sales_order_no = $data['sales_order_no'];
            $invoice_no = $data['invoice_no'];
            $waybill_status = $data['waybill_status'];
            
            $salesOrderBulk = tripWaybill::firstOrNew(['trip_id'=>$trip_id, 'sales_order_no' => $sales_order_no, 'invoice_no' => $invoice_no]);
            $salesOrderBulk->trip_id = $trip_id;
            $salesOrderBulk->sales_order_no = $sales_order_no;
            $salesOrderBulk->invoice_no = $invoice_no;
            $salesOrderBulk->waybill_status = $waybill_status;
            $salesOrderBulk->save();
            }
        return redirect()->back();
    }

    public function bulkdrivers(Request $request) {
        $upload = $request->file('uploadBulkDrivers');
        $filePath = $upload->getRealPath();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $escapedHeader = [];
        foreach($header as $key => $value) {
            $lowercaseheader  = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '_', $lowercaseheader);
            array_push($escapedHeader, $escapedItem);
        }
            
            while($columns = fgetcsv($file))
            {
                
                if($columns[0] == "") {
                    continue;
                }
                
                $data = array_combine($escapedHeader, $columns);
 
                $licence_no = $data['licence_no'];
                $driver_first_name = $data['driver_first_name'];
                $driver_last_name = $data['driver_last_name'];
                $driver_phone_number = $data['driver_phone_number'];
                $motor_boy_first_name = $data['motor_boy_first_name'];
                $motor_boy_last_name = $data['motor_boy_last_name'];
                $motor_boy_phone_no = $data['motor_boy_phone_no'];
                
                $driverBulk = drivers::firstOrNew(['driver_first_name'=>$driver_first_name, 'driver_phone_number' => $driver_phone_number]);
                $driverBulk->licence_no = $licence_no;
                $driverBulk->driver_first_name = $driver_first_name;
                $driverBulk->driver_last_name = $driver_last_name;
                $driverBulk->driver_phone_number = $driver_phone_number;
                $driverBulk->motor_boy_first_name = $motor_boy_first_name;
                $driverBulk->motor_boy_last_name = $motor_boy_last_name;
                $driverBulk->motor_boy_phone_no = $motor_boy_phone_no;
                $driverBulk->save();
                
            }
        return redirect()->back();
    }

    public function truckDriver(Request $request) {
        $upload = $request->file('uploadBulkDriverTruck');
        $filePath = $upload->getRealPath();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $escapedHeader = [];
        foreach($header as $key => $value) {
            $lowercaseheader  = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '_', $lowercaseheader);
            array_push($escapedHeader, $escapedItem);
        }
            
            while($columns = fgetcsv($file))
            {
                if($columns[0] == "") {
                    continue;
                }
                
                $data = array_combine($escapedHeader, $columns);
                $truck_id = $data['truck_id'];
                $driver_id = $data['driver_id'];
                
                $truckDriver = truckDriver::firstOrNew(['truck_id'=>$truck_id, 'driver_id' => $driver_id]);
                $truckDriver->truck_id = $truck_id;
                $truckDriver->driver_id = $driver_id;
                $truckDriver->save();
            }
        return redirect()->back();
    }

    public function eventTime(Request $request) {
        $upload = $request->file('eventTime');
        $filePath = $upload->getRealPath();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $escapedHeader = [];
        foreach($header as $key => $value) {
            $lowercaseheader  = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '_', $lowercaseheader);
            array_push($escapedHeader, $escapedItem);
        }
            
            while($columns = fgetcsv($file))
            {
                if($columns[0] == "") {
                    continue;
                }
                
                $data = array_combine($escapedHeader, $columns);
                $truck_id = $data['truck_id'];
                $driver_id = $data['driver_id'];
                
                $truckDriver = truckDriver::firstOrNew(['truck_id'=>$truck_id, 'driver_id' => $driver_id]);
                $truckDriver->truck_id = $truck_id;
                $truckDriver->driver_id = $driver_id;
                $truckDriver->save();
            }
        return redirect()->back();
    }
}
