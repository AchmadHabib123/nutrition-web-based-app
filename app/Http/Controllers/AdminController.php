<?php

namespace App\Http\Controllers;
use App\Models\Patient;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
{
    // Ambil semua data pasien
    $patients = Patient::all();

    // Kirim data pasien ke view dashboard
    return view('admin.dashboard', compact('patients'));
}
}
