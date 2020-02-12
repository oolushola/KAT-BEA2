<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\loadingClerkRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\loadingClerk;

class loadingClerkContronller extends Controller
{
    public function index() {
        $states = DB::SELECT(
            DB::RAW(
                'SELECT  * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $fieldOps = loadingClerk::ORDERBY('last_name', 'ASC')->PAGINATE(15);
        return view('field-ops.loading-clerk',
            compact(
                'states',
                'fieldOps'
            )
        );
    }

    public function store(loadingClerkRequest $request) {
        $check = loadingClerk::WHERE('email', $request->email)->exists();
        if($check){
            return 'exists';
        }
        else{
            loadingClerk::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $states = DB::SELECT(
            DB::RAW(
                'SELECT  * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'
            )
        );
        $fieldOps = loadingClerk::ORDERBY('last_name', 'ASC')->PAGINATE(15);
        $recid = loadingClerk::findOrFail($id);
        return view('field-ops.loading-clerk',
            compact(
                'states',
                'fieldOps',
                'recid'
            )
        );
    }

    public function update(loadingClerkRequest $request, $id) {
        $check = loadingClerk::WHERE('email', $request->email)->WHERE('id', '!=', $id)->exists();
        if($check){
            return 'exists';
        }
        else{
            $recid = loadingClerk::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }

    public function destroy() {

    }
}
