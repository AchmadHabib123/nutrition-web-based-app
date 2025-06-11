<?php

namespace App\Http\Controllers;

use App\Models\BahanMakanan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Patient;
use App\Models\RiwayatStokBahanMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BahanMakananController extends Controller
{
    /**
     * Konstruktor untuk menerapkan middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:ahli-gizi']);
    }

    /**
     * Menampilkan daftar menu.
     */
    // public function index()
    // {
    //     // $bahanMakanans = Menu::all()->groupBy('tipe');
    //     $bahanMakanans = Menu::all();
    //     $bahanMakanans = Menu::paginate(12);
    //     return view('ahli-gizi.logistics.index', compact('bahan_makanans'));
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
    //     $bahanMakanans = $query->paginate(12);

    //     return view('ahli-gizi.logistics.index', compact('bahan_makanans', 'kategoriOptions'));
    // }
    public function index(Request $request)
    {
        $bahanMakanans = BahanMakanan::query()
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

        $kategoriOptions = BahanMakanan::select('kategori_bahan_masakan')->distinct()->pluck('kategori_bahan_masakan');
        $startDate = Carbon::now()->subDays(6)->startOfDay(); // Default: 7 hari terakhir
    $endDate = Carbon::now()->endOfDay();

    switch ($request->updated) {
        case 'today':
            $startDate = Carbon::today()->startOfDay();
            break;
        case 'last7':
            // Sudah menjadi default
            break;
        case 'last30':
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            break;
        case 'custom':
            if ($request->from && $request->to) {
                $startDate = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay();
            }
            break;
    }

    // 2. Buat periode tanggal untuk label dan mapping data
    $period = CarbonPeriod::create($startDate, $endDate);
    $dates = collect($period)->map(fn ($date) => $date->format('Y-m-d'));

    // 3. Query data stok masuk dan keluar sesuai rentang tanggal
    $stokMasuk = RiwayatStokBahanMakanan::where('tipe', 'masuk')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(jumlah) as total'))
        ->groupBy('tanggal')
        ->pluck('total', 'tanggal');

    $stokKeluar = RiwayatStokBahanMakanan::where('tipe', 'keluar')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(jumlah) as total'))
        ->groupBy('tanggal')
        ->pluck('total', 'tanggal');

    // 4. Siapkan data akhir untuk dikirim ke view
    $chartData = [
        'labels' => $dates->map(fn ($date) => Carbon::parse($date)->format('d M')), // Format label: "09 Jun"
        'dataMasuk' => $dates->map(fn ($date) => $stokMasuk[$date] ?? 0),
        'dataKeluar' => $dates->map(fn ($date) => $stokKeluar[$date] ?? 0),
    ];
    $riwayatTerbaru = RiwayatStokBahanMakanan::with('bahanMakanan')
        ->latest() // Mengurutkan dari yang paling baru
        ->limit(5) // Kita batasi hanya 5 entri terbaru
        ->get();

        return view('ahli-gizi.logistics.index', compact('bahanMakanans', 'kategoriOptions','chartData','riwayatTerbaru'));
    }
    

    public function show(BahanMakanan $bahanMakanan)
{
    return view('ahli-gizi.logistics.bahan_makanans.show', compact('bahanMakanan')); // Kirim satu menu saja
}
    /**
     * Menampilkan form untuk menambahkan menu baru.
     */
    public function create()
    {
        return view('ahli-gizi.logistics.bahan_makanans.create');
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
        // $bahanMakanan = Menu::create([
        //     'tipe' => $request->tipe,
        //     'nama_makanan' => $request->nama_makanan,
        //     'kalori' => $request->kalori,
        // ]);

        // // Perbarui kalori_makanan untuk semua pasien dengan tipe yang sama
        // Patient::where('tipe_pasien', $request->tipe)->update([
        //     'kalori_makanan' => Menu::where('tipe', $request->tipe)->sum('kalori'),
        // ]);

        // return redirect()->route('ahli-gizi.logistics.index')->with('success', 'Menu baru berhasil ditambahkan dan kalori pasien diperbarui.');
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'protein' => 'required|integer|min:0',
            'karbohidrat' => 'required|integer|min:0',
            'total_lemak' => 'required|integer|min:0',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'kategori_bahan_masakan' => 'required|in:makanan_pokok,lauk,sayur,buah,susu',
            'stok' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('images/bahan_makanans', 'public');
        }

        BahanMakanan::create($validated);

        return redirect()->route('ahli-gizi.logistics.index')->with('success', 'Menu makanan berhasil ditambahkan!');
    }



    /**
     * Menampilkan form untuk mengedit menu.
     */
//     public function edit(Menu $bahanMakanan)
//     {
    //         $tipe_options = ['VVIP', 'VIP', 'Normal'];
    //         return view('ahli-gizi.bahan_makanans.edit', compact('menu', 'tipe_options'));
    //     }

    //     /**
    //      * Memperbarui menu.
    //      */
    //     public function update(Request $request, Menu $bahanMakanan)
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
    //                   ->where('id', '!=', $bahanMakanan->id)
    //                   ->exists();

    //     if ($exists) {
    //         return redirect()->back()->withErrors(['nama_makanan' => 'Makanan ini sudah ada untuk tipe pasien yang dipilih.'])->withInput();
    //     }

    //     // Update menu
    //     $bahanMakanan->update([
    //         'tipe' => $request->tipe,
    //         'nama_makanan' => $request->nama_makanan,
    //         'kalori' => $request->kalori,
    //     ]);

    //     // Perbarui kalori_makanan untuk semua pasien dengan tipe yang sama
    //     Patient::where('tipe_pasien', $request->tipe)->update([
    //         'kalori_makanan' => Menu::where('tipe', $request->tipe)->sum('kalori'),
    //     ]);

    //     return redirect()->route('ahli-gizi.logistics.index')->with('success', 'Menu berhasil diperbarui dan kalori pasien diperbarui.');
    // }


//     /**
//      * Menghapus menu.
//      */
//     public function destroy(Menu $bahanMakanan)
// {
//     $tipe = $bahanMakanan->tipe;
//     $bahanMakanan->delete();

//     // Perbarui kalori_makanan untuk semua pasien dengan tipe yang sama
//     Patient::where('tipe_pasien', $tipe)->update([
//         'kalori_makanan' => Menu::where('tipe', $tipe)->sum('kalori'),
//     ]);

//     return redirect()->route('ahli-gizi.logistics.index')->with('success', 'Menu berhasil dihapus dan kalori pasien diperbarui.');
// }
    public function edit(BahanMakanan $bahanMakanan)
    {
        return view('ahli-gizi.logistics.bahan_makanans.edit', compact('bahanMakanan'));
    }

    public function update(Request $request, BahanMakanan $bahanMakanan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'protein' => 'required|integer|min:0',
            'karbohidrat' => 'required|integer|min:0',
            'total_lemak' => 'required|integer|min:0',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'kategori_bahan_masakan' => 'required|in:makanan_pokok,lauk,sayur,buah,susu',
            'stok' => 'required|integer|min:0',
        ]);

        $stokLama = $bahanMakanan->stok;

        // (Kode upload gambar Anda tetap di sini, tidak perlu diubah)
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($bahanMakanan->gambar) {
                Storage::disk('public')->delete($bahanMakanan->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('images/bahan_makanans', 'public');
        }

        // Update data utama bahan makanan dengan data yang sudah divalidasi
        $bahanMakanan->update($validated);

        // 2. Ambil nilai stok BARU dari data yang sudah divalidasi
        $stokBaru = $validated['stok'];

        // 3. Hitung selisih dan buat catatan riwayat jika ada perubahan
        $selisih = $stokBaru - $stokLama;

        if ($selisih > 0) {
            // Jika stok bertambah (stok masuk)
            RiwayatStokBahanMakanan::create([
                'bahan_makanan_id' => $bahanMakanan->id,
                'tipe' => 'masuk',
                'jumlah' => $selisih,
                'satuan' => 'gram', // Sesuaikan jika Anda punya satuan dinamis
                'keterangan' => 'Penambahan stok dari form edit.',
            ]);
        } elseif ($selisih < 0) {
            // Jika stok berkurang (stok keluar)
            RiwayatStokBahanMakanan::create([
                'bahan_makanan_id' => $bahanMakanan->id,
                'tipe' => 'keluar',
                'jumlah' => abs($selisih), // Gunakan abs() untuk nilai positif
                'satuan' => 'gram', // Sesuaikan jika Anda punya satuan dinamis
                'keterangan' => 'Pengurangan stok dari form edit.',
            ]);
        }
        // Jika selisih nol, tidak ada yang dicatat.

        // ======================================================================
        // >>> AKHIR LOGIKA PENCATATAN RIWAYAT STOK
        // ======================================================================

        return redirect()->route('ahli-gizi.logistics.index')->with('success', 'Menu makanan berhasil diperbarui dan riwayat stok dicatat!');
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

    //     return redirect()->route('ahli-gizi.logistics.index')->with('success', 'Data dari CSV berhasil diimpor!');
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

            BahanMakanan::create([
                'nama' => $record['name'],
                'protein' => (int) $record['proteins'],
                'karbohidrat' => (int) $record['carbohydrate'],
                'total_lemak' => (int) $record['fat'],
                'tipe_pasien' => 'Normal', // Bisa diubah sesuai kebutuhan
                'kategori_bahan_masakan' => 'makanan_pokok', // Bisa dibuat aturan konversi
                'gambar' => $imageUrl, // Simpan URL jika valid
            ]);
        }

        return redirect()->route('ahli-gizi.logistics.index')->with('success', 'Data dari CSV berhasil diimpor!');
    }

}
