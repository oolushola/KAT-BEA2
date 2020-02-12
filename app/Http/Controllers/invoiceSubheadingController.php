<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\client;
use App\invoiceSubheading;


class invoiceSubheadingController extends Controller
{
    public function index(){
        $clientlistings = client::SELECT('id', 'company_name')->ORDERBY('company_name', 'ASC')->GET();
        $invoiceHeadings = DB::SELECT(
            DB::RAW(
                'SELECT a.company_name, b.* FROM tbl_kaya_clients a JOIN tbl_kaya_invoice_subheadings b ON a.id = b.client_id '
            )
        );
        return view('invoice-subheading.create', array(
            'clients' => $clientlistings,
            'invoiceHeadings' => $invoiceHeadings
        ));
    }

    public function store(Request $request){

        $check = invoiceSubheading::WHERE('client_id', $request->client_id)->exists();
        if($check){
            return 'exists';
        } else {
            invoiceSubheading::CREATE($request->all());
            return 'saved';
        }
    }
}
