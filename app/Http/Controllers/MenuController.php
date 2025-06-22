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
        $bahanMakanans = BahanMakanan::all();
        $dietKhusus = DietKhusus::all(); // <--- Ambil semua Diet Khusus
        return view('ahli-gizi.menus.create', compact('bahanMakanans', 'dietKhusus'));
        // return view('ahli-gizi.menus.create', compact('bahanMakanans'));
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
        $bahanMakanans = BahanMakanan::all(); // Untuk dropdown bahan makanan
        $dietKhusus = DietKhusus::all(); // <--- Ambil semua Diet Khusus
        $selectedDietKhususIds = $menu->dietKhusus->pluck('id')->toArray(); // <--- Ambil yang sudah terpilih
        return view('ahli-gizi.menus.edit', compact('menu', 'bahanMakanans', 'dietKhusus', 'selectedDietKhususIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:menus,nama,' . $menu->id,
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi file gambar
            'tipe_pasien' => 'required|in:VVIP,VIP,Normal',
            'bahan_makanans' => 'required|array',
            'bahan_makanans.*.id' => 'required|exists:bahan_makanans,id',
            'bahan_makanans.*.jumlah' => 'required|numeric|min:1',
            'diet_khusus_ids' => 'nullable|array', // <--- VALIDASI UNTUK DIET KHUSUS
            'diet_khusus_ids.*' => 'exists:diet_khusus,id',
        ]);

        // Tangani gambar lama jika ada upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $menu->gambar = $request->file('gambar')->store('images/menus', 'public');
        } elseif ($request->input('gambar_exist') === 'no_change' || $request->input('gambar_exist') === 'keep') {
            // Jika tidak ada upload baru dan ingin mempertahankan gambar lama, tidak lakukan apa-apa
        } else {
            // Jika tidak ada upload baru dan gambar lama ingin dihapus (jika ada input checkbox "hapus gambar")
            if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $menu->gambar = null;
        }


        $totalProtein = 0;
        $totalKarbohidrat = 0;
        $totalLemak = 0;
        $pivotData = [];

        foreach ($request->bahan_makanans as $bahanInput) {
            $bahanModel = BahanMakanan::find($bahanInput['id']);
            if ($bahanModel) {
                $jumlah = $bahanInput['jumlah'];
                $proteinPerGram = $bahanModel->protein / 100;
                $karbohidratPerGram = $bahanModel->karbohidrat / 100;
                $lemakPerGram = $bahanModel->total_lemak / 100;

                $totalProtein += $proteinPerGram * $jumlah;
                $totalKarbohidrat += $karbohidratPerGram * $jumlah;
                $totalLemak += $lemakPerGram * $jumlah;

                $pivotData[$bahanInput['id']] = ['jumlah' => $jumlah];
            }
        }

        $menu->total_protein = round($totalProtein, 2);
        $menu->total_karbohidrat = round($totalKarbohidrat, 2);
        $menu->total_lemak = round($totalLemak, 2);
        $menu->kalori = round(($menu->total_protein * 4) + ($menu->total_karbohidrat * 4) + ($menu->total_lemak * 9), 2);

        // Update atribut Menu yang lain
        $menu->nama = $request->nama;
        $menu->deskripsi = $request->deskripsi;
        $menu->tipe_pasien = $request->tipe_pasien;
        // gambar sudah ditangani di atas

        DB::beginTransaction();
        try {
            $menu->save(); // Simpan perubahan pada model Menu

            // Sinkronkan bahan makanan (pivot table)
            $menu->bahanMakanans()->sync($pivotData);

            // Sinkronkan relasi many-to-many dietKhusus
            $menu->dietKhusus()->sync($request->input('diet_khusus_ids', [])); // <--- TAUTKAN DIET KHUSUS
            
            DB::commit();
            return redirect()->route('ahli-gizi.menus.index')->with('success', 'Menu berhasil diperbarui!'); // <--- KOREKSI ROUTE REDIRECT
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui menu: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
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
