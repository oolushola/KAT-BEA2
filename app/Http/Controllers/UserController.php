<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\StaffEducation;
use Auth;

class UserController extends Controller
{
    public function biodata() {
        $user = Auth::user();
        $banks = array(
            'Access Bank Plc',
            'Citibank Nigeria Limited',
            'Ecobank Nigeria Plc',
            'Fidelity Bank Plc',
            'First Bank of Nigeria Limited',
            'First City Monument Bank Limited',
            'Guaranty Trust Bank',
            'Heritage Bank',
            'Jaiz Bank Plc',
            'Keystone Bank',
            'Polaris Bank Limited',
            'Providus Bank Limited',
            'Stanbic IBTC Bank',
            'Standard Chartered',
            'Sterling Bank Plc',
            'TajBank Limited',
            'Union Bank of Nigeria Plc',
            'United Bank for Africa Plc',
            'Unity Bank Plc',
            'Wema Bank Plc',
            'Zenith Bank Plc'
        );

        return view('hr.bio-data.biodata', 
            compact(
                'user',
                'banks'
            )
        );
    }

    public function storeBiodata(Request $request) {
        $user_id = Auth::user()->id;
        $permission = User::findOrFail(Auth::user()->id);
        if($permission->date_of_appointment == NULL) {
            return 'notAllowed';
        }
        else {
            $permission->date_of_birth = $request->dateOfBirth;
            $permission->gender = $request->gender;
            $permission->address = $request->address;
            $permission->bank_name = $request->bankName;
            $permission->account_no =  $request->accountNo;
            $permission->save();

            return 'updated';
        }
    }

    public function storeUserEducation(Request $request) {
        $checker = StaffEducation::WHERE('user_id', Auth::user()->id)->WHERE('school_name', $request->school_name)->WHERE('qualification_obtained', $request->qualification_obtained)->exists();
        if($checker) {
            // update it here Please...

        }
        else {
            StaffEducation::CREATE($request->all());
            return 'saved';
        }
    }
}
