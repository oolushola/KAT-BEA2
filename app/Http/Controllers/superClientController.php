<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\parentClient;

use App\Http\Requests;
use App\Http\Requests\driversRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;


class superClientController extends Controller
{
    public function index() {
        $superClients = parentClient::GET();
        return view('super-client.create', compact('superClients'));
    }

    public function store(Request $request) {
        $check = parentClient::WHERE('parent_name', $request->parent_name)->exists();
        if($check) {
            return 'exists';
        }
        else{
            parentClient::CREATE($request->all());
            return back()->with('Successful.');
        }
    }
}
