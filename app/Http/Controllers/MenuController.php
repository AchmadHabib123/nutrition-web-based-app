<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanMakanan;
use App\Models\Menu;

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
        return view('ahli-gizi.menus.create', compact('bahanMakanans'));
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
            'bahan_makanans.*.id' => 'nullable|exists:bahan_makanans,id',
            'bahan_makanans.*.jumlah' => 'nullable|numeric|min:1',

        ]);
    
        $data = $request->only(['nama', 'deskripsi', 'tipe_pasien']);
    
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('images/menus', 'public');
        }
        $totalProtein = 0;
        $totalKarbohidrat = 0;
        $totalLemak = 0;
        $pivotData = [];

        foreach ($request->bahan_makanans as $bahan) {
            // if (!isset($bahan['selected'])) continue;

            $bahanModel = BahanMakanan::find($bahan['id']);
            if ($bahanModel) {
                $jumlah = $bahan['jumlah'];
                $proteinPerGram = $bahanModel->protein / 100;
                $karbohidratPerGram = $bahanModel->karbohidrat / 100;
                $lemakPerGram = $bahanModel->total_lemak / 100; // Asumsi total_lemak juga per 100gr

                $totalProtein += $proteinPerGram * $jumlah;
                $totalKarbohidrat += $karbohidratPerGram * $jumlah;
                $totalLemak += $lemakPerGram * $jumlah;

                $pivotData[$bahan['id']] = ['jumlah' => $jumlah];
            }
        }


        // Tambahkan ke $data
        $data['total_protein'] = $totalProtein;
        $data['total_karbohidrat'] = $totalKarbohidrat;
        $data['total_lemak'] = $totalLemak;


    
        $menu = Menu::create($data);
    
        // Hubungkan bahan makanan
        $pivotData = [];
        foreach ($request->bahan_makanans as $bahan) {
            // Lewati jika tidak dipilih
            if (!isset($bahan['selected']) || empty($bahan['jumlah'])) continue;

            $pivotData[$bahan['id']] = [
                'jumlah' => $bahan['jumlah']
            ];
        }
        $menu->bahanMakanans()->sync($pivotData);
    
        return redirect()->route('ahli-gizi.menus.create')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
