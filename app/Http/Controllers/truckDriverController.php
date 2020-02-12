<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\driversRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\trucks;
use App\drivers;
use App\truckDriver;

class truckDriverController extends Controller
{
    public function index() {
        $driverTruckPairing = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.id AS "\'driver_id\'", b.driver_first_name, b.driver_last_name, b.driver_phone_number, c.id AS "\'truck_id\'", c.truck_no FROM tbl_kaya_truck_drivers a JOIN tbl_kaya_drivers b JOIN tbl_kaya_trucks c ON a.driver_id = b.id AND a.truck_id = c.id'
            )
        );
        $truckslisting = trucks::ORDERBY('truck_no', 'ASC')->GET();
        $driverslisting = drivers::ORDERBY('driver_first_name', 'ASC')->GET();
        return view('transportation.truck-driver', compact('driverTruckPairing', 'truckslisting', 'driverslisting'));
    }

    public function store(Request $request) {
        $checker = truckDriver::WHERE('truck_id', $request->truck_id)->WHERE('driver_id', $request->driver_id)->exists();
        if($checker) {
            return 'exist';
        } else{
            truckDriver::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $driverTruckPairing = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.id AS "\'driver_id\'", b.driver_first_name, b.driver_last_name, b.driver_phone_number, c.id AS "\'truck_id\'", c.truck_no FROM tbl_kaya_truck_drivers a JOIN tbl_kaya_drivers b JOIN tbl_kaya_trucks c ON a.driver_id = b.id AND a.truck_id = c.id'
            )
        );
        $truckslisting = trucks::ORDERBY('truck_no', 'ASC')->GET();
        $driverslisting = drivers::ORDERBY('driver_first_name', 'ASC')->GET();
        $recid = truckDriver::findOrFail($id);
        return view('transportation.truck-driver', compact('driverTruckPairing', 'truckslisting', 'driverslisting', 'recid'));
    }

    public function update(Request $request, $id) {
        $checker = truckDriver::WHERE('truck_id', $request->truck_id)->WHERE('driver_id', $request->driver_id)->WHERE('id', '<>', $id)->exists();
        if($checker) {
            return 'exist';
        } else{
            $recid = truckDriver::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }

}
