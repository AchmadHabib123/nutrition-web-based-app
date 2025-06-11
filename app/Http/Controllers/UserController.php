<?php

namespace App\Http\Controllers;
use App\Models\Patient;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {
        $patients = Patient::all();
        return view('tenaga-gizi.dashboard',compact('patients'));
    }
}
