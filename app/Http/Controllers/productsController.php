<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\productRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\product;

class productsController extends Controller
{
    public function index() {
        $products = product::ORDERBY('product', 'ASC')->PAGINATE(10);
        return view('products.product', 
            compact(
                'products'
            )
        );
    }

    public function store(productRequest $request) {
        $check = product::WHERE('product_code', $request->product_code)->exists();
        if($check) {
            return 'exists';
        }
        else {
            product::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $products = product::ORDERBY('product', 'ASC')->PAGINATE(10);
        $recid = product::findOrFail($id);
        return view('products.product', 
            compact(
                'products',
                'recid'
            )
        );
    }

    public function update(productRequest $request, $id) {
        $check = product::WHERE('product_code', $request->product_code)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = product::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }
}
