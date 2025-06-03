<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\JadwalMakanan;
use App\Models\BahanMakanan;
use App\Models\Patient;
use Illuminate\Support\Facades\Log;

use App\Models\FoodConsumption;

use Illuminate\Support\Facades\DB;

class JadwalMakananController extends Controller
{
    public function index()
    {
        $jadwals = JadwalMakanan::withCount(['menus'])->latest()->get();
        return view('ahli-gizi.jadwal_makanans.index', compact('jadwals'));
    }

    public function create()
    {
        $menus = Menu::all();
        return view('ahli-gizi.jadwal_makanans.create', compact('menus'));
    }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'tanggal_mulai' => 'required|date',
    //         'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
    //         'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
    //         'menus' => 'required|array',
    //     ]);

    //     // Simpan jadwal utama
    //     $jadwal = JadwalMakanan::create([
    //         'tanggal_mulai' => $request->tanggal_mulai,
    //         'tanggal_selesai' => $request->tanggal_selesai,
    //         'tipe_pasien' => $request->tipe_pasien,
    //     ]);

    //     $dataMenus = [];

    //     foreach ($request->menus as $tanggal => $waktus) {
    //         foreach ($waktus as $waktu => $menu_id) {
    //             if ($menu_id) {
    //                 $dataMenus[] = [
    //                     'jadwal_makanan_id' => $jadwal->id,
    //                     'menu_id' => $menu_id,
    //                     'tanggal' => $tanggal,
    //                     'waktu_makan' => $waktu,
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //             }
    //         }
    //     }

    //     // Simpan ke tabel pivot
    //     DB::table('jadwal_makanan_menu')->insert($dataMenus);

    //     return redirect()->route('ahli-gizi.jadwal-makanans.create')->with('success', 'Jadwal makanan berhasil dibuat.');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'menus' => 'required|array',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Simpan jadwal utama di dalam transaksi
                $jadwal = JadwalMakanan::create([
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'tipe_pasien' => $request->tipe_pasien,
                ]);

                $dataMenus = [];
                // $dataConsumptions = [];

                // Ambil pasien yang aktif dan sesuai tipe
                // $activePatients = Patient::where('status_pasien', 'aktif')
                //                         ->where('tipe_pasien', $request->tipe_pasien)
                //                         ->get();

                foreach ($request->menus as $tanggal => $waktus) {
                    foreach ($waktus as $waktu => $menu_id) {
                        if ($menu_id) {
                            // Masukkan ke tabel pivot jadwal
                            $dataMenus[] = [
                                'jadwal_makanan_id' => $jadwal->id,
                                'menu_id' => $menu_id,
                                'tanggal' => $tanggal,
                                'waktu_makan' => $waktu,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            // Ambil data menu
                            // $menu = Menu::find($menu_id);
                            // if (!$menu || is_null($menu->kalori)) {
                            //     Log::warning("Menu ID {$menu_id} tidak ditemukan atau kalori kosong. Melewati pembuatan konsumsi untuk menu ini.");
                            //     continue; 
                            // }

                            // Buat record konsumsi makanan untuk setiap pasien aktif
                            // foreach ($activePatients as $patient) {
                            //     $dataConsumptions[] = [
                            //         'patient_id' => $patient->id,
                            //         'tanggal' => $tanggal,
                            //         'waktu_makan' => $waktu,
                            //         'nama_makanan' => $menu->nama,
                            //         'kalori' => $menu->kalori, 
                            //         'status' => 'planned', // Default status saat jadwal dibuat
                            //         'created_at' => now(),
                            //         'updated_at' => now(),
                            //     ];
                            // }
                        }
                    }
                }

                if (!empty($dataMenus)) {
                    DB::table('jadwal_makanan_menu')->insert($dataMenus);
                }
                // if (!empty($dataConsumptions)) {
                //     FoodConsumption::insert($dataConsumptions);
                // }
            });

            return redirect()->route('ahli-gizi.jadwal-makanans.create')->with('success', 'Jadwal makanan dan rencana konsumsi berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan jadwal makanan dan konsumsi: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return back()->with('error', 'Terjadi kesalahan saat membuat jadwal makanan: ' . $e->getMessage());
        }
        // Simpan jadwal utama
        // $jadwal = JadwalMakanan::create([
        //     'tanggal_mulai' => $request->tanggal_mulai,
        //     'tanggal_selesai' => $request->tanggal_selesai,
        //     'tipe_pasien' => $request->tipe_pasien,
        // ]);

        // $dataMenus = [];
        // $dataConsumptions = [];

        // $activePatients = Patient::where('status_pasien', 'aktif')
        //                     ->where('tipe_pasien', $request->tipe_pasien)
        //                     ->get();

        // foreach ($request->menus as $tanggal => $waktus) {
        //     foreach ($waktus as $waktu => $menu_id) {
        //         if ($menu_id) {
        //             $dataMenus[] = [
        //                 'jadwal_makanan_id' => $jadwal->id,
        //                 'menu_id' => $menu_id,
        //                 'tanggal' => $tanggal,
        //                 'waktu_makan' => $waktu,
        //                 'created_at' => now(),
        //                 'updated_at' => now(),
        //             ];

        //             $menu = Menu::find($menu_id);
        //             if (!$menu || is_null($menu->kalori)) {
        //                 continue; 
        //             }

        //             foreach ($activePatients as $patient) {
        //                 $dataConsumptions[] = [
        //                     'patient_id' => $patient->id,
        //                     'tanggal' => $tanggal,
        //                     'waktu_makan' => $waktu,
        //                     'nama_makanan' => $menu->nama,
        //                     'kalori' => $menu->kalori, 
        //                     'status' => 'planned',
        //                     'created_at' => now(),
        //                     'updated_at' => now(),
        //                 ];
        //             }
        //         }
        //     }
        // }

        // Simpan data ke pivot dan ke food_consumptions
        // DB::table('jadwal_makanan_menu')->insert($dataMenus);
        // FoodConsumption::insert($dataConsumptions);
        // try {
        //     DB::table('jadwal_makanan_menu')->insert($dataMenus);
        //     FoodConsumption::insert($dataConsumptions);
        // } catch (\Exception $e) {
        //     return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        // }

        // return redirect()->route('ahli-gizi.jadwal-makanans.create')->with('success', 'Jadwal makanan berhasil dibuat.');
    }

    public function show($id)
    {
        $jadwal = JadwalMakanan::with('menus')->findOrFail($id);

        // Menyusun menu berdasarkan tanggal dan waktu
        $menuPerTanggal = [];

        foreach ($jadwal->menus as $menu) {
            $menuPerTanggal[$menu->pivot->tanggal][$menu->pivot->waktu_makan] = $menu;
        }

        return view('ahli-gizi.jadwal_makanans.show', compact('jadwal', 'menuPerTanggal'));
    }
    public function edit($id)
    {
        $jadwal = JadwalMakanan::with('menus')->findOrFail($id);
        $menus = Menu::all();

        // Format ulang agar mudah dipakai di form edit
        $menuPerTanggal = [];
        foreach ($jadwal->menus as $menu) {
            $menuPerTanggal[$menu->pivot->tanggal][$menu->pivot->waktu_makan] = $menu->id;
        }

        return view('ahli-gizi.jadwal_makanans.edit', compact('jadwal', 'menus', 'menuPerTanggal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'menus' => 'required|array',
        ]);

        $jadwal = JadwalMakanan::findOrFail($id);
        $jadwal->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tipe_pasien' => $request->tipe_pasien,
        ]);

        // Hapus menu lama
        DB::table('jadwal_makanan_menu')->where('jadwal_makanan_id', $jadwal->id)->delete();

        // Simpan menu baru
        $dataMenus = [];
        foreach ($request->menus as $tanggal => $waktus) {
            foreach ($waktus as $waktu => $menu_id) {
                if ($menu_id) {
                    $dataMenus[] = [
                        'jadwal_makanan_id' => $jadwal->id,
                        'menu_id' => $menu_id,
                        'tanggal' => $tanggal,
                        'waktu_makan' => $waktu,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('jadwal_makanan_menu')->insert($dataMenus);

        return redirect()->route('ahli-gizi.jadwal-makanans.show', $jadwal->id)
            ->with('success', 'Jadwal berhasil diperbarui.');
    }



    // public function byTanggal(Request $request)
    // {
    //     Log::info('Mencari data jadwal_makanan_menu berdasarkan tanggal: ' . $request->date);
    
    //     $result = DB::table('jadwal_makanan_menu')
    //         ->join('jadwal_makanans', 'jadwal_makanan_menu.jadwal_makanan_id', '=', 'jadwal_makanans.id')
    //         ->join('menus', 'jadwal_makanan_menu.menu_id', '=', 'menus.id')
    //         ->whereDate('jadwal_makanan_menu.tanggal', $request->date)
    //         ->select(
    //             'jadwal_makanan_menu.*',
    //             'menus.nama as nama_menu',
    //             // 'jadwal_makanans'
    //         )
    //         ->get();
    
    //     return response()->json($result);
    // }
    


}
