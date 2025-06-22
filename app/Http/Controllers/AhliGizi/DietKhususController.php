<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\DietKhusus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk middleware role

class DietKhususController extends Controller
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
     * Menampilkan daftar Diet Khusus.
     */
    public function index()
    {
        $dietKhusus = DietKhusus::latest()->get();
        return view('ahli-gizi.diet-khusus.index', compact('dietKhusus'));
    }

    /**
     * Menampilkan form untuk membuat Diet Khusus baru.
     */
    public function create()
    {
        return view('ahli-gizi.diet-khusus.create');
    }

    /**
     * Menyimpan Diet Khusus baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:diet_khusus,nama',
            'deskripsi' => 'nullable|string',
        ]);

        DietKhusus::create($request->all());

        return redirect()->route('ahli-gizi.diet-khusus.index')->with('success', 'Diet Khusus berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail Diet Khusus (opsional, untuk CRUD resource).
     */
    public function show(DietKhusus $dietKhusu) // Parameter harus singular dan cocok dengan route wildcard
    {
        return view('ahli-gizi.diet-khusus.show', compact('dietKhusu'));
    }

    /**
     * Menampilkan form untuk mengedit Diet Khusus.
     */
    public function edit(DietKhusus $dietKhusu) // Parameter harus singular
    {
        return view('ahli-gizi.diet-khusus.edit', compact('dietKhusu'));
    }

    /**
     * Memperbarui data Diet Khusus.
     */
    public function update(Request $request, DietKhusus $dietKhusu) // Parameter harus singular
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:diet_khusus,nama,' . $dietKhusu->id,
            'deskripsi' => 'nullable|string',
        ]);

        $dietKhusu->update($request->all());

        return redirect()->route('ahli-gizi.diet-khusus.index')->with('success', 'Diet Khusus berhasil diperbarui.');
    }

    /**
     * Menghapus Diet Khusus.
     */
    public function destroy(DietKhusus $dietKhusu) // Parameter harus singular
    {
        $dietKhusu->delete();
        return redirect()->route('ahli-gizi.diet-khusus.index')->with('success', 'Diet Khusus berhasil dihapus.');
    }
}