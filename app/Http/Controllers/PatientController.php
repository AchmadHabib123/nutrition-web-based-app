<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\FoodConsumption;
use App\Models\JadwalMakanan; // Pastikan ini diimpor
use App\Models\Menu; // Pastikan ini diimpor
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    // Konstruktor untuk menerapkan middleware
    public function __construct()
    {
        $this->middleware(['auth', 'role:ahli-gizi']);
    }

    /**
     * Menampilkan daftar pasien.
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

        return view('ahli-gizi.patients.index', compact('patients', 'selectedDate'));
    }

    /**
     * Menampilkan form untuk menambahkan pasien baru.
     */
    public function create()
    {
        return view('ahli-gizi.patients.create');
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

        return redirect()->route('ahli-gizi.patients.index')->with('success', 'Pasien baru berhasil ditambahkan.');
    }

    public function show(Patient $patients, Request $request) // <-- Ubah $patient menjadi $patients, tambahkan Request
    {
        // Ambil parameter tanggal dari URL (dari dashboard)
        $date = $request->query('date', Carbon::today()->toDateString()); // Default hari ini jika tidak ada query 'date'

        // Load konsumsi makanan untuk pasien ini ($patients->id) pada tanggal yang dipilih
        // Ini akan menampilkan semua status (planned, delivered, consumed, skipped) untuk tanggal tersebut
        $foodConsumptionsForDate = FoodConsumption::where('patient_id', $patients->id) // <-- Gunakan $patients->id
            ->whereDate('tanggal', $date)
            ->with('menu') // Eager load menu untuk akses makro
            ->orderBy('waktu_makan')
            ->get();

        // Hitung ringkasan konsumsi untuk tampilan detail (opsional)
        $consumedToday = $foodConsumptionsForDate->where('status', 'consumed');
        $deliveredToday = $foodConsumptionsForDate->where('status', 'delivered');
        $plannedToday = $foodConsumptionsForDate->where('status', 'planned');
        $skippedToday = $foodConsumptionsForDate->where('status', 'skipped'); // Asumsi status skipped ada

        $totalConsumedCaloriesToday = $consumedToday->sum(fn($fc) => $fc->menu ? $fc->menu->kalori : $fc->kalori);
        // Anda bisa tambahkan total makro terkonsumsi di sini juga jika diperlukan di halaman show

        // Pass $patients (model pasien), $foodConsumptionsForDate, $date, dan ringkasan ke view
        return view('ahli-gizi.patients.show', compact('patients', 'foodConsumptionsForDate', 'date', 'totalConsumedCaloriesToday', 'plannedToday','consumedToday', 'deliveredToday', 'skippedToday'));
    }
    /**
     * Menampilkan form untuk mengedit pasien.
     */
    public function edit(Patient $patients)
    {
        return view('ahli-gizi.patients.edit', compact('patients'));
    }

    /**
     * Memperbarui data pasien.
     */
    public function update(Request $request, Patient $patients)
    {
        // Validasi data
        $request->validate([
            'no_kamar' => 'required|unique:patients,no_kamar,' . $patients->id,
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
        $patients->update([
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

        return redirect()->route('ahli-gizi.patients.index')->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Menghitung BMR (Basal Metabolic Rate) menggunakan rumus Mifflin-St Jeor.
     */
    // public function filterByDate(Request $request)
    // {
    //     $date = $request->query('date');

    //     if (!$date) {
    //         return response()->json(['error' => 'Tanggal tidak valid'], 400);
    //     }

    //     try {
    //         $carbonDate = Carbon::parse($date)->toDateString();
    //     } catch (\Exception $e) {
    //         Log::error("Format tanggal tidak valid: " . $date);
    //         return response()->json(['error' => 'Format tanggal tidak valid'], 400);
    //     }

    //     Log::info("Mencari data pasien untuk tanggal: " . $carbonDate);

    //     try {
    //         $patients = Patient::where('status_pasien', 'aktif')
    //             ->whereDate('created_at', '<=', $carbonDate) // Ini akan memfilter pasien berdasarkan tanggal dibuat, bukan jadwal makan
    //             ->get();

    //         // **TAMBAHKAN LOGIKA INI UNTUK MENGHITUNG KALORI TERJADWAL**
    //         $responseData = [];
    //         $totalScheduledCalories = 0;
    //         $totalScheduledProtein = 0;
    //         $totalScheduledCarbs = 0;
    //         $totalScheduledFat = 0;
    //         $totalTargetCalories = $patients->sum('kalori_harian');

    //         foreach ($patients as $patient) {
    //             $patientScheduledCalories = 0;
    //             $patientScheduledProtein = 0;
    //             $patientScheduledCarbs = 0;
    //             $patientScheduledFat = 0;

    //             // Ambil jadwal makanan yang berlaku untuk tanggal yang diminta dan sesuai dengan tipe pasien
    //             $jadwalMakanans = JadwalMakanan::where('tipe_pasien', $patient->tipe_pasien)
    //                 ->whereDate('tanggal_mulai', '<=', $carbonDate)
    //                 ->whereDate('tanggal_selesai', '>=', $carbonDate)
    //                 ->with(['menus' => function($query) use ($carbonDate) {
    //                     // Hanya ambil menu yang dijadwalkan untuk tanggal yang diminta
    //                     $query->wherePivot('tanggal', $carbonDate);
    //                 }])
    //                 ->get();

    //             foreach ($jadwalMakanans as $jadwal) {
    //                 foreach ($jadwal->menus as $menu) {
    //                     $patientScheduledCalories += $menu->kalori;
    //                     // Pastikan kolom ini ada di tabel 'menus'
    //                     $patientScheduledProtein += $menu->total_protein ?? 0;
    //                     $patientScheduledCarbs += $menu->total_karbohidrat ?? 0;
    //                     $patientScheduledFat += $menu->total_lemak ?? 0;
    //                 }
    //             }

    //             // Tambahkan properti ke objek pasien yang akan dikembalikan
    //             $patient->kalori_makanan_hari_ini = $patientScheduledCalories;
    //             $patient->protein_makanan_hari_ini = $patientScheduledProtein;
    //             $patient->karbohidrat_makanan_hari_ini = $patientScheduledCarbs;
    //             $patient->lemak_makanan_hari_ini = $patientScheduledFat;

    //             // Akumulasikan total untuk dashboard (semua pasien)
    //             $totalScheduledCalories += $patientScheduledCalories;
    //             $totalScheduledProtein += $patientScheduledProtein;
    //             $totalScheduledCarbs += $patientScheduledCarbs;
    //             $totalScheduledFat += $patientScheduledFat;

    //             $responseData[] = $patient->toArray();
    //         }

    //         // Mengembalikan respons dengan data pasien DAN data agregat untuk dashboard
    //         return response()->json([
    //             'patients' => $responseData,
    //             'summary' => [
    //                 'total_scheduled_calories' => $totalScheduledCalories,
    //                 'total_target_calories' => $totalTargetCalories, // Hanya total kalori target yang ada
    //                 'total_scheduled_protein' => $totalScheduledProtein,
    //                 'total_scheduled_carbs' => $totalScheduledCarbs,
    //                 'total_scheduled_fat' => $totalScheduledFat,
    //             ]
    //         ]);
    //         // Log::info("Jumlah pasien ditemukan: " . $patients->count());

    //         // return response()->json($patients);
    //     } catch (\Exception $e) {
    //         Log::error("Gagal mengambil data pasien: " . $e->getMessage());
    //         return response()->json(['error' => 'Terjadi kesalahan di server'], 500);
    //     }
    // }
    public function filterByDate(Request $request)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json(['error' => 'Tanggal tidak valid'], 400);
        }

        try {
            $carbonDate = Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            Log::error("Format tanggal tidak valid: " . $date);
            return response()->json(['error' => 'Format tanggal tidak valid'], 400);
        }

        Log::info("Mencari data pasien untuk tanggal: " . $carbonDate);

        try {
            $patients = Patient::where('status_pasien', 'aktif')->get();

            $responseData = []; // Untuk array pasien di respons JSON
            
            // Inisialisasi total untuk dashboard (agregat dari SEMUA pasien aktif)
            $totalConsumedCalories = 0;
            $totalConsumedProtein = 0;
            $totalConsumedCarbs = 0;
            $totalConsumedFat = 0;

            // Total target kalori dashboard (sum dari kalori_harian SEMUA pasien aktif)
            $totalTargetCalories = $patients->sum('kalori_harian');

            $totalPendingValidationCount = 0; // Total item yang 'delivered' dan perlu divalidasi
            $totalPendingValidationCalories = 0; // Total kalori dari item yang 'delivered'

            foreach ($patients as $patient) {
                // Ambil semua konsumsi makanan pasien ini untuk tanggal yang diminta
                // Tanpa filter status di sini, agar bisa menghitung 'consumed' dan 'delivered'
                $foodsForToday = FoodConsumption::where('patient_id', $patient->id)
                    ->whereDate('tanggal', $carbonDate)
                    ->with('menu') // Load menu untuk mendapatkan detail makro
                    ->get();

                // Hitung total kalori dan makro yang DIKONSUMSI oleh pasien ini hari ini
                $consumedFoodsToday = $foodsForToday->where('status', 'consumed');
                $patientConsumedCalories = $consumedFoodsToday->sum(fn($food) => $food->menu ? $food->menu->kalori : $food->kalori);
                $patientConsumedProtein = $consumedFoodsToday->sum(fn($food) => $food->menu ? ($food->menu->total_protein ?? 0) : 0);
                $patientConsumedCarbs = $consumedFoodsToday->sum(fn($food) => $food->menu ? ($food->menu->total_karbohidrat ?? 0) : 0);
                $patientConsumedFat = $consumedFoodsToday->sum(fn($food) => $food->menu ? ($food->menu->total_lemak ?? 0) : 0);

                // **TAMBAHAN PENTING UNTUK INDIKATOR VALIDASI:**
                // Hitung berapa banyak makanan yang 'delivered' tapi belum 'consumed'
                $pendingValidationItems = $foodsForToday->where('status', 'delivered');
                $patientPendingValidationCount = $pendingValidationItems->count();
                $patientPendingValidationCalories = $pendingValidationItems->sum(fn($food) => $food->menu ? $food->menu->kalori : $food->kalori);

                // Tambahkan properti dinamis ke objek pasien untuk ditampilkan di tabel pasien
                $patient->kalori_makanan_hari_ini = $patientConsumedCalories; // Kalori aktual dikonsumsi
                $patient->pending_validation_count = $patientPendingValidationCount; // Jumlah yang perlu divalidasi per pasien
                $patient->pending_validation_calories = $patientPendingValidationCalories; // Total kalori yang perlu divalidasi per pasien

                // Akumulasikan ke total dashboard
                $totalConsumedCalories += $patientConsumedCalories;
                $totalConsumedProtein += $patientConsumedProtein;
                $totalConsumedCarbs += $patientConsumedCarbs;
                $totalConsumedFat += $patientConsumedFat;
                $totalPendingValidationCount += $patientPendingValidationCount;
                $totalPendingValidationCalories += $patientPendingValidationCalories;

                $responseData[] = $patient->toArray();
            }

            return response()->json([
                'patients' => $responseData,
                'summary' => [
                    'total_consumed_calories' => $totalConsumedCalories,
                    'total_target_calories' => $totalTargetCalories,
                    'total_consumed_protein' => $totalConsumedProtein,
                    'total_consumed_carbs' => $totalConsumedCarbs,
                    'total_consumed_fat' => $totalConsumedFat,
                    'total_pending_validation_count' => $totalPendingValidationCount, // Total seluruh item pending validasi
                    'total_pending_validation_calories' => $totalPendingValidationCalories, // Total kalori seluruh item pending
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal mengambil data pasien dan konsumsi: " . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['error' => 'Terjadi kesalahan di server saat memuat data.', 'details' => $e->getMessage()], 500);
        }
    }
    
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
    public function destroy(Patient $patients)
    {
        $patients->delete();
        return redirect()->route('ahli-gizi.patients.index')->with('success', 'Pasien berhasil dihapus.');
    }
}
