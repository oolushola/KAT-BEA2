<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\client;
use App\ClientArrangement;
use Illuminate\Support\Facades\DB;

class ClientArrangementController extends Controller
{
    public function index() {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $clientArrangements = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.id AS clientId, company_name  FROM tbl_kaya_pay_client_arrangements a JOIN tbl_kaya_clients b ON a.client_id = b.id ORDER BY company_name ASC'
            )
        );
        return view(
            'kaya-pay.setup.client-arrangement',
            compact(
                'clients',
                'clientArrangements'
            )
        );
    }

    public function store(Request $request) {
        $validateData = $request->validate([
            'client_id' => 'required | integer',
            'payback_in' => 'required | integer',
            'interest_rate' => 'required',
            'overdue_charge' => 'required' 
        ]);
        $checkClientRecord = ClientArrangement::WHERE('client_id', $request->client_id)->exists();
        if($checkClientRecord) {
            return 'exists';
        }
        ClientArrangement::CREATE($request->all());
        return 'saved';
    }

    public function edit(Request $request, $id) {
        $clients = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $recid = ClientArrangement::findOrFail($id);
        $clientArrangements = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.id AS clientId, company_name  FROM tbl_kaya_pay_client_arrangements a JOIN tbl_kaya_clients b ON a.client_id = b.id ORDER BY company_name ASC'
            )
        );
        return view(
            'kaya-pay.setup.client-arrangement',
            compact(
                'clients',
                'clientArrangements',
                'recid'
            )
        );
    }

    public function update(Request $request, $id) {
        $validateData = $request->validate([
            'client_id' => 'required | integer',
            'payback_in' => 'required | integer',
            'interest_rate' => 'required',
            'overdue_charge' => 'required' 
        ]);
        $checkClientRecord = ClientArrangement::WHERE('client_id', $request->client_id)->WHERE('id', '!=', $id)->exists();
        if($checkClientRecord) {
            return 'exists';
        }
        $clientArrangmentInfo = ClientArrangement::findOrFail($id);
        $clientArrangmentInfo->UPDATE($request->all());
        return 'updated';
    }

    public function destroy(Request $request, $id) {
        // come back later for this.
    }
}
