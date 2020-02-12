<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\companyProfile;

class companyProfileController extends Controller
{
    public function index() {
        $authorizedUsers = User::WHERE('role_id', 3)->GET();
        $companyProfileDetails = companyProfile::GET();
        return view('kaya-profile.create', compact('authorizedUsers', 'companyProfileDetails'));
    }

    public function store(Request $request) {
        
        $this->validate($request, [
            'company_name' => 'required|string',
            'company_email' => 'required|email',
            'website'   => 'required|string',
            'company_phone_no' => 'required|string',
            'address' => 'required|string',
            'company_logo' => 'required|max:1024',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_no' => 'required|string|max:10',
            'tin' => 'required|string',
            'authorized_user_id' => 'required|integer',
            'signatory' => 'required|max:1024'
        ]);
        $check = companyProfile::GET();
        if(sizeof($check)>0) {
            return 'exists';
        }
        else {
            $record = companyProfile::CREATE($request->all());
            $id =  $record->id;
            $recid = companyProfile::findOrFail($id);;
            if($request->hasFile('company_logo')) {
                $company_logo = $request->file('company_logo');
                $name = str_slug($recid->company_name).'.'.$company_logo->getClientOriginalExtension();
                $destination_path = public_path('assets/img/kaya/');
                $companyLogoPath = $destination_path."/".$name;
                $company_logo->move($destination_path, $name);
                $recid->company_logo = $name;
                $recid->save();
            }

            if($request->hasFile('signatory')) {
                $signatory = $request->file('signatory');
                $name = $recid->authorized_user_id.'.'.$signatory->getClientOriginalExtension();
                $destination_path = public_path('assets/img/kaya/');
                $signatoryPath = $destination_path."/".$name;
                $signatory->move($destination_path, $name);
                $recid->signatory = $name;
                $recid->save();
            }

            return 'saved';
            
        }

    }

    

    public function edit($id) {
        $authorizedUsers = User::WHERE('role_id', 3)->GET();
        $companyProfileDetails = companyProfile::GET();
        $recid = companyProfile::findOrFail($id);
        return view('kaya-profile.create', compact('authorizedUsers', 'companyProfileDetails', 'recid'));
    }

    public function update(Request $request, $id) {

    }
}
