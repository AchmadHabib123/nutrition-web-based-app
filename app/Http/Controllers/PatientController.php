<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientController extends Controller
{
    // Konstruktor untuk menerapkan middleware
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Menampilkan daftar pasien.
     */
    public function index(Request $request)
    {
        // $patients = Patient::where('status_pasien', 'aktif')->get();
        // return view('admin.patient.index', compact('patients'));
        $selectedDate = $request->input('tanggal') 
        ? Carbon::parse($request->input('tanggal')) 
        : Carbon::today();

        // Filter pasien aktif yang dibuat sebelum atau pada tanggal yang dipilih
        $patients = Patient::where('status_pasien', 'aktif')
            ->whereDate('created_at', '<=', $selectedDate)
            ->get();

        return view('admin.patients.index', compact('patients', 'selectedDate'));
    }

    /**
     * Menampilkan form untuk menambahkan pasien baru.
     */
    public function create()
    {
        return view('admin.patient.create');
    }

    /**
     * Menyimpan pasien baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'no_kamar' => 'required|unique:patients,no_kamar',
            'nama_pasien' => 'required|string|max:255',
            'riwayat_penyakit' => 'required|string',
            'kalori_makanan' => 'required|integer|min:0',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'usia' => 'required|integer|min:0',
            'jenis_kelamin' => 'required|in:pria,wanita',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal', // Tambahkan validasi tipe_pasien
            'status_pasien' => 'aktif',
        ]);

        // Hitung kalori harian berdasarkan BMR dan riwayat penyakit
        // Anda dapat menyesuaikan perhitungan ini sesuai kebutuhan
        $bmr = $this->calculateBMR($request);
        $kalori_harian = $bmr + $this->adjustCaloriesBasedOnDisease($request->riwayat_penyakit);

        // Buat pasien baru
        Patient::create([
            'no_kamar' => $request->no_kamar,
            'nama_pasien' => $request->nama_pasien,
            'riwayat_penyakit' => $request->riwayat_penyakit,
            'kalori_makanan' => $request->kalori_makanan,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'usia' => $request->usia,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tipe_pasien' => $request->tipe_pasien,
            'kalori_harian' => $kalori_harian,
        ]);

        return redirect()->route('admin.patients.index')->with('success', 'Pasien baru berhasil ditambahkan.');
    }

    public function show(Patient $patient)
    {
        return view('admin.patient.show', compact('patient'));
    }
    /**
     * Menampilkan form untuk mengedit pasien.
     */
    public function edit(Patient $patient)
    {
        return view('admin.patient.edit', compact('patient'));
    }

    /**
     * Memperbarui data pasien.
     */
    public function update(Request $request, Patient $patient)
    {
        // Validasi data
        $request->validate([
            'no_kamar' => 'required|unique:patients,no_kamar,' . $patient->id,
            'nama_pasien' => 'required|string|max:255',
            'riwayat_penyakit' => 'required|string',
            'kalori_makanan' => 'required|integer|min:0',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'usia' => 'required|integer|min:0',
            'jenis_kelamin' => 'required|in:pria,wanita',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal', // Tambahkan validasi tipe_pasien
            'status_pasien' => 'required|in:aktif,nonaktif',
        ]);

        // Hitung kalori harian berdasarkan BMR dan riwayat penyakit
        $bmr = $this->calculateBMR($request);
        $kalori_harian = $bmr + $this->adjustCaloriesBasedOnDisease($request->riwayat_penyakit);

        // Update data pasien
        $patient->update([
            'no_kamar' => $request->no_kamar,
            'nama_pasien' => $request->nama_pasien,
            'riwayat_penyakit' => $request->riwayat_penyakit,
            'kalori_makanan' => $request->kalori_makanan,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'usia' => $request->usia,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tipe_pasien' => $request->tipe_pasien,
            'kalori_harian' => $kalori_harian,
            'status_pasien' => $request->status_pasien,

        ]);

        return redirect()->route('admin.patients.index')->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function filterByDate(Request $request)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json(['error' => 'Tanggal tidak valid'], 400);
        }

        $carbonDate = Carbon::parse($date)->toDateString();
        
        // DEBUG: Cek apakah tanggal sudah dikonversi dengan benar
        \Log::info("Mencari data pasien untuk tanggal: " . $carbonDate);

        $patients = Patient::whereRaw("DATE(created_at) = ?", [$carbonDate])
            ->orWhereRaw("DATE(updated_at) = ?", [$carbonDate])
            ->get();
        // DEBUG: Cek apakah data ditemukan
        if ($patients->isEmpty()) {
            \Log::info("Tidak ada pasien ditemukan untuk tanggal: " . $carbonDate);
        }

        return response()->json($patients);
    }

    /**
     * Menghitung BMR (Basal Metabolic Rate) menggunakan rumus Mifflin-St Jeor.
     */
    private function calculateBMR(Request $request)
    {
        $berat = $request->berat_badan; // dalam kg
        $tinggi = $request->tinggi_badan; // dalam cm
        $usia = $request->usia; // dalam tahun
        $jenis_kelamin = $request->jenis_kelamin; // 'pria' atau 'wanita'

        if ($jenis_kelamin === 'pria') {
            // Rumus Mifflin-St Jeor untuk pria
            $bmr = (10 * $berat) + (6.25 * $tinggi) - (5 * $usia) + 5;
        } else {
            // Rumus Mifflin-St Jeor untuk wanita
            $bmr = (10 * $berat) + (6.25 * $tinggi) - (5 * $usia) - 161;
        }

        return round($bmr);
    }


    /**
     * Menyesuaikan kalori harian berdasarkan riwayat penyakit.
     */
    private function adjustCaloriesBasedOnDisease($riwayat_penyakit)
    {
        // Contoh penyesuaian:
        // - Diabetes: -200 kalori
        // - Hipertensi: -100 kalori
        // - Tidak ada penyakit: 0 kalori

        $adjustment = 0;

        if (stripos($riwayat_penyakit, 'diabetes') !== false) {
            $adjustment -= 200;
        }

        if (stripos($riwayat_penyakit, 'hipertensi') !== false) {
            $adjustment -= 100;
        }

        // Tambahkan kondisi lainnya sesuai kebutuhan

        return $adjustment;
    }

    /**
     * Menghapus pasien.
     * (Opsional, jika Anda ingin menyediakan fitur penghapusan pasien)
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('admin.patients.index')->with('success', 'Pasien berhasil dihapus.');
    }
}
