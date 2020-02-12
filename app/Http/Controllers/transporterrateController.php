<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\transporterRate;

class transporterrateController extends Controller
{
    public function loadClientStates(Request $request) {
        $answer = '<label>State</label>
                    <select class="form-control form-control-select2" name="state_id" id="state">
                    <option value="0">Choose State Domiciled</option>';
        $country_id = $request->country_id;
         $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = '.$country_id.' ORDER BY state ASC'));
        foreach($states as $state) {
            $answer.='<option value='.$state->regional_state_id.'>'.$state->state.'</option>';
        }     
        $answer.='</select>';

        return $answer;
    }

    public function index() {
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC '));
        $transporterRates = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.state FROM `tbl_kaya_transporter_rates` a JOIN tbl_regional_state b ON a.transporter_to_state_id = b.regional_state_id'
            )
        );
        return view('finance.transporter-rate.create',
            compact(
                'states',
                'transporterRates'
            )
        );
    }

    public function store(Request $request) {
        if($request->hasFile('file')) {
        $upload = $request->file('file');
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
                $value = ($key=="transporter_from_state_id" || $key="transporter_to_state_id")?(integer)$value:(float)$value;
             }             
            $transporter_from_state_id = $data['transporter_from_state_id'];
            $transporter_to_state_id = $data['transporter_to_state_id'];
            $transporter_destination = $data['transporter_destination'];
            $transporter_tonnage = $data['transporter_tonnage'];
            $transporter_amount_rate = $data['transporter_amount_rate'];
            
            $fareRates = transporterRate::firstOrNew(['transporter_from_state_id'=>$transporter_from_state_id, 'transporter_to_state_id'=>$transporter_to_state_id, 'transporter_destination'=>$transporter_destination]);
            $fareRates->transporter_from_state_id = $transporter_from_state_id;
            $fareRates->transporter_to_state_id = $transporter_to_state_id;
            $fareRates->transporter_destination = $transporter_destination;
            $fareRates->transporter_tonnage = $transporter_tonnage;
            $fareRates->transporter_amount_rate = $transporter_amount_rate;
            $fareRates->save();
         }
        }
        else{
            $validatedata = $request->validate([
                'transporter_from_state_id' => 'required|integer',
                'transporter_to_state_id' => 'required|integer',
                'transporter_destination'=> 'required|string',
                'transporter_tonnage' => 'required',
                'transporter_amount_rate' => 'required|between:0,99.99',
            ]);
            $check = transporterRate::WHERE("transporter_from_state_id", $request->transporter_from_state_id)->WHERE('transporter_to_state_id', $request->transporter_to_state_id)->WHERE('transporter_destination', $request->transporter_destination)->WHERE('transporter_amount_rate', $request->transporter_amount_rate)->exists();
            if($check) {
                return 'exists';
            }
            else{
                transporterRate::CREATE($request->all());
            }
        }
        return 'saved';
    }

    public function edit($id) {
        $recid = transporterRate::findOrFail($id);
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC '));
        $transporterRates = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.state FROM `tbl_kaya_transporter_rates` a JOIN tbl_regional_state b ON a.transporter_to_state_id = b.regional_state_id'
            )
        );

        return view('finance.transporter-rate.create',
            compact(
                'states',
                'transporterRates',
                'recid'
            )
        );
    }

    public function update(Request $request, $id) {
        $validatedata = $request->validate([
            'transporter_from_state_id' => 'required|integer',
            'transporter_to_state_id' => 'required|integer',
            'transporter_destination'=> 'required|string',
            'transporter_tonnage' => 'required',
            'transporter_amount_rate' => 'required|between:0,99.99',
        ]);
        $check = transporterRate::WHERE("transporter_from_state_id", $request->transporter_from_state_id)->WHERE('transporter_to_state_id', $request->transporter_to_state_id)->WHERE('transporter_destination', $request->transporter_destination)->WHERE('transporter_amount_rate', $request->transporter_amount_rate)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else{
            $recid = transporterRate::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }
}
