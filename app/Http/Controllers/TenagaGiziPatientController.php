<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Carbon\Carbon;

class TenagaGiziPatientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:tenaga-gizi']);
    }

    /**
     * Menampilkan daftar pasien untuk tenaga gizi.
     */
    public function index(Request $request)
    {
        $selectedDate = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        // Filter pasien aktif yang dibuat sebelum atau pada tanggal yang dipilih
        $patients = Patient::where('status_pasien', 'aktif')
            ->whereDate('created_at', '<=', $selectedDate)
            ->get();

        return view('tenaga-gizi.patients.index', compact('patients', 'selectedDate'));
    }
    public function show(Patient $patients)
    {
        // PERHATIAN: View Path harus disesuaikan!
        return view('tenaga-gizi.patients.show', compact('patients'));
    }
    public function filterByDate(Request $request)
    {
        $date = $request->query('date');
    
        if (!$date) {
            return response()->json(['error' => 'Tanggal tidak valid'], 400);
        }
    
        try {
            $carbonDate = Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            \Log::error("Format tanggal tidak valid: " . $date);
            return response()->json(['error' => 'Format tanggal tidak valid'], 400);
        }
    
        \Log::info("Mencari data pasien untuk tanggal: " . $carbonDate);
    
        try {
            $patients = Patient::where('status_pasien', 'aktif')
                ->whereDate('created_at', '<=', $carbonDate)
                ->get();
    
            \Log::info("Jumlah pasien ditemukan: " . $patients->count());
    
            return response()->json($patients);
        } catch (\Exception $e) {
            \Log::error("Gagal mengambil data pasien: " . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan di server'], 500);
        }
    }
}