<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\JadwalMakanan;
use App\Models\BahanMakanan;
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
    // public function edit($id)
    // {
    //     $jadwal = JadwalMakanan::with('details')->findOrFail($id);
    //     $menus = BahanMakanan::all();

    //     return view('admin.jadwal_makanans.edit', compact('jadwal', 'menus'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'tanggal_mulai' => 'required|date',
    //         'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
    //         'tipe_pasien' => 'required|string',
    //         'menus' => 'required|array',
    //     ]);

    //     $jadwal = JadwalMakanan::findOrFail($id);
    //     $jadwal->update($request->only(['tanggal_mulai', 'tanggal_selesai', 'tipe_pasien']));

    //     // Hapus detail lama lalu insert baru
    //     $jadwal->details()->delete();
    //     foreach ($request->menus as $tanggal => $waktus) {
    //         foreach ($waktus as $waktu => $menu_id) {
    //             $jadwal->details()->create([
    //                 'tanggal' => $tanggal,
    //                 'waktu' => $waktu,
    //                 'bahan_makanan_id' => $menu_id,
    //             ]);
    //         }
    //     }

    //     return redirect()->route('admin.jadwal-makanans.show', $jadwal->id)
    //         ->with('success', 'Jadwal berhasil diperbarui.');
    // }
    public function edit($id)
    {
        $jadwal = JadwalMakanan::with('menus')->findOrFail($id);
        $menus = Menu::all();

        // Format ulang agar mudah dipakai di form edit
        $menuPerTanggal = [];
        foreach ($jadwal->menus as $menu) {
            $menuPerTanggal[$menu->pivot->tanggal][$menu->pivot->waktu_makan] = $menu->id;
        }

        return view('admin.jadwal_makanans.edit', compact('jadwal', 'menus', 'menuPerTanggal'));
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

        return redirect()->route('admin.jadwal-makanans.show', $jadwal->id)
            ->with('success', 'Jadwal berhasil diperbarui.');
    }



}
