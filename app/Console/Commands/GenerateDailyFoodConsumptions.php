<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use App\Models\JadwalMakanan;
use App\Models\FoodConsumption;
use App\Models\Menu; // Jangan lupa import model Menu
use Carbon\Carbon; // Untuk bekerja dengan tanggal
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GenerateDailyFoodConsumptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:daily-food-consumptions {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $this->info("Generating food consumptions for: " . $date->toDateString());

        // Ambil semua pasien yang aktif
        $activePatients = Patient::where('status_pasien', 'aktif')->get();

        if ($activePatients->isEmpty()) {
            $this->info("No active patients found for " . $date->toDateString());
            return Command::SUCCESS;
        }

        $totalGenerated = 0;
        $failedMenus = [];

        foreach ($activePatients as $patient) {
            // Temukan jadwal makanan aktif untuk pasien ini pada tanggal dan tipe pasien ini
            // Perhatikan bahwa jadwal bisa saja berlaku untuk beberapa hari
            $jadwalMenus = DB::table('jadwal_makanan_menu')
                            ->join('jadwal_makanans', 'jadwal_makanan_menu.jadwal_makanan_id', '=', 'jadwal_makanans.id')
                            ->where('jadwal_makanan_menu.tanggal', $date->toDateString()) // Hanya untuk tanggal hari ini
                            ->where('jadwal_makanans.tipe_pasien', $patient->tipe_pasien)
                            ->where('jadwal_makanans.tanggal_mulai', '<=', $date->toDateString())
                            ->where('jadwal_makanans.tanggal_selesai', '>=', $date->toDateString())
                            ->select('jadwal_makanan_menu.menu_id', 'jadwal_makanan_menu.waktu_makan')
                            ->get();

            if ($jadwalMenus->isEmpty()) {
                Log::info("No food schedule found for patient ID {$patient->id} ({$patient->nama}) on " . $date->toDateString());
                continue; // Lanjut ke pasien berikutnya
            }

            $dataConsumptions = [];
            $failedMenus = [];
            foreach ($jadwalMenus as $jadwalMenu) {
                $menu = Menu::find($jadwalMenu->menu_id);

                if (!$menu || is_null($menu->kalori)) {
                    // Log warning jika menu tidak ditemukan atau kalori kosong
                    Log::warning("Menu ID {$jadwalMenu->menu_id} tidak ditemukan atau kalori kosong saat generate konsumsi untuk pasien {$patient->id} ({$patient->nama}) pada tanggal {$date->toDateString()}.");
                    $failedMenus[] = [
                        'patient_id' => $patient->id,
                        'tanggal' => $date->toDateString(),
                        'menu_id' => $jadwalMenu->menu_id
                    ];
                    continue; // Lewati menu ini
                }

                $dataConsumptions[] = [
                    'patient_id' => $patient->id,
                    'tanggal' => $date->toDateString(),
                    'waktu_makan' => $jadwalMenu->waktu_makan,
                    'nama_makanan' => $menu->nama,
                    'kalori' => $menu->kalori,
                    'status' => 'planned', // Default status saat digenerate
                    'created_at' => now(),
                    'updated_at' => now(),
                    'menu_id' => $jadwalMenu->menu_id
                ];
            }

            // Masukkan data konsumsi ke database
            // if (!empty($dataConsumptions)) {
            //     // Hindari duplikasi: cek apakah sudah ada entri untuk pasien, tanggal, dan waktu makan yang sama
            //     foreach ($dataConsumptions as $consumptionData) {
            //         $exists = FoodConsumption::where('patient_id', $consumptionData['patient_id'])
            //                                 ->where('tanggal', $consumptionData['tanggal'])
            //                                 ->where('waktu_makan', $consumptionData['waktu_makan'])
            //                                 ->where('menu_id', $consumptionData['menu_id'])
            //                                 ->exists();
            //         if (!$exists) {
            //             FoodConsumption::create($consumptionData); // Insert satu per satu untuk menghindari duplikasi mudah
            //             $totalGenerated++;
            //         } else {
            //             Log::info("Food consumption for patient {$patient->id} ({$patient->nama}) on {$consumptionData['tanggal']} at {$consumptionData['waktu_makan']} already exists. Skipping.");
            //         }
            //     }
            // }
            if (!empty($dataConsumptions)) {
                foreach ($dataConsumptions as $consumptionData) {
                    // Menggunakan updateOrCreate untuk menghindari duplikasi dan membuat/memperbarui entri
                    FoodConsumption::updateOrCreate(
                        [
                            'patient_id' => $consumptionData['patient_id'],
                            'tanggal' => $consumptionData['tanggal'],
                            'waktu_makan' => $consumptionData['waktu_makan'],
                            'menu_id' => $consumptionData['menu_id'],
                        ],
                        [
                            'nama_makanan' => $consumptionData['nama_makanan'],
                            'kalori' => $consumptionData['kalori'],
                            'status' => $consumptionData['status'],
                            // created_at dan updated_at bisa juga dimasukkan di sini jika update
                            'created_at' => $consumptionData['created_at'],
                            'updated_at' => $consumptionData['updated_at'],
                        ]
                    );
                    // $totalGenerated++; // Uncomment jika Anda melacak ini
                }
            }
        }
        $this->info("Successfully generated {$totalGenerated} food consumption entries for " . $date->toDateString());
        if (!empty($failedMenus)) {
            $this->warn("Warnings for " . count($failedMenus) . " menus that could not be processed due to missing data. Check logs.");
        }
        return 0;
    }
}
