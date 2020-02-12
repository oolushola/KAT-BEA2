<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\driversRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\drivers;

class driversController extends Controller
{
    public function index() {
        $drivers = drivers::ORDERBY('driver_last_name', 'ASC')->GET();
        return view('transportation.drivers',
            compact(
                'drivers'
            )
        );
    }

    public function store(driversRequest $request) {
        $check = drivers::WHERE('driver_phone_number', $request->driver_phone_number)->exists();
        if($check) {
            return 'exists';
        }
        else {
            drivers::CREATE($request->all());
            return 'saved';
        } 
    }

    public function edit($id) {
        $drivers = drivers::ORDERBY('driver_last_name')->GET();
        $recid = drivers::findOrFail($id);
        return view('transportation.drivers', 
            compact(
                'drivers',
                'recid'
            )
        );
    }

    public function update(driversRequest $request, $id) {
        $check = drivers::WHERE('driver_phone_number', $request->driver_phone_number)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = drivers::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        } 
    }

    public function destroy() {

    }
}
