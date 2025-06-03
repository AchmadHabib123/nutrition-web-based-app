<?php

namespace App\Http\Controllers;

use App\Models\FoodConsumption;
use App\Models\Patient;
use App\Models\Menu;
use Illuminate\Http\Request;

class FoodConsumptionController extends Controller
{
    /**
     * Konstruktor untuk menerapkan middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:ahli-gizi']);
    }

    /**
     * Menyimpan konsumsi makanan baru.
     */
    // public function store(Request $request, $patients_id)
    // {
    //     $patients = Patient::findOrFail($patients_id);

    //     // Validasi data
    //     $request->validate([
    //         'nama_makanan' => 'required|string|max:255',
    //         'kalori' => 'required|integer|min:0',
    //     ]);

    //     // Tambah konsumsi makanan
    //     $patients->foodConsumptions()->create([
    //         'nama_makanan' => $request->nama_makanan,
    //         'kalori' => $request->kalori,
    //     ]);

    //     // Update kalori_makanan dan kalori_harian
    //     $this->updatePatientCalories($patients);

    //     return redirect()->route('ahli-gizi.patients.edit', $patients_id)->with('success', 'Makanan berhasil ditambahkan.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'menu_id' => 'required|exists:menus,id',
            'waktu_makan' => 'required|in:pagi,siang,malam',
            'tanggal' => 'required|date',
        ]);

        FoodConsumption::create([
            'patient_id' => $request->patient_id,
            'menu_id' => $request->menu_id,
            'waktu_makan' => $request->waktu_makan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('ahli-gizi.dashboard')->with('success', 'Konsumsi makanan berhasil disimpan.');
    }

    /**
     * Menampilkan form edit konsumsi makanan.
     */
    public function edit($patients_id, $food_id)
    {
        $patients = Patient::findOrFail($patients_id);
        $food = $patients->foodConsumptions()->findOrFail($food_id);

        return view('ahli-gizi.food_consumptions.edit', compact('patient', 'food'));
    }

    /**
     * Memperbarui konsumsi makanan.
     */
    public function update(Request $request, $patients_id, $food_id)
    {
        $patients = Patient::findOrFail($patients_id);
        $food = $patients->foodConsumptions()->findOrFail($food_id);

        // Validasi data
        $request->validate([
            'nama_makanan' => 'required|string|max:255',
            'kalori' => 'required|integer|min:0',
        ]);

        // Update konsumsi makanan
        $food->update([
            'nama_makanan' => $request->nama_makanan,
            'kalori' => $request->kalori,
        ]);

        // Update kalori_makanan dan kalori_harian
        $this->updatePatientCalories($patients);

        return redirect()->route('ahli-gizi.patients.edit', $patients_id)->with('success', 'Makanan berhasil diperbarui.');
    }

    /**
     * Menghapus konsumsi makanan.
     */
    public function destroy($patients_id, $food_id)
    {
        $patients = Patient::findOrFail($patients_id);
        $food = $patients->foodConsumptions()->findOrFail($food_id);

        // Hapus konsumsi makanan
        $food->delete();

        // Update kalori_makanan dan kalori_harian
        $this->updatePatientCalories($patients);

        return redirect()->route('ahli-gizi.patients.edit', $patients_id)->with('success', 'Makanan berhasil dihapus.');
    }

    /**
     * Memperbarui kalori_makanan dan kalori_harian pasien.
     */
    private function updatePatientCalories(Patient $patients)
    {
        // Hitung total kalori makanan
        $total_kalori_makanan = $patients->foodConsumptions()->sum('kalori');

        // Hitung BMR
        $bmr = $this->calculateBMR($patients);

        // Hitung kalori harian
        $kalori_harian = $bmr + $this->adjustCaloriesBasedOnDisease($patients->riwayat_penyakit);

        // Update pasien
        $patients->update([
            'kalori_makanan' => $total_kalori_makanan,
            'kalori_harian' => $kalori_harian,
        ]);
    }


    /**
     * Menghitung BMR (Basal Metabolic Rate) menggunakan rumus Mifflin-St Jeor.
     */
    private function calculateBMR(Patient $patients)
    {
        $berat = $patients->berat_badan; // dalam kg
        $tinggi = $patients->tinggi_badan; // dalam cm
        $usia = $patients->usia; // dalam tahun
        $jenis_kelamin = $patients->jenis_kelamin; // 'pria' atau 'wanita'

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
        return $adjustment;
    }

    public function create()
    {
        // ambil data pasien dan menu untuk pilihan dropdown
        $patients = Patient::all();
        $menus = Menu::all();

        return view('ahli-gizi.food_consumptions.create', compact('patients', 'menus'));
    }

}
