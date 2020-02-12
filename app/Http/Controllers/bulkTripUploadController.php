<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class bulkTripUploadController extends Controller
{
    public function store(request $request) {
        return $request->all();
    }
}
