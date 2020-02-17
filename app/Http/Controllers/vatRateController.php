<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\HttpResponse;
use App\vatRate;

class vatRateController extends Controller
{
    public function index() {
        $vatRates = vatRate::GET();
        $vatRateRecord = vatRate::first();
        return view('vat-rate.create', compact('vatRates', 'vatRateRecord'));
    }

    public function store(Request $request) {
       
        $validateData = $request->validate([
            'withholding_tax' => 'required',
            'vat_rate' => 'required'
        ]);
        $createRecord = vatRate::firstOrNew(['withholding_tax' => $request->withholding_tax, 'vat_rate' => $request->vat_rate]);
        $createRecord->save();
        return 'saved';
    }

    public function edit() {

    }

    public function update() {

    }

    public function destroy() {

    }
}
