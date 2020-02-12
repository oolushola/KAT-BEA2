<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\client;
use App\cargoAvailability;
use Illuminate\Support\Facades\DB;


class cargoAvailabilityController extends Controller
{
    public function index() {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $cargoAvailable = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name FROM tbl_kaya_cargo_availabilities a JOIN tbl_kaya_clients b ON a.client_id = b.id '
            )
        );
        return view('cargo-availability.create', compact('clients', 'cargoAvailable'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'client_id' => 'required | integer',
            'available_order' => 'required'
        ]);

        $cargoAvailability = cargoAvailability::firstOrNew(['current_year' => $request->current_year, 'current_month' => $request->current_month, 'client_id' => $request->client_id]);
        $cargoAvailability->available_order = $request->available_order;
        $cargoAvailability->save();
        return 'saved';
    }

    public function edit($id) {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $cargoAvailable = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.company_name FROM tbl_kaya_cargo_availabilities a JOIN tbl_kaya_clients b ON a.client_id = b.id '
            )
        );
        $recid = cargoAvailability::findOrFail($id);
        return view('cargo-availability.create', compact('clients', 'cargoAvailable', 'recid'));
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'client_id' => 'required | integer',
            'available_order' => 'required'
        ]);

        $checker = cargoAvailability::WHERE('client_id', $request->client_id)->WHERE('current_month', $request->current_month)->WHERE('current_year', $request->current_year)->WHERE('id', '<>', $id)->exists();
        if($checker) {
            return 'exists';
        } else {
            $recid = cargoAvailability::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }
}
