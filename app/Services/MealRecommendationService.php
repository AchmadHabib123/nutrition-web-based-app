<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Menu;
use App\Models\SiklusMenu;
use App\Models\BahanMakanan; // Untuk memeriksa stok
use Carbon\Carbon;
use Illuminate\Support\Collection; // Untuk bekerja dengan koleksi
use Illuminate\Support\Facades\Log;

class MealRecommendationService
{
    /**
     * Merekomendasikan menu untuk slot jadwal tertentu berdasarkan pedoman.
     *
     * @param Patient $patient
     * @param string $dateString (YYYY-MM-DD)
     * @param string $waktuMakan (pagi, siang, sore, selingan_pagi, dll.)
     * @return array Rekomendasi menu dengan detail kecocokan/peringatan.
     */
    public function getRecommendedMenusForSlot(Patient $patient, string $dateString, string $waktuMakan): array
    {
        $recommendations = [];
        $carbonDate = Carbon::parse($dateString);
        $hariSiklus = (($carbonDate->day - 1) % 11) + 1; // Menghitung hari siklus (1-11)
        
        Log::info("Mencari rekomendasi untuk Pasien: {$patient->nama_pasien} (ID: {$patient->id}), Tanggal: {$dateString}, Waktu: {$waktuMakan}");
        Log::info("Hari Siklus: {$hariSiklus}, Kondisi Klinis: {$patient->kondisi_diet_klinis}, Tipe Pasien: {$patient->tipe_pasien}");

        // --- 1. Penentuan Usulan Menu Dasar dari Siklus ---
        $baseSiklusMenuEntry = SiklusMenu::where('hari_siklus', $hariSiklus)
                                         ->where('waktu_makan', $waktuMakan)
                                         ->where('tipe_pasien', $patient->tipe_pasien)
                                         ->with('menu.bahanMakanans') // Load menu dan bahan makanannya
                                         ->first();
        $baseMenu = $baseSiklusMenuEntry ? $baseSiklusMenuEntry->menu : null;
        $baseMenuSuitability = 'suitable';
        $baseMenuWarnings = [];

        if (!$baseMenu) {
            $baseMenuSuitability = 'not_found_in_cycle';
            $baseMenuWarnings[] = 'Tidak ada menu standar di Siklus Menu untuk slot ini.';
            Log::warning("Tidak ada menu dasar dari SiklusMenu untuk pasien {$patient->id} pada hari {$hariSiklus}, waktu {$waktuMakan}, tipe {$patient->tipe_pasien}");
        } else {
            // --- 2. Filter Awal (Aturan Keras / Binary Rules) ---
            list($baseMenuSuitability, $baseMenuWarnings) = $this->applyHardFilters($patient, $baseMenu, $waktuMakan);
            
            // Jika lolos filter keras, cek nutrisi dan stok
            if ($baseMenuSuitability === 'suitable') {
                // KOREKSI INI: Tambahkan $waktuMakan sebagai argumen ketiga
                list($nutrientSuitability, $nutrientWarnings) = $this->checkNutrientCompliance($patient, $baseMenu, $waktuMakan); // <--- KOREKSI DI SINI
                $baseMenuSuitability = $nutrientSuitability;
                $baseMenuWarnings = array_merge($baseMenuWarnings, $nutrientWarnings);

                list($stockSuitability, $stockWarnings) = $this->checkStockAvailability($baseMenu);
                if ($stockSuitability === 'insufficient_stock') {
                    $baseMenuSuitability = 'insufficient_stock';
                }
                $baseMenuWarnings = array_merge($baseMenuWarnings, $stockWarnings);
            }
        }
        
        // Tambahkan Usulan Menu Dasar ke rekomendasi
        if ($baseMenu) {
            $recommendations[] = [
                'menu' => $baseMenu->toArray(),
                'source' => 'siklus',
                'suitability' => $baseMenuSuitability,
                'warnings' => $baseMenuWarnings,
                'score' => $this->calculateMenuScore($patient, $baseMenu, $baseMenuSuitability)
            ];
        }

        // --- 3. Cari Menu Alternatif (jika Usulan Menu Dasar tidak cocok) ---
        if ($baseMenuSuitability !== 'suitable') {
            Log::info("Mencari alternatif karena menu dasar tidak cocok. Suitability: {$baseMenuSuitability}");
            $alternativeMenus = $this->findAlternativeMenus($patient, $waktuMakan, $baseMenu);

            foreach ($alternativeMenus as $altMenu) {
                 list($altSuitability, $altWarnings) = $this->applyHardFilters($patient, $altMenu, $waktuMakan);
                 if ($altSuitability === 'suitable') {
                    // KOREKSI INI: Tambahkan $waktuMakan sebagai argumen ketiga
                    list($nutrientAltSuitability, $nutrientAltWarnings) = $this->checkNutrientCompliance($patient, $altMenu, $waktuMakan); // <--- KOREKSI DI SINI
                    $altSuitability = $nutrientAltSuitability;
                    $altWarnings = array_merge($altWarnings, $nutrientAltWarnings);

                    list($stockAltSuitability, $stockAltWarnings) = $this->checkStockAvailability($altMenu);
                    if ($stockAltSuitability === 'insufficient_stock') {
                        $altSuitability = 'insufficient_stock';
                    }
                    $altWarnings = array_merge($altWarnings, $stockAltWarnings);
                 }

                $recommendations[] = [
                    'menu' => $altMenu->toArray(),
                    'source' => 'alternatif',
                    'suitability' => $altSuitability,
                    'warnings' => $altWarnings,
                    'score' => $this->calculateMenuScore($patient, $altMenu, $altSuitability)
                ];
            }
        }
        
        // Urutkan rekomendasi: paling cocok di atas
        usort($recommendations, function($a, $b) {
            if ($a['suitability'] === 'suitable' && $b['suitability'] !== 'suitable') return -1;
            if ($a['suitability'] !== 'suitable' && $b['suitability'] === 'suitable') return 1;
            return $b['score'] <=> $a['score'];
        });

        return $recommendations;
    }

    /**
     * Menerapkan filter keras (binary rules) pada menu.
     * @return array [suitability: 'suitable'|'unsuitable_condition'|'incompatible_diet', warnings: array]
     */
    protected function applyHardFilters(Patient $patient, Menu $menu, string $waktuMakan): array
    {
        $warnings = [];
        
        // Filter Kondisi Diet Klinis (BAB 2 Pola Pemberian Makan Pasien)
        // Ini memerlukan mapping di database: kondisi_diet_klinis -> jenis_tekstur_diet (cair, saring, lunak, padat)
        // Dan menu memiliki jenis_tekstur_menu
        // Contoh sederhana:
        if ($patient->kondisi_diet_klinis) {
            // Asumsi: kondisi_diet_klinis di pasien bisa langsung diconvert ke jenis tekstur yang dibutuhkan
            $requiredTexture = $this->mapKondisiKlinisToTexture($patient->kondisi_diet_klinis);
            // Asumsi: menu memiliki atribut 'tekstur' (padat, lunak, saring, cair)
            if ($requiredTexture && $menu->tekstur !== $requiredTexture) {
                $warnings[] = "Tidak cocok dengan kondisi diet klinis pasien ({$requiredTexture}).";
                return ['unsuitable_condition', $warnings];
            }
        }

        // Filter Pola Menu Diet Khusus (BAB 3)
        // Periksa apakah menu ini kompatibel dengan SEMUA diet khusus pasien
        foreach ($patient->dietKhusus as $pasienDietKhusus) {
            if (!$menu->dietKhusus->contains($pasienDietKhusus->id)) {
                $warnings[] = "Tidak kompatibel dengan Diet Khusus: {$pasienDietKhusus->nama}.";
                return ['incompatible_diet', $warnings];
            }
        }
        
        // Filter Standar Diit (BAB 4 - Aturan Kualitatif)
        if ($patient->standarDiit) {
            $standarDiit = $patient->standarDiit;
            // Kategori bahan terlarang
            if ($standarDiit->kategori_bahan_terlarang) {
                foreach ($menu->bahanMakanans as $bahanMenu) {
                    if (in_array($bahanMenu->kategori_bahan_masakan, $standarDiit->kategori_bahan_terlarang)) {
                        $warnings[] = "Mengandung kategori bahan terlarang: {$bahanMenu->kategori_bahan_masakan}.";
                        return ['unsuitable_restriction', $warnings];
                    }
                }
            }
            // Bahan makanan spesifik terlarang
            if ($standarDiit->bahan_makanan_terlarang_ids) {
                foreach ($menu->bahanMakanans as $bahanMenu) {
                    if (in_array($bahanMenu->id, $standarDiit->bahan_makanan_terlarang_ids)) {
                        $warnings[] = "Mengandung bahan terlarang: {$bahanMenu->nama}.";
                        return ['unsuitable_restriction', $warnings];
                    }
                }
            }
        }
        
        return ['suitable', $warnings];
    }

    /**
     * Memeriksa kepatuhan nutrisi kuantitatif menu terhadap standar diit pasien (BAB 4).
     * @return array [suitability: 'suitable'|'calorie_mismatch'|'macro_mismatch', warnings: array]
     */
    protected function checkNutrientCompliance(Patient $patient, Menu $menu, string $waktuMakan): array
    {
        $warnings = [];
        if (!$patient->standarDiit) {
            return ['suitable', ['Pasien tidak memiliki Standar Diit terhubung.']];
        }

        $standarDiit = $patient->standarDiit;
        $menuKalori = $menu->kalori;
        $menuKaloriUntukRasio = $menu->kalori > 0 ? $menu->kalori : 1; // Untuk menghindari division by zero
        $menuProteinRatio = $menu->total_protein / ($menuKaloriUntukRasio / 4);
        $menuCarbsRatio = $menu->total_karbohidrat / ($menuKaloriUntukRasio / 4);
        $menuFatRatio = $menu->total_lemak / ($menuKaloriUntukRasio / 9);

        // Pencocokan Kalori
        // if ($standarDiit->min_kalori && $menuKalori < $standarDiit->min_kalori) {
        //     $warnings[] = "Kalori menu ({$menuKalori} kcal) di bawah minimum standar ({$standarDiit->min_kalori} kcal).";
        //     return ['calorie_mismatch', $warnings];
        // }
        // if ($standarDiit->max_kalori && $menuKalori > $standarDiit->max_kalori) {
        //     $warnings[] = "Kalori menu ({$menuKalori} kcal) di atas maksimum standar ({$standarDiit->max_kalori} kcal).";
        //     return ['calorie_mismatch', $warnings];
        // }

        // Pencocokan Rasio Makro (jika standar memiliki rasio target)
        // Ini lebih kompleks, biasanya toleransi +- X%
        if ($standarDiit->target_protein_ratio && abs($menuProteinRatio - $standarDiit->target_protein_ratio) > 0.05) { // Toleransi 5%
            // $warnings[] = "Rasio protein menu tidak sesuai target.";
            // return ['macro_mismatch', $warnings];
        }
        // ... (check untuk karbohidrat dan lemak) ...

        // Pencocokan Pola Porsi Kategori (BAB 4 - Kuantitatif)
        // Ini adalah bagian yang paling kompleks dan memerlukan data yang sangat terstruktur di pola_porsi_kategori
        if ($standarDiit->pola_porsi_kategori) {
            $targetPolaPorsi = $this->getTargetPolaPorsiForSlot($standarDiit->pola_porsi_kategori, $waktuMakan, $patient);

            if ($targetPolaPorsi) {
                $menuCategoryComposition = $this->getMenuCategoryComposition($menu); // Hitung komposisi kategori menu

                foreach ($targetPolaPorsi as $category => $targetQuantity) {
                    if (isset($menuCategoryComposition[$category])) {
                        $actualQuantity = $menuCategoryComposition[$category];
                        // Periksa apakah actualQuantity mendekati targetQuantity
                        // Misalnya, toleransi 20% dari target
                        if (is_numeric($targetQuantity) && abs($actualQuantity - $targetQuantity) / $targetQuantity > 0.20) {
                            $warnings[] = "Komposisi kategori {$category} menu tidak sesuai pola porsi target.";
                            // return ['macro_mismatch', $warnings]; // Mungkin beda jenis suitability
                        }
                    } else if (is_numeric($targetQuantity) && $targetQuantity > 0) {
                         $warnings[] = "Menu tidak mengandung kategori {$category} yang dibutuhkan.";
                         // return ['macro_mismatch', $warnings];
                    }
                }
            }
        }

        return ['suitable', $warnings];
    }

    /**
     * Memeriksa ketersediaan stok bahan makanan untuk menu.
     * @return array [suitability: 'suitable'|'insufficient_stock', warnings: array]
     */
    protected function checkStockAvailability(Menu $menu): array
    {
        $warnings = [];
        foreach ($menu->bahanMakanans as $bahan) {
            $requiredQuantity = $bahan->pivot->jumlah; // Jumlah gram bahan yang dibutuhkan untuk menu ini
            if ($bahan->stok < $requiredQuantity) { // Asumsi stok di BahanMakanan adalah total gram tersedia
                $warnings[] = "Stok bahan '{$bahan->nama}' tidak cukup ({$bahan->stok}g tersedia, butuh {$requiredQuantity}g).";
                return ['insufficient_stock', $warnings];
            }
        }
        return ['suitable', $warnings];
    }

    /**
     * Menghitung skor kecocokan menu. (Semakin tinggi skor, semakin cocok)
     * Ini bisa disesuaikan lebih lanjut.
     * @param string $suitability Hasil dari filter dan compliance.
     */
    protected function calculateMenuScore(Patient $patient, Menu $menu, string $suitability): float
    {
        if ($suitability !== 'suitable') {
            return 0.0; // Menu yang tidak cocok mendapat skor 0
        }

        $score = 100.0; // Mulai dengan skor sempurna

        // Pengurangan skor berdasarkan warning atau ketidaksesuaian kecil
        // Anda bisa menambahkan logika pengurangan skor di sini jika checkNutrientCompliance
        // hanya memberikan warning tapi suitability tetap 'suitable'

        // Logika tambahan untuk preferensi atau diversifikasi dapat ditambahkan di sini
        // Misalnya, mengurangi skor jika menu ini baru saja disajikan
        // atau meningkatkan skor jika ahli gizi memberikan tag "favorit"

        return $score;
    }

    // --- Helper Methods ---
    protected function findAlternativeMenus(Patient $patient, string $waktuMakan, ?Menu $excludeMenu = null): Collection
    {
        $alternativeMenus = collect();
        
        // Ambil semua menu yang aktif dan load relasi bahanMakanans dan dietKhusus
        $allMenus = Menu::with('bahanMakanans', 'dietKhusus')->get();

        foreach ($allMenus as $altMenu) {
            // Lewati menu yang dikecualikan (misal: menu dasar yang tidak cocok)
            if ($excludeMenu && $altMenu->id === $excludeMenu->id) {
                continue;
            }

            // Terapkan filter keras pada menu alternatif
            list($suitability, $warnings) = $this->applyHardFilters($patient, $altMenu, $waktuMakan);

            if ($suitability === 'suitable') {
                // Jangan lakukan pemeriksaan nutrisi dan stok terlalu ketat di sini,
                // karena itu akan dilakukan oleh getRecommendedMenusForSlot
                // Cukup pastikan lolos filter keras saja.
                $alternativeMenus->push($altMenu);
            }
        }
        return $alternativeMenus;
    }
    /**
     * Memetakan kondisi diet klinis pasien ke jenis tekstur yang dibutuhkan.
     * Ini adalah logika mapping yang harus Anda definisikan berdasarkan dokumen BAB 2.
     * @param string $kondisiKlinis
     * @return string|null 'cair', 'saring', 'lunak', 'padat'
     */
    protected function mapKondisiKlinisToTexture(string $kondisiKlinis): ?string
    {
        // Contoh mapping, sesuaikan dengan dokumen BAB 2 Anda
        return match ($kondisiKlinis) {
            'Pasca Operasi Hari 1' => 'cair',
            'Diet Cair Awal' => 'cair',
            'Diet Saring Pasca-op' => 'saring',
            'Diet Lunak Pemulihan' => 'lunak',
            'diet biasa' => 'padat',
            default => null, // Jika tidak ada mapping spesifik
        };
    }

    /**
     * Mendapatkan pola porsi kategori target dari StandarDiit berdasarkan waktu makan dan demografi pasien.
     * @param array $polaPorsiKategoriJSON Struktur JSON dari standar_diits.pola_porsi_kategori
     * @param string $waktuMakan
     * @param Patient $patient
     * @return array|null Pola porsi untuk slot ini, misal ['makanan_pokok' => 100, 'lauk_hewani' => 50]
     */
    protected function getTargetPolaPorsiForSlot(array $polaPorsiKategoriJSON, string $waktuMakan, Patient $patient): ?array
    {
        if (!isset($polaPorsiKategoriJSON[$waktuMakan])) {
            return null; // Tidak ada pola untuk waktu makan ini
        }

        $waktuPorsi = $polaPorsiKategoriJSON[$waktuMakan];
        $targetPola = [];

        // Iterasi melalui kategori bahan makanan dalam waktu makan tersebut
        foreach ($waktuPorsi as $category => $demographicQuantities) {
            // Cari kunci yang paling cocok dengan demografi pasien
            // Misal: "1-3 tahun", "DM B", "1900_kalori", "umum"
            // Urutan prioritas: ID Diet Khusus spesifik, lalu Kondisi Klinis, lalu Tipe Pasien, lalu Usia, lalu "umum"

            $foundQuantity = null;

            // 1. Coba cocokkan dengan Diet Khusus yang diterapkan pasien
            // Asumsi kunci di JSON bisa seperti "DM_B_1900", "DM_B1_2100"
            // Anda perlu menentukan kunci unik untuk setiap varian diet di JSON Anda
            if ($patient->dietKhusus->isNotEmpty()) {
                foreach ($patient->dietKhusus as $dietKhusus) {
                    // Buat kunci yang mungkin ada di JSON berdasarkan diet khusus
                    // Contoh: "DM_B_1900_kalori" jika StandarDiit punya target itu
                    // Ini membutuhkan penyesuaian di nama kunci JSON atau di sini
                    $dietKey = str_replace(' ', '_', strtoupper($dietKhusus->nama)); // Contoh: "DIET_DIABETES"
                    if (isset($demographicQuantities[$dietKey])) {
                        $foundQuantity = $demographicQuantities[$dietKey];
                        break;
                    }
                    // Jika standar diit punya varian kalori (misal DM B 1900_kalori)
                    if ($patient->standarDiit && $patient->standarDiit->max_kalori) {
                        $variantKey = $dietKey . '_' . $patient->standarDiit->max_kalori . '_kalori';
                        if (isset($demographicQuantities[$variantKey])) {
                            $foundQuantity = $demographicQuantities[$variantKey];
                            break;
                        }
                    }
                }
            }

            // 2. Coba cocokkan dengan Kondisi Diet Klinis
            if (is_null($foundQuantity) && $patient->kondisi_diet_klinis) {
                $klinisKey = str_replace(' ', '_', strtolower($patient->kondisi_diet_klinis));
                if (isset($demographicQuantities[$klinisKey])) {
                    $foundQuantity = $demographicQuantities[$klinisKey];
                }
            }

            // 3. Coba cocokkan dengan Usia (misal "1-3 tahun", "4-6 tahun")
            if (is_null($foundQuantity)) {
                // Ini butuh logika untuk menentukan rentang usia dari pasien->usia
                $ageRangeKey = $this->getAgeRangeKey($patient->usia); // Fungsi helper lain
                if ($ageRangeKey && isset($demographicQuantities[$ageRangeKey])) {
                    $foundQuantity = $demographicQuantities[$ageRangeKey];
                }
            }

            // 4. Fallback ke "umum"
            if (is_null($foundQuantity) && isset($demographicQuantities['umum'])) {
                $foundQuantity = $demographicQuantities['umum'];
            }

            // Jika kuantitas ditemukan dan bukan "-", tambahkan ke target pola
            if (!is_null($foundQuantity) && $foundQuantity !== '-') {
                 $targetPola[$category] = is_numeric($foundQuantity) ? (float)$foundQuantity : $foundQuantity;
            }
        }
        return empty($targetPola) ? null : $targetPola;
    }

    /**
     * Menghitung komposisi kategori bahan makanan dari sebuah Menu.
     * @return array ['makanan_pokok' => total_gram, 'lauk_hewani' => total_gram]
     */
    protected function getMenuCategoryComposition(Menu $menu): array
    {
        $composition = [];
        foreach ($menu->bahanMakanans as $bahan) {
            $category = $bahan->kategori_bahan_masakan; // 'makanan_pokok', 'lauk_hewani', dst.
            $quantity = $bahan->pivot->jumlah; // Jumlah gram bahan dalam menu

            if (!isset($composition[$category])) {
                $composition[$category] = 0;
            }
            $composition[$category] += $quantity;
        }
        return $composition;
    }
    
    // --- Anda mungkin perlu helper tambahan seperti ini ---
    // protected function getAgeRangeKey(int $usia): ?string { /* logika menentukan "1-3 tahun", "4-6 tahun" dari usia */ }
}