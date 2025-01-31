<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Patient;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Konstruktor untuk menerapkan middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Menampilkan daftar menu.
     */
    public function index()
    {
        // $menus = Menu::all()->groupBy('tipe');
        $menus = Menu::all();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Menampilkan form untuk menambahkan menu baru.
     */
    public function create()
    {
        return view('admin.menus.create');
    }

    /**
     * Menyimpan menu baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data
        // $request->validate([
        //     'tipe' => 'required|in:VVIP,VIP,Normal',
        //     'nama_makanan' => 'required|string|max:255',
        //     'kalori' => 'required|integer|min:0',
        // ]);

        // // Cek duplikasi
        // $exists = Menu::where('tipe', $request->tipe)
        //               ->where('nama_makanan', $request->nama_makanan)
        //               ->exists();

        // if ($exists) {
        //     return redirect()->back()->withErrors(['nama_makanan' => 'Makanan ini sudah ada untuk tipe pasien yang dipilih.'])->withInput();
        // }

        // // Simpan menu
        // $menu = Menu::create([
        //     'tipe' => $request->tipe,
        //     'nama_makanan' => $request->nama_makanan,
        //     'kalori' => $request->kalori,
        // ]);

        // // Perbarui kalori_makanan untuk semua pasien dengan tipe yang sama
        // Patient::where('tipe_pasien', $request->tipe)->update([
        //     'kalori_makanan' => Menu::where('tipe', $request->tipe)->sum('kalori'),
        // ]);

        // return redirect()->route('admin.menus.index')->with('success', 'Menu baru berhasil ditambahkan dan kalori pasien diperbarui.');
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'protein' => 'required|integer|min:0',
            'karbohidrat' => 'required|integer|min:0',
            'total_lemak' => 'required|integer|min:0',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'kategori_bahan_masakan' => 'required|in:makanan_pokok,lauk,sayur,buah,susu',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('images/menus', 'public');
        }

        Menu::create($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu makanan berhasil ditambahkan!');
    }



    /**
     * Menampilkan form untuk mengedit menu.
     */
//     public function edit(Menu $menu)
//     {
    //         $tipe_options = ['VVIP', 'VIP', 'Normal'];
    //         return view('admin.menus.edit', compact('menu', 'tipe_options'));
    //     }

    //     /**
    //      * Memperbarui menu.
    //      */
    //     public function update(Request $request, Menu $menu)
    // {
    //     // Validasi data
    //     $request->validate([
    //         'tipe' => 'required|in:VVIP,VIP,Normal',
    //         'nama_makanan' => 'required|string|max:255',
    //         'kalori' => 'required|integer|min:0',
    //     ]);

    //     // Cek duplikasi
    //     $exists = Menu::where('tipe', $request->tipe)
    //                   ->where('nama_makanan', $request->nama_makanan)
    //                   ->where('id', '!=', $menu->id)
    //                   ->exists();

    //     if ($exists) {
    //         return redirect()->back()->withErrors(['nama_makanan' => 'Makanan ini sudah ada untuk tipe pasien yang dipilih.'])->withInput();
    //     }

    //     // Update menu
    //     $menu->update([
    //         'tipe' => $request->tipe,
    //         'nama_makanan' => $request->nama_makanan,
    //         'kalori' => $request->kalori,
    //     ]);

    //     // Perbarui kalori_makanan untuk semua pasien dengan tipe yang sama
    //     Patient::where('tipe_pasien', $request->tipe)->update([
    //         'kalori_makanan' => Menu::where('tipe', $request->tipe)->sum('kalori'),
    //     ]);

    //     return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui dan kalori pasien diperbarui.');
    // }


//     /**
//      * Menghapus menu.
//      */
//     public function destroy(Menu $menu)
// {
//     $tipe = $menu->tipe;
//     $menu->delete();

//     // Perbarui kalori_makanan untuk semua pasien dengan tipe yang sama
//     Patient::where('tipe_pasien', $tipe)->update([
//         'kalori_makanan' => Menu::where('tipe', $tipe)->sum('kalori'),
//     ]);

//     return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus dan kalori pasien diperbarui.');
// }
    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'protein' => 'required|integer|min:0',
            'karbohidrat' => 'required|integer|min:0',
            'total_lemak' => 'required|integer|min:0',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'kategori_bahan_masakan' => 'required|in:makanan_pokok,lauk,sayur,buah,susu',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($menu->gambar) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('images/menus', 'public');
        }

        $menu->update($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu makanan berhasil diperbarui!');
    }


}
