<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\JadwalMakanan;
use Illuminate\Support\Facades\DB;

class JadwalMakananController extends Controller
{
    public function index()
    {
        $jadwals = JadwalMakanan::withCount(['menus'])->latest()->get();
        return view('admin.jadwal_makanans.index', compact('jadwals'));
    }

    public function create()
    {
        $menus = Menu::all();
        return view('admin.jadwal_makanans.create', compact('menus'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'menus' => 'required|array',
        ]);

        // Simpan jadwal utama
        $jadwal = JadwalMakanan::create([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tipe_pasien' => $request->tipe_pasien,
        ]);

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

        // Simpan ke tabel pivot
        DB::table('jadwal_makanan_menu')->insert($dataMenus);

        return redirect()->route('admin.jadwal-makanans.create')->with('success', 'Jadwal makanan berhasil dibuat.');
    }

    public function show($id)
    {
        $jadwal = JadwalMakanan::with('menus')->findOrFail($id);

        // Menyusun menu berdasarkan tanggal dan waktu
        $menuPerTanggal = [];

        foreach ($jadwal->menus as $menu) {
            $menuPerTanggal[$menu->pivot->tanggal][$menu->pivot->waktu_makan] = $menu;
        }

        return view('admin.jadwal_makanans.show', compact('jadwal', 'menuPerTanggal'));
    }

}
