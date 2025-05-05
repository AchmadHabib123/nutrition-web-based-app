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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bahanMakanans = BahanMakanan::all();
        return view('admin.menus.create', compact('bahanMakanans'));
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
        ]);
    
        $data = $request->only(['nama', 'deskripsi', 'tipe_pasien']);
    
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('images/menus', 'public');
        }
        $totalProtein = 0;
        $totalKarbohidrat = 0;
        $totalLemak = 0;

        foreach ($request->bahan_makanans as $bahan) {
            $bahanModel = BahanMakanan::find($bahan['id']);
            if ($bahanModel) {
                $jumlah = $bahan['jumlah'];
                $totalProtein += $bahanModel->protein * $jumlah;
                $totalKarbohidrat += $bahanModel->karbohidrat * $jumlah;
                $totalLemak += $bahanModel->total_lemak * $jumlah;
            }
        }

        // Tambahkan ke $data
        $data['protein'] = $totalProtein;
        $data['karbohidrat'] = $totalKarbohidrat;
        $data['total_lemak'] = $totalLemak;

    
        $menu = Menu::create($data);
    
        // Hubungkan bahan makanan
        $pivotData = [];
        foreach ($request->bahan_makanans as $bahan) {
            $pivotData[$bahan['id']] = ['jumlah' => $bahan['jumlah']];
        }
        $menu->bahanMakanans()->sync($pivotData);
    
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan!');
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
