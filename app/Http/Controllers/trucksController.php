<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\trucksRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\trucks;
use App\transporter;
use App\truckType;

class trucksController extends Controller
{
    public function index() {
        $transporters = transporter::ORDERBY('transporter_name', 'ASC')->GET();
        $truckTypes = truckType::ORDERBY('truck_type', 'ASC')->GET();
        $trucks = trucks::ORDERBY('truck_no')->PAGINATE(50);
        return view('transportation.truck', 
            compact(
                'transporters',
                'truckTypes',
                'trucks'
            )
        );
    }

    public function store(trucksRequest $request) {
        $check = trucks::WHERE('transporter_id', $request->transporter_id)->WHERE('truck_type_id', $request->truck_type_id)->WHERE('truck_no', $request->truck_no)->exists();
        if($check) {
            return 'exists';
        }
        else {
            trucks::CREATE($request->all());
            return 'saved';
        } 
    }

    public function edit($id) {
        $trucks = trucks::ORDERBY('truck_no')->PAGINATE(50);
        $recid = trucks::findOrFail($id);
        $transporters = transporter::ORDERBY('transporter_name', 'ASC')->GET();
        $truckTypes = truckType::ORDERBY('truck_type', 'ASC')->GET();
        return view('transportation.truck', 
            compact(
                'transporters',
                'truckTypes',
                'recid',
                'trucks'
            )
        );
    }

    public function update(trucksRequest $request, $id) {
        $check = trucks::WHERE('transporter_id', $request->transporter_id)->WHERE('truck_type_id', $request->truck_type_id)->WHERE('truck_no', $request->truck_no)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = trucks::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        } 
    }

    public function destroy() {

    }
}
