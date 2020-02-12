<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\truckTypeRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\truckType;

class trucktypeController extends Controller
{
    public function index() {
        $trucktypes = truckType::ORDERBY('tonnage', 'ASC')->PAGINATE(15)    ;
        return view('trucks.truck-types', 
            compact(
                'trucktypes'
            )
        );
    }

    public function store(truckTypeRequest $request) {
        $check = truckType::WHERE('truck_type_code', $request->truck_type_code)->exists();
        if($check) {
            return 'exists';
        }
        else {
            trucktype::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $trucktypes = truckType::ORDERBY('tonnage', 'ASC')->PAGINATE(15);
        $recid = truckType::findOrFail($id);
        return view('trucks.truck-types', 
            compact(
                'trucktypes',
                'recid'
            )
        );
    }

    public function update(truckTypeRequest $request, $id) {
        $check = truckType::WHERE('truck_type_code', $request->truck_type_code)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = truckType::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }
}
