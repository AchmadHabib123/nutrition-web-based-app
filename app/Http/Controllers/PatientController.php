<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StandarDiit; // <--- TAMBAHKAN INI
use App\Models\DietKhusus;
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
        $standarDiits = StandarDiit::all(); // Ambil semua Standar Diit
        $dietKhusus = DietKhusus::all(); // Ambil semua Diet Khusus
        return view('ahli-gizi.patients.create', compact('standarDiits', 'dietKhusus'));
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
            // 'kalori_makanan' => 'required|integer|min:0',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'usia' => 'required|integer|min:0',
            'jenis_kelamin' => 'required|in:pria,wanita',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal', // Tambahkan validasi tipe_pasien
            'kondisi_diet_klinis' => 'nullable|string|max:255', // <--- TAMBAHKAN VALIDASI INI
            'standar_diit_id' => 'nullable|exists:standar_diits,id', // <--- TAMBAHKAN VALIDASI INI
            'diet_khusus_ids' => 'nullable|array', // <--- TAMBAHKAN VALIDASI INI
            'diet_khusus_ids.*' => 'exists:diet_khusus,id',
            // 'status_pasien' => 'aktif',
        ]);

        // Hitung kalori harian berdasarkan BMR dan riwayat penyakit
        // Anda dapat menyesuaikan perhitungan ini sesuai kebutuhan
        $bmr = $this->calculateBMR($request);
        $kalori_harian = $bmr + $this->adjustCaloriesBasedOnDisease($request->riwayat_penyakit);

        // Buat pasien baru
        $patient = Patient::create([
            'no_kamar' => $request->no_kamar,
            'nama_pasien' => $request->nama_pasien,
            'riwayat_penyakit' => $request->riwayat_penyakit,
            // 'kalori_makanan' => $request->kalori_makanan,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'usia' => $request->usia,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tipe_pasien' => $request->tipe_pasien,
            'kondisi_diet_klinis' => $request->kondisi_diet_klinis, // <--- TAMBAHKAN INI
            'standar_diit_id' => $request->standar_diit_id,
            'kalori_harian' => $kalori_harian,
            'status_pasien' => 'aktif',

        ]);
        $patient->dietKhusus()->sync($request->input('diet_khusus_ids', []));
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
        $standarDiits = StandarDiit::all(); // Ambil semua Standar Diit
        $dietKhusus = DietKhusus::all(); // Ambil semua Diet Khusus
        // Ambil ID Diet Khusus yang sudah terpilih untuk pasien ini
        $selectedDietKhususIds = $patients->dietKhusus->pluck('id')->toArray();
        return view('ahli-gizi.patients.edit', compact('patients', 'standarDiits', 'dietKhusus', 'selectedDietKhususIds'));
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
            'kondisi_diet_klinis' => 'nullable|string|max:255', // <--- TAMBAHKAN VALIDASI INI
            'standar_diit_id' => 'nullable|exists:standar_diits,id', // <--- TAMBAHKAN VALIDASI INI
            'diet_khusus_ids' => 'nullable|array', // <--- TAMBAHKAN VALIDASI INI
            'diet_khusus_ids.*' => 'exists:diet_khusus,id',
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
            'kondisi_diet_klinis' => $request->kondisi_diet_klinis, // <--- TAMBAHKAN INI
            'standar_diit_id' => $request->standar_diit_id,
            'kalori_harian' => $kalori_harian,
            'status_pasien' => $request->status_pasien,

        ]);
        $patients->dietKhusus()->sync($request->input('diet_khusus_ids', []));
        return redirect()->route('ahli-gizi.patients.index')->with('success', 'Data pasien berhasil diperbarui.');
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
    public function getFoodConsumptionMenuDetails(FoodConsumption $foodConsumption)
    {
        // Pastikan user adalah ahli-gizi dan memiliki akses melihat konsumsi ini
        // Meskipun tidak ada Policy spesifik untuk ini, kita bisa menggunakan Policy view
        $this->authorize('view', $foodConsumption); // Asumsi policy 'view' ada

        // Eager load menu dan bahanMakanans dari menu tersebut
        $foodConsumption->load(['menu.bahanMakanans']);

        if (!$foodConsumption->menu) {
            Log::warning("Menu not found for FoodConsumption ID: {$foodConsumption->id}");
            return response()->json(['message' => 'Detail menu tidak ditemukan untuk konsumsi ini.'], 404);
        }

        $menu = $foodConsumption->menu;
        $bahanMakanans = [];

        foreach ($menu->bahanMakanans as $bahan) {
            // Hitung kalori per 100g dari bahan makanan
            // Kolom 'protein', 'karbohidrat', 'total_lemak' di BahanMakanan diasumsikan per 100g
            $kaloriPer100gBahan = ($bahan->protein * 4) + ($bahan->karbohidrat * 4) + ($bahan->total_lemak * 9);

            // Hitung kontribusi nutrisi bahan makanan ini ke menu asli
            $quantityInMenu = $bahan->pivot->jumlah; // Jumlah dalam gram bahan di 1 porsi menu
            $contributionFactor = $quantityInMenu / 100; // Karena nutrisi bahan makanan per 100g

            $bahanMakanans[] = [
                'id' => $bahan->id,
                'nama' => $bahan->nama,
                'jumlah_di_menu_gram' => (float)$quantityInMenu,
                'kalori_kontribusi_awal' => round($kaloriPer100gBahan * $contributionFactor, 2),
                'protein_kontribusi_awal' => round($bahan->protein * $contributionFactor, 2),
                'karbohidrat_kontribusi_awal' => round($bahan->karbohidrat * $contributionFactor, 2),
                'lemak_kontribusi_awal' => round($bahan->total_lemak * $contributionFactor, 2),
                // Data nutrisi per 100g juga bisa dikirim untuk perhitungan frontend
                'protein_per_100g' => (float)$bahan->protein,
                'karbohidrat_per_100g' => (float)$bahan->karbohidrat,
                'lemak_per_100g' => (float)$bahan->total_lemak,
                'kalori_per_100g' => round($kaloriPer100gBahan, 2),
            ];
        }

        return response()->json([
            'menu_details' => [
                'nama_menu' => $menu->nama,
                'original_kalori' => $menu->kalori,
                'original_protein' => $menu->total_protein,
                'original_karbohidrat' => $menu->total_karbohidrat,
                'original_lemak' => $menu->total_lemak,
                'bahan_makanans' => $bahanMakanans,
            ],
            'food_consumption_id' => $foodConsumption->id,
        ]);
    }
    public function validateConsumption(Request $request, FoodConsumption $foodConsumption)
    {
        Log::info('DEBUG: validateConsumption START for ID: ' . $foodConsumption->id);
        Log::info('DEBUG: Request data received: ' . json_encode($request->all()));

        try {
            // Pastikan otorisasi benar
            $this->authorize('markAsConsumed', $foodConsumption);
            Log::info('DEBUG: Authorization successful.');

            // Validasi input
            $request->validate([
                'final_status' => 'required|in:consumed,skipped', // Perbaikan: hanya consumed/skipped sebagai final_status
                'actual_kalori' => 'required|numeric|min:0',
                'actual_protein' => 'required|numeric|min:0',
                'actual_karbohidrat' => 'required|numeric|min:0',
                'actual_lemak' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:500',
                'bahan_consumptions' => 'required|array', // Pastikan ini ada dan array
                'bahan_consumptions.*.bahan_id' => 'required|integer|exists:bahan_makanans,id', // Validasi setiap item bahan
                'bahan_consumptions.*.status' => 'required|in:consumed_full,partial,skipped',
                'bahan_consumptions.*.sisa_gram' => 'nullable|numeric|min:0',
            ]);
            Log::info('DEBUG: Request validation passed.');

            // Lakukan update
            $foodConsumption->update([
                'status' => $request->input('final_status'), // Menggunakan final_status dari request
                'actual_kalori' => $request->input('actual_kalori'),
                'actual_protein' => $request->input('actual_protein'),
                'actual_karbohidrat' => $request->input('actual_karbohidrat'),
                'actual_lemak' => $request->input('actual_lemak'),
                // consumption_percentage tidak dikirim dari JS, jadi bisa null atau dihitung di sini
                // 'consumption_percentage' => (original_kalori > 0) ? ($request->input('actual_kalori') / original_kalori * 100) : 0,
                'notes' => $request->input('notes'),
            ]);
            Log::info('DEBUG: FoodConsumption updated successfully.');

            // Opsional: Jika Anda perlu menyimpan detail konsumsi bahan per bahan,
            // ini adalah tempat untuk itu (mungkin di tabel terpisah).
            // Contoh: foreach ($request->input('bahan_consumptions') as $bahanData) { ... }

            return response()->json(['message' => 'Validasi konsumsi berhasil disimpan.', 'food_consumption' => $foodConsumption], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('DEBUG: Validation Error for FoodConsumption ID ' . $foodConsumption->id . ': ' . json_encode($e->errors()));
            return response()->json(['message' => 'Data validasi tidak valid.', 'errors' => $e->errors()], 422);
        } catch (AuthorizationException $e) {
            Log::warning('DEBUG: Authorization Failed for FoodConsumption ID ' . $foodConsumption->id . ': ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            Log::error('ERROR: Uncaught Exception in validateConsumption for ID ' . $foodConsumption->id . ': ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan di server: ' . $e->getMessage()], 500);
        }
    }
}
