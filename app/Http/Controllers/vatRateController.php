<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\HttpResponse;
use App\vatRate;
use App\client;
use Illuminate\Support\Facades\DB;

class vatRateController extends Controller
{
    public function index() {
        $vatRates = DB::SELECT(
            DB::RAW(
                'SELECT a.id, withholding_tax, vat_rate, company_name FROM tbl_kaya_vat_rates a JOIN tbl_kaya_clients b ON a.client_id = b.id'
            )
        );
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        return view('vat-rate.create', compact('vatRates', 'clients'));
    }

    public function store(Request $request) {
        $validateData = $request->validate([
            'client_id' => 'required',
            'withholding_tax' => 'required',
            'vat_rate' => 'required'
        ]);
        $checkClient = vatRate::WHERE('client_id', $request->client_id)->exists();
        if($checkClient) {
            return 'exists';
        }
        $createRecord = vatRate::firstOrNew([
            'client_id' => $request->client_id,
            'withholding_tax' => $request->withholding_tax, 
            'vat_rate' => $request->vat_rate
        ]);
        $createRecord->save();
        return 'saved';
    }

    public function edit($id) {
        $recid = vatRate::findOrFail($id);
        $vatRates = DB::SELECT(
            DB::RAW(
                'SELECT a.id, withholding_tax, vat_rate, company_name FROM tbl_kaya_vat_rates a JOIN tbl_kaya_clients b ON a.client_id = b.id'
            )
        );
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        return view('vat-rate.create', compact('vatRates', 'clients', 'recid'));
    }

    public function update(Request $request, $id) {
        $validateData = $request->validate([
            'client_id' => 'required',
            'withholding_tax' => 'required',
            'vat_rate' => 'required'
        ]);
        $checkClient = vatRate::WHERE('client_id', $request->client_id)->WHERE('id', '<>', $request->id)->exists();
        if($checkClient) {
            return 'exists';
        }
        $recid = vatRate::findOrFail($request->id);
        $recid->client_id = $request->client_id;
        $recid->withholding_tax = $request->withholding_tax;
        $recid->vat_rate = $request->vat_rate;
        $recid->save();
        return 'updated';
    }
}
