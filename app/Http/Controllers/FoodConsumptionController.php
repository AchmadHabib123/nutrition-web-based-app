<?php

namespace App\Http\Controllers;

use App\Models\FoodConsumption;
use App\Models\Patient;
use Illuminate\Http\Request;

class FoodConsumptionController extends Controller
{
    /**
     * Konstruktor untuk menerapkan middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Menyimpan konsumsi makanan baru.
     */
    public function store(Request $request, $patient_id)
    {
        $patient = Patient::findOrFail($patient_id);

        // Validasi data
        $request->validate([
            'nama_makanan' => 'required|string|max:255',
            'kalori' => 'required|integer|min:0',
        ]);

        // Tambah konsumsi makanan
        $patient->foodConsumptions()->create([
            'nama_makanan' => $request->nama_makanan,
            'kalori' => $request->kalori,
        ]);

        // Update kalori_makanan dan kalori_harian
        $this->updatePatientCalories($patient);

        return redirect()->route('admin.patients.edit', $patient_id)->with('success', 'Makanan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit konsumsi makanan.
     */
    public function edit($patient_id, $food_id)
    {
        $patient = Patient::findOrFail($patient_id);
        $food = $patient->foodConsumptions()->findOrFail($food_id);

        return view('admin.food_consumptions.edit', compact('patient', 'food'));
    }

    /**
     * Memperbarui konsumsi makanan.
     */
    public function update(Request $request, $patient_id, $food_id)
    {
        $patient = Patient::findOrFail($patient_id);
        $food = $patient->foodConsumptions()->findOrFail($food_id);

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
        $this->updatePatientCalories($patient);

        return redirect()->route('admin.patients.edit', $patient_id)->with('success', 'Makanan berhasil diperbarui.');
    }

    /**
     * Menghapus konsumsi makanan.
     */
    public function destroy($patient_id, $food_id)
    {
        $patient = Patient::findOrFail($patient_id);
        $food = $patient->foodConsumptions()->findOrFail($food_id);

        // Hapus konsumsi makanan
        $food->delete();

        // Update kalori_makanan dan kalori_harian
        $this->updatePatientCalories($patient);

        return redirect()->route('admin.patients.edit', $patient_id)->with('success', 'Makanan berhasil dihapus.');
    }

    /**
     * Memperbarui kalori_makanan dan kalori_harian pasien.
     */
    private function updatePatientCalories(Patient $patient)
    {
        // Hitung total kalori makanan
        $total_kalori_makanan = $patient->foodConsumptions()->sum('kalori');

        // Hitung BMR
        $bmr = $this->calculateBMR($patient);

        // Hitung kalori harian
        $kalori_harian = $bmr + $this->adjustCaloriesBasedOnDisease($patient->riwayat_penyakit);

        // Update pasien
        $patient->update([
            'kalori_makanan' => $total_kalori_makanan,
            'kalori_harian' => $kalori_harian,
        ]);
    }


    /**
     * Menghitung BMR (Basal Metabolic Rate) menggunakan rumus Mifflin-St Jeor.
     */
    private function calculateBMR(Patient $patient)
    {
        $berat = $patient->berat_badan; // dalam kg
        $tinggi = $patient->tinggi_badan; // dalam cm
        $usia = $patient->usia; // dalam tahun
        $jenis_kelamin = $patient->jenis_kelamin; // 'pria' atau 'wanita'

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
}
