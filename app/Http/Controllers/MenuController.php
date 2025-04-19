<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Menu;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Carbon\Carbon;

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
    // public function index()
    // {
    //     // $menus = Menu::all()->groupBy('tipe');
    //     $menus = Menu::all();
    //     $menus = Menu::paginate(12);
    //     return view('admin.menus.index', compact('menus'));
    // }
    // public function index(Request $request)
    // {
    //     $query = Menu::query();

    //     // Filter pencarian berdasarkan nama
    //     if ($request->filled('search')) {
    //         $query->where('nama', 'like', '%' . $request->search . '%');
    //     }

    //     // Filter berdasarkan kategori_bahan_masakan
    //     if ($request->filled('kategori')) {
    //         $query->where('kategori_bahan_masakan', $request->kategori);
    //     }

    //     // Ambil data kategori unik untuk filter dropdown
    //     $kategoriOptions = Menu::select('kategori_bahan_masakan')
    //         ->distinct()
    //         ->pluck('kategori_bahan_masakan');

    //     // Ambil hasil akhir dengan paginasi
    //     $menus = $query->paginate(12);

    //     return view('admin.menus.index', compact('menus', 'kategoriOptions'));
    // }
    public function index(Request $request)
    {
        $menus = Menu::query()
            ->when($request->search, fn($q) =>
                $q->where('nama', 'like', '%' . $request->search . '%'))
            ->when($request->kategori, fn($q) =>
                $q->where('kategori_bahan_masakan', $request->kategori))
            ->when($request->updated, function ($q) use ($request) {
                // if ($request->updated) {
                //     $q->where('updated_at', '>=', Carbon::now()->subDays((int)$request->updated));
                // }
                switch ($request->updated) {
                    case 'today':
                        $q->whereDate('updated_at', Carbon::today());
                        break;
                    case 'last7':
                        $q->where('updated_at', '>=', Carbon::now()->subDays(7));
                        break;
                    case 'last30':
                        $q->where('updated_at', '>=', Carbon::now()->subDays(30));
                        break;
                    case 'custom':
                        if ($request->from && $request->to) {
                            $from = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay();
                            $to = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay();
                            $q->whereBetween('updated_at', [$from, $to]);
                        }
                        break;
                    default:
                        // "anytime", do nothing
                        break;
                }
            })
            ->orderBy('nama', 'asc')
            ->paginate(12)
            ->appends($request->all());

        $kategoriOptions = Menu::select('kategori_bahan_masakan')->distinct()->pluck('kategori_bahan_masakan');

        return view('admin.menus.index', compact('menus', 'kategoriOptions'));
    }
    // public function show(Menu $menu)
    // {
    //     $menus = Menu::all();
    //     return view('admin.menus.show', compact('menus'));
    // }

    public function show(Menu $menu)
{
    return view('admin.menus.show', compact('menu')); // Kirim satu menu saja
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

    // public function importCsv(Request $request)
    // {
    //     $request->validate([
    //         'csv_file' => 'required|file|mimes:csv,txt|max:2048',
    //     ]);

    //     $file = $request->file('csv_file');
    //     $path = $file->getRealPath();
        
    //     $csv = Reader::createFromPath($path, 'r');
    //     $csv->setHeaderOffset(0); // Gunakan baris pertama sebagai header
        
    //     $records = $csv->getRecords();

    //     foreach ($records as $record) {
    //         Menu::create([
    //             'nama' => $record['name'],
    //             'protein' => (int) $record['proteins'],
    //             'karbohidrat' => (int) $record['carbohydrate'],
    //             'total_lemak' => (int) $record['fat'],
    //             'tipe_pasien' => 'Normal', // Bisa diubah sesuai kebutuhan
    //             'kategori_bahan_masakan' => 'makanan_pokok', // Bisa dibuat aturan konversi
    //             'gambar' => $record['image'], // Pastikan URL gambar dapat diakses atau upload ke storage
    //         ]);
    //     }

    //     return redirect()->route('admin.menus.index')->with('success', 'Data dari CSV berhasil diimpor!');
    // }
    public function importCsv(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt|max:2048',
    ]);

    $file = $request->file('csv_file');
    $path = $file->getRealPath();
    
    $csv = Reader::createFromPath($path, 'r');
    $csv->setHeaderOffset(0); // Gunakan baris pertama sebagai header
    
    $records = $csv->getRecords();

    foreach ($records as $record) {
        // Pastikan nilai gambar tidak lebih dari 255 karakter
        $imageUrl = $record['image'] ?? null; // Pastikan kolom ada dan tidak null
        if ($imageUrl && strlen($imageUrl) > 255) {
            $imageUrl = null; // Bisa juga diisi dengan gambar default jika diinginkan
        }

        Menu::create([
            'nama' => $record['name'],
            'protein' => (int) $record['proteins'],
            'karbohidrat' => (int) $record['carbohydrate'],
            'total_lemak' => (int) $record['fat'],
            'tipe_pasien' => 'Normal', // Bisa diubah sesuai kebutuhan
            'kategori_bahan_masakan' => 'makanan_pokok', // Bisa dibuat aturan konversi
            'gambar' => $imageUrl, // Simpan URL jika valid
        ]);
    }

    return redirect()->route('admin.menus.index')->with('success', 'Data dari CSV berhasil diimpor!');
}

}
