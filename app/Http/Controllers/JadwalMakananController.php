<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\JadwalMakanan;
use App\Models\BahanMakanan;
use App\Models\Patient;
use App\Services\MealRecommendationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\FoodConsumption;

use Illuminate\Support\Facades\DB;

class JadwalMakananController extends Controller
{
    public function index()
    {
        $jadwals = JadwalMakanan::withCount(['menus'])->latest()->get();

        // KEMBALIKAN KE VIEW JADWAL MAKANAN
        return view('ahli-gizi.jadwal_makanans.index', compact('jadwals'));
    }

    public function create()
    {
        $menus = Menu::all();
        $patients = Patient::all(); // Ambil semua pasien
        $waktuMakanOptions = ['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'];
        $tipePasienOptions = ['VVIP', 'VIP', 'Normal'];
        return view('ahli-gizi.jadwal_makanans.create', compact('menus', 'patients', 'waktuMakanOptions','tipePasienOptions'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'jadwal_detail' => 'nullable|array', // Mengubah nama field untuk detail jadwal
            'jadwal_detail.*.patient_id' => 'required|exists:patients,id',
            'jadwal_detail.*.date' => 'required|date_format:Y-m-d',
            'jadwal_detail.*.waktu_makan' => ['required', Rule::in(['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'])],
            'jadwal_detail.*.menu_id' => 'required|exists:menus,id',
        ]);
        DB::beginTransaction();
        try {
            // Buat entri jadwal utama (JadwalMakanan)
            // Ini bisa berfungsi sebagai header atau ringkasan jadwal yang dibuat
            $jadwalUtama = JadwalMakanan::create([
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'tipe_pasien' => $request->tipe_pasien,
                'keterangan' => 'Dibuat/Diperbarui melalui Smart Scheduler', // Tambahkan keterangan
            ]);

            $totalGenerated = 0;
            // Jika ada detail jadwal yang dikirim (dari auto-generate atau input manual)
            if ($request->has('jadwal_detail') && is_array($request->jadwal_detail)) {
                foreach ($request->jadwal_detail as $item) {
                    // Ambil nama dan kalori menu untuk disimpan di FoodConsumption
                    $menu = Menu::find($item['menu_id']);
                    if (!$menu) {
                        Log::warning("Menu ID {$item['menu_id']} tidak ditemukan saat menyimpan jadwal detail.");
                        continue; // Lewati item ini jika menu tidak ada
                    }

                    // Gunakan updateOrCreate untuk mencegah duplikasi dan memperbarui entri
                    // Kunci unik untuk updateOrCreate di sini adalah patient_id, tanggal, waktu_makan
                    $foodConsumption = \App\Models\FoodConsumption::updateOrCreate(
                        [
                            'patient_id' => $item['patient_id'],
                            'tanggal' => $item['date'],
                            'waktu_makan' => $item['waktu_makan'],
                            // Tidak perlu menu_id di sini jika Anda hanya ingin unique berdasarkan slot waktu pasien
                            // Jika ingin unique per menu di slot yang sama (misal patient A makan Nasi Goreng pagi dan juga Nasi Bakar pagi), maka perlu kunci menu_id
                            // Untuk saat ini, asumsikan hanya 1 menu per slot waktu pasien
                        ],
                        [
                            'menu_id' => $item['menu_id'],
                            'nama_makanan' => $menu->nama, // Ambil nama menu dari model
                            'kalori' => $menu->kalori, // Ambil kalori menu dari model
                            'status' => 'planned', // Default status planned saat dijadwalkan
                            'actual_kalori' => 0, // Reset actual_kalori saat dijadwalkan
                            'actual_protein' => 0,
                            'actual_karbohidrat' => 0,
                            'actual_lemak' => 0,
                            'consumption_percentage' => null,
                            'notes' => null,
                        ]
                    );
                    if ($foodConsumption->wasRecentlyCreated) {
                        $totalGenerated++;
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('ahli-gizi.jadwal-makanans.index')->with('success', "Jadwal utama dibuat. {$totalGenerated} entri konsumsi berhasil dibuat/diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan jadwal makanan dan konsumsi: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat jadwal makanan: ' . $e->getMessage());
        }
        // try {
        //     DB::transaction(function () use ($request) {
        //         // Simpan jadwal utama di dalam transaksi
        //         $jadwal = JadwalMakanan::create([
        //             'tanggal_mulai' => $request->tanggal_mulai,
        //             'tanggal_selesai' => $request->tanggal_selesai,
        //             'tipe_pasien' => $request->tipe_pasien,
        //             'keterangan' => 'Dibuat/Diperbarui melalui Smart Scheduler',
        //         ]);

        //         // $dataMenus = [];
        //         // $dataConsumptions = [];

        //         // Ambil pasien yang aktif dan sesuai tipe
        //         // $activePatients = Patient::where('status_pasien', 'aktif')
        //         //                         ->where('tipe_pasien', $request->tipe_pasien)
        //         //                         ->get();

        //         foreach ($request->menus as $tanggal => $waktus) {
        //             foreach ($waktus as $waktu => $menu_id) {
        //                 if ($menu_id) {
        //                     // Masukkan ke tabel pivot jadwal
        //                     $dataMenus[] = [
        //                         'jadwal_makanan_id' => $jadwal->id,
        //                         'menu_id' => $menu_id,
        //                         'tanggal' => $tanggal,
        //                         'waktu_makan' => $waktu,
        //                         'created_at' => now(),
        //                         'updated_at' => now(),
        //                     ];
        //                 }
        //             }
        //         }

        //         if (!empty($dataMenus)) {
        //             DB::table('jadwal_makanan_menu')->insert($dataMenus);
        //         }
        //     });

        //     return redirect()->route('ahli-gizi.jadwal-makanans.create')->with('success', 'Jadwal makanan dan rencana konsumsi berhasil dibuat.');

        // } catch (\Exception $e) {
        //     Log::error('Gagal menyimpan jadwal makanan dan konsumsi: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
        //     return back()->with('error', 'Terjadi kesalahan saat membuat jadwal makanan: ' . $e->getMessage());
        // }
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
    public function getRecommendations(Request $request, MealRecommendationService $mealRecommendationService)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date_format:Y-m-d',
            'waktu_makan' => ['required', Rule::in(['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'])],
        ]);

        $patient = Patient::find($request->patient_id);
        
        // Memuat relasi yang diperlukan oleh service
        $patient->load('standarDiit', 'dietKhusus'); 
        
        $recommendations = $mealRecommendationService->getRecommendedMenusForSlot(
            $patient,
            $request->date,
            $request->waktu_makan
        );

        return response()->json($recommendations);
    }
}
