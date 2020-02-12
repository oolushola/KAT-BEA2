<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;
use Auth;

class dashboardController extends Controller
{
    public function uploadProfilePhoto(Request $request) {
        $recid = User::findOrFail(base64_decode($request->user));
        if($request->hasFile('file')){
            $photo = $request->file('file');
            $name = str_slug($request->fullname).$request->user.'.'.$photo->getClientOriginalExtension();
            $destination_path = public_path('assets/img/users/');
            $profilePhotoPath = $destination_path."/".$name;
            $photo->move($destination_path, $name);
            $recid->photo = $name;
            $recid->save();
            return 'uploaded';
        }
    }

    public function changePassword(Request $request) {
        
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required_with:confirm_new_password|min:6',
            'confirm_new_password' => 'required'
        ]);
        $user = User::findOrFail(base64_decode($request->userIdentification));
        if(Hash::check($request->old_password, $user->password)) {
            $newPassword = Hash::make($request->new_password);
            $user->password = $newPassword;
            $user->save();
            return 'changed';
        }
        else{
            return 'wrongpass';
        }

    }
}
