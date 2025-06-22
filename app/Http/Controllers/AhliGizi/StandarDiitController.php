<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\StandarDiit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk middleware role

class StandarDiitController extends Controller
{
    public function __construct()
    {
        // Pastikan hanya role 'ahli-gizi' yang bisa mengakses
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'ahli-gizi') {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Menampilkan daftar Standar Diit.
     */
    public function index()
    {
        $standarDiits = StandarDiit::latest()->get();
        return view('ahli-gizi.standar-diit.index', compact('standarDiits'));
    }

    /**
     * Menampilkan form untuk membuat Standar Diit baru.
     */
    public function create()
    {
        // Anda mungkin perlu mengirim data BahanMakanan untuk form daftar terlarang
        // $bahanMakanans = \App\Models\BahanMakanan::all();
        // return view('ahli-gizi.standar-diit.create', compact('bahanMakanans'));
        return view('ahli-gizi.standar-diit.create');
    }

    /**
     * Menyimpan Standar Diit baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:standar_diits,nama',
            'min_kalori' => 'nullable|numeric|min:0',
            'max_kalori' => 'nullable|numeric|min:0|gte:min_kalori',
            'target_protein_ratio' => 'nullable|numeric|min:0|max:1', // Ratio 0-1
            'target_karbohidrat_ratio' => 'nullable|numeric|min:0|max:1',
            'target_lemak_ratio' => 'nullable|numeric|min:0|max:1',
            'kategori_bahan_terlarang' => 'nullable|array', // Akan disimpan sebagai JSON
            'bahan_makanan_terlarang_ids' => 'nullable|array', // Akan disimpan sebagai JSON (ID)
            'bahan_makanan_terlarang_ids.*' => 'integer|exists:bahan_makanans,id', // Validasi setiap ID
            'pola_porsi_kategori' => 'nullable|json',
            'catatan' => 'nullable|string',
        ]);

        StandarDiit::create($request->all());

        return redirect()->route('ahli-gizi.standar-diit.index')->with('success', 'Standar Diit berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail Standar Diit (opsional).
     */
    public function show(StandarDiit $standarDiit) // Parameter harus singular
    {
        // return view('ahli-gizi.standar-diit.show', compact('standarDiit'));
        // Biasanya detail ditampilkan di form edit atau di daftar index, jadi show() sering diabaikan
        return redirect()->route('ahli-gizi.standar-diit.edit', $standarDiit->id);
    }

    /**
     * Menampilkan form untuk mengedit Standar Diit.
     */
    public function edit(StandarDiit $standarDiit) // Parameter harus singular
    {
        // Anda mungkin perlu mengirim data BahanMakanan untuk form daftar terlarang
        // $bahanMakanans = \App\Models\BahanMakanan::all();
        // return view('ahli-gizi.standar-diit.edit', compact('standarDiit', 'bahanMakanans'));
        return view('ahli-gizi.standar-diit.edit', compact('standarDiit'));
    }

    /**
     * Memperbarui data Standar Diit.
     */
    public function update(Request $request, StandarDiit $standarDiit) // Parameter harus singular
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:standar_diits,nama,' . $standarDiit->id,
            'min_kalori' => 'nullable|numeric|min:0',
            'max_kalori' => 'nullable|numeric|min:0|gte:min_kalori',
            'target_protein_ratio' => 'nullable|numeric|min:0|max:1',
            'target_karbohidrat_ratio' => 'nullable|numeric|min:0|max:1',
            'target_lemak_ratio' => 'nullable|numeric|min:0|max:1',
            'kategori_bahan_terlarang' => 'nullable|array',
            'bahan_makanan_terlarang_ids' => 'nullable|array',
            'bahan_makanan_terlarang_ids.*' => 'integer|exists:bahan_makanans,id',
            'pola_porsi_kategori' => 'nullable|json',
            'catatan' => 'nullable|string',
        ]);

        $standarDiit->update($request->all());

        return redirect()->route('ahli-gizi.standar-diit.index')->with('success', 'Standar Diit berhasil diperbarui.');
    }

    /**
     * Menghapus Standar Diit.
     */
    public function destroy(StandarDiit $standarDiit) // Parameter harus singular
    {
        // Pastikan tidak ada pasien yang masih merujuk standar diit ini
        if ($standarDiit->patients()->exists()) {
            return redirect()->back()->with('error', 'Standar Diit tidak dapat dihapus karena masih digunakan oleh pasien.');
        }

        $standarDiit->delete();
        return redirect()->route('ahli-gizi.standar-diit.index')->with('success', 'Standar Diit berhasil dihapus.');
    }
}