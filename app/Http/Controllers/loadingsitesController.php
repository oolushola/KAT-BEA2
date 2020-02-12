<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\loadingSiteRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\loadingSite;

class loadingsitesController extends Controller
{
    public function index() {
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'));
        $loadingSites = loadingSite::ORDERBY('loading_site', 'ASC')->PAGINATE(15);
        return view('loading-sites.create',
            compact(
                'states',
                'loadingSites'
            )
        );
    }

    public function store(loadingSiteRequest $request) {
        $check = loadingSite::WHERE('state_domiciled', $request->state_domiciled)->WHERE('loading_site_code', $request->loading_site_code)->exists();
        if($check) {
            return 'exists';
        }
        else {
            loadingSite::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC'));
        $loadingSites = loadingSite::ORDERBY('loading_site', 'ASC')->PAGINATE(15);
        $recid = loadingSite::findOrFail($id);
        return view('loading-sites.create',
            compact(
                'states',
                'recid',
                'loadingSites'
            )
        );
    }

    public function update(loadingSiteRequest $request, $id) {
        $check = loadingSite::WHERE('state_domiciled', $request->state_domiciled)->WHERE('loading_site_code', $request->loading_site_code)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = loadingSite::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }
}
