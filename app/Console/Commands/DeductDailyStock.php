<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FoodConsumption; // Untuk mendapatkan menu yang direncanakan
use App\Models\BahanMakanan; // Untuk memperbarui stok
use App\Models\RiwayatStokBahanMakanan; // Untuk mencatat riwayat stok
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DeductDailyStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:deduct-daily {date?}'; // date? opsional untuk tanggal spesifik

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deducts daily required ingredients from stock based on planned food consumptions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::today(); // Default hari ini
        $dateString = $date->toDateString();

        $this->info("Starting daily stock deduction for planned meals on: " . $dateString);
        Log::info("DeductDailyStock: Starting deduction for {$dateString}.");

        // --- 1. Identifikasi Kebutuhan Bahan Makanan Harian ---
        // Ambil semua FoodConsumption yang berstatus 'planned' untuk tanggal ini
        // Eager load relasi menu dan bahanMakanans dari menu
        $plannedConsumptions = FoodConsumption::where('status', 'planned')
            ->whereDate('tanggal', $dateString)
            ->with('menu.bahanMakanans') // Memuat relasi nested
            ->get();

        if ($plannedConsumptions->isEmpty()) {
            $this->info("No planned meals found for {$dateString}. No stock deduction needed.");
            Log::info("DeductDailyStock: No planned meals found for {$dateString}.");
            return Command::SUCCESS;
        }

        $requiredBahanTotal = []; // Array asosiatif: [bahan_id => total_quantity_needed]
        $deductionDetails = []; // Untuk logging dan riwayat stok

        foreach ($plannedConsumptions as $consumption) {
            if ($consumption->menu) {
                foreach ($consumption->menu->bahanMakanans as $bahan) {
                    $requiredQuantity = $bahan->pivot->jumlah; // Jumlah gram bahan yang dibutuhkan per porsi menu

                    if (!isset($requiredBahanTotal[$bahan->id])) {
                        $requiredBahanTotal[$bahan->id] = 0;
                    }
                    $requiredBahanTotal[$bahan->id] += $requiredQuantity; // Akumulasi total kebutuhan
                }
            }
        }

        // --- 2. Pengurangan Stok Aktual ---
        $totalDeductions = 0;
        $insufficientStockWarnings = [];

        DB::beginTransaction(); // Gunakan transaksi untuk atomisitas
        try {
            foreach ($requiredBahanTotal as $bahanId => $quantityNeeded) {
                $bahan = BahanMakanan::find($bahanId);

                if (!$bahan) {
                    $this->warn("Bahan Makanan ID {$bahanId} tidak ditemukan. Melewati pengurangan.");
                    Log::warning("DeductDailyStock: Bahan Makanan ID {$bahanId} not found.");
                    continue;
                }

                if ($bahan->stok >= $quantityNeeded) {
                    $oldStock = $bahan->stok; // Simpan stok lama sebelum dikurangi
                    $bahan->stok -= $quantityNeeded;
                    $bahan->save(); // Simpan perubahan stok

                    // Catat riwayat stok
                    RiwayatStokBahanMakanan::create([
                        'bahan_makanan_id' => $bahan->id,
                        'tipe' => 'keluar', // <--- KOREKSI INI: Masukkan nilai 'tipe' (misal: 'masuk', 'keluar', 'penyesuaian')
                        'jumlah' => -$quantityNeeded, // <--- KOREKSI INI: Jumlah perubahan (negatif untuk pengurangan)
                        'satuan' => 'gram', // <--- KOREKSI INI: Sediakan 'satuan' (misal: 'gram', 'unit'). Asumsi 'gram'
                        'keterangan' => "Pengurangan stok untuk jadwal makan {$dateString}", // <--- KOREKSI INI: Ganti 'catatan' jadi 'keterangan'
                        // 'stok_sebelumnya' => $oldStock, // Hapus baris ini jika tidak ada di tabel
                        // 'stok_setelahnya' => $bahan->stok, // Hapus baris ini jika tidak ada di tabel
                    ]);
                    $totalDeductions++;
                    $deductionDetails[] = "{$bahan->nama}: {$quantityNeeded}g dikurangi. Sisa: {$bahan->stok}g";
                    Log::info("DeductDailyStock: Stok '{$bahan->nama}' (ID: {$bahan->id}) dikurangi {$quantityNeeded}g. Stok baru: {$bahan->stok}g.");

                } else {
                    $this->warn("Stok '{$bahan->nama}' tidak cukup ({$bahan->stok}g tersedia, butuh {$quantityNeeded}g). Tidak dikurangi.");
                    $insufficientStockWarnings[] = "Stok '{$bahan->nama}' tidak cukup ({$bahan->stok}g tersedia, butuh {$quantityNeeded}g).";
                    Log::warning("DeductDailyStock: Stok '{$bahan->nama}' (ID: {$bahan->id}) tidak cukup. Tersedia {$bahan->stok}g, butuh {$quantityNeeded}g.");
                    // Anda bisa memilih untuk Rollback di sini atau melanjutkan dengan peringatan
                }
            }

            DB::commit(); // Commit semua perubahan jika semua berhasil
            $this->info("Completed daily stock deduction for {$dateString}. Total bahan dikurangi: {$totalDeductions}.");
            Log::info("DeductDailyStock: Finished deduction for {$dateString}. Total items deducted: {$totalDeductions}.");

            if (!empty($insufficientStockWarnings)) {
                $this->error("Peringatan: Stok tidak cukup untuk beberapa bahan:");
                foreach ($insufficientStockWarnings as $warning) {
                    $this->error("- " . $warning);
                }
                Log::error("DeductDailyStock: Insufficient stock warnings: " . implode('; ', $insufficientStockWarnings));
            }

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback semua jika ada kesalahan
            $this->error("Failed to deduct stock for {$dateString}: " . $e->getMessage());
            Log::error("DeductDailyStock: Exception during deduction for {$dateString}: {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
            return Command::FAILURE; // Mengembalikan status kegagalan
        }

        return Command::SUCCESS;
    }
}