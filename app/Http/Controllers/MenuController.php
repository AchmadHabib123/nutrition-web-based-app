<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanMakanan;
use App\Models\Menu;
use App\Models\DietKhusus; // <--- PASTIKAN INI DIIMPOR
use Illuminate\Support\Facades\Auth; // <--- PASTIKAN INI DIIMPOR
use Illuminate\Support\Facades\DB; // <--- PASTIKAN INI DIIMPOR
use Illuminate\Support\Facades\Log; // <--- PASTIKAN INI DIIMPOR
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with('bahanMakanans')->latest()->get();
        return view('ahli-gizi.menus.index', compact('menus'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bahanMakanans = BahanMakanan::all(); // Semua bahan makanan untuk dropdown
        $dietKhusus = DietKhusus::all(); // Semua diet khusus untuk multi-select
        
        // Untuk form create, tidak ada menu yang sudah ada, jadi null atau array kosong
        $menu = null; // Agar view tidak error saat akses $menu->properti
        $selectedDietKhususIds = []; // Tidak ada yang terpilih
        $selectedBahanMakanansData = []; // Tidak ada bahan makanan awal
        
        return view('ahli-gizi.menus.create', compact('menu', 'bahanMakanans', 'dietKhusus', 'selectedDietKhususIds', 'selectedBahanMakanansData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'bahan_makanans' => 'required|array',
            'bahan_makanans.*.id' => 'required|exists:bahan_makanans,id',
            'bahan_makanans.*.jumlah' => 'required|numeric|min:1',
            'diet_khusus_ids' => 'nullable|array', // <--- VALIDASI UNTUK DIET KHUSUS
            'diet_khusus_ids.*' => 'exists:diet_khusus,id',

        ]);
    
        $data = $request->only(['nama', 'deskripsi', 'tipe_pasien']);
        $data['gambar'] = null;
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('images/menus', 'public');
        }
        $totalProtein = 0;
        $totalKarbohidrat = 0;
        $totalLemak = 0;
        $pivotData = [];

        foreach ($request->bahan_makanans as $bahanInput) {
            // if (!isset($bahan['selected'])) continue;

            $bahanModel = BahanMakanan::find($bahanInput['id']);
            if ($bahanModel) {
                $jumlah = $bahanInput['jumlah'];
                $proteinPerGram = $bahanModel->protein / 100;
                $karbohidratPerGram = $bahanModel->karbohidrat / 100;
                $lemakPerGram = $bahanModel->total_lemak / 100; // Asumsi total_lemak juga per 100gr

                $totalProtein += $proteinPerGram * $jumlah;
                $totalKarbohidrat += $karbohidratPerGram * $jumlah;
                $totalLemak += $lemakPerGram * $jumlah;

                $pivotData[$bahanInput['id']] = ['jumlah' => $jumlah];
            }
        }


        // Tambahkan ke $data
        $data['total_protein'] = $totalProtein;
        $data['total_karbohidrat'] = $totalKarbohidrat;
        $data['total_lemak'] = $totalLemak;
        $data['kalori'] = round(($data['total_protein'] * 4) + ($data['total_karbohidrat'] * 4) + ($data['total_lemak'] * 9), 2);
        DB::beginTransaction();
        try {
            $menu = Menu::create($data); // Buat menu dengan data termasuk total nutrisi
        
            // Sinkronkan bahan makanan (pivot table bahan_menu)
            $menu->bahanMakanans()->sync($pivotData);

            // Sinkronkan relasi many-to-many dietKhusus
            $menu->dietKhusus()->sync($request->input('diet_khusus_ids', [])); // <--- TAUTKAN DIET KHUSUS
            
            DB::commit();
            return redirect()->route('ahli-gizi.menus.index')->with('success', 'Menu berhasil ditambahkan!'); // <--- KOREKSI ROUTE REDIRECT
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan menu: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            // Hapus gambar yang mungkin sudah terupload jika transaksi gagal
            if (isset($data['gambar']) && Storage::disk('public')->exists($data['gambar'])) {
                Storage::disk('public')->delete($data['gambar']);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan menu: ' . $e->getMessage());
        }
    
        // $menu = Menu::create($data);
    
        // // Hubungkan bahan makanan
        // $pivotData = [];
        // foreach ($request->bahan_makanans as $bahan) {
        //     // Lewati jika tidak dipilih
        //     if (!isset($bahan['selected']) || empty($bahan['jumlah'])) continue;

        //     $pivotData[$bahan['id']] = [
        //         'jumlah' => $bahan['jumlah']
        //     ];
        // }
        // $menu->bahanMakanans()->sync($pivotData);
    
        // return redirect()->route('ahli-gizi.menus.create')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        $menu->load('bahanMakanans', 'dietKhusus');
        return view('ahli-gizi.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $bahanMakanans = BahanMakanan::all(); // Semua bahan makanan untuk dropdown
        $dietKhusus = DietKhusus::all(); // Semua diet khusus untuk multi-select
        
        $selectedDietKhususIds = $menu->dietKhusus->pluck('id')->toArray(); // ID Diet Khusus yang sudah terpilih

        // Siapkan data bahan makanan yang sudah terpilih untuk form edit
        $selectedBahanMakanansData = $menu->bahanMakanans->map(function($bahan) {
            return [
                'id' => $bahan->id,
                'jumlah' => $bahan->pivot->jumlah,
                'selected' => true // Tandai sudah terpilih
            ];
        })->toArray();
        // Gabungkan dengan old input jika ada validasi gagal
        if (old('bahan_makanans')) {
            // Gunakan array_replace_recursive untuk menggabungkan old input dengan data existing,
            // atau cukup ambil old input jika itu yang paling baru setelah gagal validasi
            $processedOldBahan = [];
            foreach (old('bahan_makanans') as $bahanId => $data) {
                // Pastikan hanya bahan yang sebelumnya dicentang yang diproses dari old()
                if (isset($data['selected']) && $data['selected'] == '1') {
                    $processedOldBahan[] = [
                        'id' => $bahanId,
                        'jumlah' => $data['jumlah'] ?? '', // Ambil jumlah dari old input
                        'selected' => true
                    ];
                }
            }
            $selectedBahanMakanansData = $processedOldBahan;
        }

        return view('ahli-gizi.menus.edit', compact('menu', 'bahanMakanans', 'dietKhusus', 'selectedDietKhususIds', 'selectedBahanMakanansData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:menus,nama,' . $menu->id,
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'bahan_makanans' => 'nullable|array',
            'bahan_makanans.*.selected' => 'nullable|boolean',
            'bahan_makanans.*.id' => 'required_with:bahan_makanans.*.selected|exists:bahan_makanans,id',
            'bahan_makanans.*.jumlah' => 'required_with:bahan_makanans.*.selected|numeric|min:1',
            'diet_khusus_ids' => 'nullable|array',
            'diet_khusus_ids.*' => 'exists:diet_khusus,id',
        ]);

        $data = $request->only(['nama', 'deskripsi', 'tipe_pasien']);
        
        // Tangani gambar lama jika ada upload gambar baru
        if ($request->hasFile('gambar')) {
            if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('images/menus', 'public');
        } elseif ($request->boolean('delete_gambar')) { // Cek checkbox "hapus gambar saat ini"
            if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $data['gambar'] = null;
        } else {
            $data['gambar'] = $menu->gambar; // Pertahankan gambar lama jika tidak ada perubahan
        }

        $totalProtein = 0;
        $totalKarbohidrat = 0;
        $totalLemak = 0;
        $pivotData = [];

        if ($request->has('bahan_makanans') && is_array($request->bahan_makanans)) {
            foreach ($request->bahan_makanans as $bahanId => $bahanData) {
                if (isset($bahanData['selected']) && $bahanData['selected'] == '1' && isset($bahanData['jumlah']) && $bahanData['jumlah'] >= 1) {
                    $bahanModel = BahanMakanan::find($bahanId);
                    if ($bahanModel) {
                        $jumlah = (float)$bahanData['jumlah'];
                        $totalProtein += ($bahanModel->protein / 100) * $jumlah;
                        $totalKarbohidrat += ($bahanModel->karbohidrat / 100) * $jumlah;
                        $totalLemak += ($bahanModel->total_lemak / 100) * $jumlah;
                        $pivotData[$bahanId] = ['jumlah' => $jumlah];
                    }
                }
            }
        }
        
        $data['total_protein'] = round($totalProtein, 2);
        $data['total_karbohidrat'] = round($totalKarbohidrat, 2);
        $data['total_lemak'] = round($totalLemak, 2);
        $data['kalori'] = round(($data['total_protein'] * 4) + ($data['total_karbohidrat'] * 4) + ($data['total_lemak'] * 9), 2);

        DB::beginTransaction();
        try {
            $menu->update($data); // Update atribut Menu
            $menu->bahanMakanans()->sync($pivotData);
            $menu->dietKhusus()->sync($request->input('diet_khusus_ids', []));
            
            DB::commit();
            return redirect()->route('ahli-gizi.menus.index')->with('success', 'Menu berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui menu: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            // Jika ada gambar baru terupload tapi update gagal, hapus gambar baru tersebut
            if (isset($data['gambar']) && Storage::disk('public')->exists($data['gambar']) && $data['gambar'] !== $menu->gambar) {
                Storage::disk('public')->delete($data['gambar']);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui menu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        if ($menu->jadwalMakanans()->exists()) {
            return redirect()->back()->with('error', 'Menu tidak dapat dihapus karena masih digunakan di jadwal makanan.');
       }

       // Hapus gambar terkait jika ada
       if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
           Storage::disk('public')->delete($menu->gambar);
       }

       $menu->delete();
       return redirect()->route('ahli-gizi.menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}
