<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\target;

class targetController extends Controller
{
    public function index() {
        $monthlytargets = target::WHERE('current_year', date('Y'))->ORDERBY('current_year', 'DESC')->GET();
        return view('kaya-target.create', compact('monthlytargets'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'target' => 'required',
        ]);

        $check = target::WHERE('current_year', $request->current_year)->WHERE('current_month', $request->current_month)->exists();
        if($check) {
            return 'exists';
        }
        else {
            target::CREATE($request->all());
            return 'saved';
        }
    }

    public function edit($id) {
        $monthlytargets = target::WHERE('current_year', date('Y'))->ORDERBY('current_year', 'DESC')->GET();
        $recid = target::findOrFail($id);
        return view('kaya-target.create', compact('monthlytargets', 'recid'));
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'target' => 'required',
        ]);

        $check = target::WHERE('current_year', $request->current_year)->WHERE('current_month', $request->current_month)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        } else {
            $recid = target::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }

        
    }
}
