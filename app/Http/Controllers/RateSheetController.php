<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\RateSheet;
use App\Client;
use App\truckType;
use App\trucks;
use App\incentives;

class RateSheetController extends Controller
{

    public function uploadBulkRateSheet(Request $request) {
        $upload = $request->file('bulkRateSheet');
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
                    
            $client_id = $data['client_id'];
            $state = $data['state'];
            $tonnage = $data['tonnage'];
            $exact_location = $data['exact_location'];
            $client_rate = $data['client_rate'];
            $transporter_rate = $data['transporter_rate'];
            
            $bulkRateSheet = RateSheet::firstOrNew(['client_id'=>$client_id, 'state' => $state, 'tonnage' => $tonnage, 'exact_location' => $exact_location]);
            $bulkRateSheet->client_id = $client_id;
            $bulkRateSheet->client_rate = $client_rate;
            $bulkRateSheet->transporter_rate = $transporter_rate;
            $bulkRateSheet->tonnage = $tonnage;
            $bulkRateSheet->exact_location = $exact_location;
            $bulkRateSheet->save();
        }
        return 'updated';
        
    }

    public function index(Request $request) {
        $clients = Client::SELECT('client_status', 'id', 'company_name', 'client_alias')->WHERE('client_status', '1')->ORDERBY('company_name', 'ASC')->GET();
        $states = DB::SELECT(
            DB::RAW(
                'SELECT regional_state_id, state FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $ratesheets = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name, c.state FROM tbl_kaya_rate_sheets a JOIN tbl_kaya_clients b JOIN tbl_regional_state c ON a.client_id = b.id AND a.state = c.regional_state_id ORDER BY b.company_name ASC'
            )
        );
        return view('finance.rate-sheet.ratesheet', compact('clients', 'states', 'ratesheets'));
    }

    public function store(Request $request) {
        $validateResults = $request->validate([
            'client_id' => 'numeric | required',
            'state' => 'numeric | required',
            'exact_location' => 'string | required',
            'client_rate' => 'numeric | required',
            'transporter_rate' => 'numeric | required',
            'tonnage' => 'numeric | required'
        ]);
        $clientId = $request->client_id;
        $state = $request->state;
        $tonnage = $request->tonnage;
        $clientRate = $request->client_rate;
        $transporterRate = $request->transporter_rate;
        $exactDestination = $request->exact_destination;

        $checkData = RateSheet::WHERE('client_id', $clientId)->WHERE('state', $state)->WHERE('tonnage', $tonnage)->WHERE('exact_location', $exactDestination)->exists();
        if($checkData) {
            return 'recordExists';
        }
        $createRecord = RateSheet::CREATE($request->all());
        return 'saved';
    }

    public function show(Request $request, $id) {
        $recid = RateSheet::findOrFail($id);
        return $recid;
    }

    public function update(Request $request, $id) {
        $validateResults = $request->validate([
            'client_id' => 'numeric | required',
            'state' => 'numeric | required',
            'exact_location' => 'string | required',
            'client_rate' => 'numeric | required',
            'transporter_rate' => 'numeric | required',
            'tonnage' => 'numeric | required'
        ]);
         $checkData = RateSheet::WHERE('client_id', $request->client_id)->WHERE('state', $request->state)->WHERE('tonnage', $request->tonnage)->WHERE('exact_location', $request->exact_destination)->WHERE('id', '!=', $id)->exists();
        if($checkData) {
            return 'recordExists';
        }
        $recid = RateSheet::findOrFail($id);
        $recid->client_id = $request->client_id;
        $recid->state = $request->state;
        $recid->tonnage = $request->tonnage;
        $recid->transporter_rate = $request->tonnage;
        $recid->client_rate = $request->client_rate;
        $recid->exact_location = $request->exact_location;
        $recid->save();       
        return 'updated';
    }  
    
    public function rateVerification(Request $request) {
        $client_id = $request->clientId;
        $state = $request->state;
        $truckId = $request->truckId;
        $exactLocation = $request->exactLocation;
        $truck = trucks::findOrFail($truckId);
        $tonnage = truckType::findOrFail($truck->truck_type_id)->tonnage;

        $rate = RateSheet::WHERE("client_id", $client_id)->WHERE("state", $state)->WHERE("tonnage", $tonnage)->WHERE("exact_location", $exactLocation)->FIRST();
        
        if($rate) {
            $getDestinationIncentives = incentives::WHERE("state", $state)->WHERE("exact_location", $exactLocation)->FIRST();
            return $rate."`".$getDestinationIncentives;   
        }
        return "notfound";   
        
    }


}
