<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class hrController extends Controller
{
    public function dashboard() {
        $staffs = User::GET();
        return view('hr.dashboard');
    }
}
