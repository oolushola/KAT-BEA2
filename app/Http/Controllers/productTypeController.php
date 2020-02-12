<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\productCategoryRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\productcategory;

class productTypeController extends Controller
{
    public function index() {
        $product_categories = productcategory::ORDERBY('product_category')->PAGINATE('15');
        return view('products.product-category',
            compact(
                'product_categories'
            )
        );
    }

    public function store(productCategoryRequest $request) {
        $check = productcategory::WHERE('product_category_code', $request->product_category_code)->exists();
        if($check) {
            return 'exists';
        }
        else {
            productcategory::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $recid = productcategory::findOrFail($id);
        $product_categories = productcategory::ORDERBY('product_category')->PAGINATE('15');
        return view('products.product-category',
            compact(
                'product_categories',
                'recid'
            )
        );
    }

    public function update(productCategoryRequest $request, $id) {
        $check = productcategory::WHERE('product_category_code', $request->product_category_code)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = productcategory::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }

}


