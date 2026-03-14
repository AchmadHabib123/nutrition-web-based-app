<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\SiklusMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk middleware role
use Illuminate\Validation\Rule;

class SiklusMenuController extends Controller
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
     * Menampilkan daftar Siklus Menu.
     */
    public function index()
    {
        $siklusMenus = SiklusMenu::with('menu')->orderBy('hari_siklus')->orderBy('waktu_makan')->get();
        return view('ahli-gizi.siklus-menu.index', compact('siklusMenus'));
    }

    /**
     * Menampilkan form untuk membuat entri Siklus Menu baru.
     */
    public function create()
    {
        $menus = Menu::all(); // Ambil semua Menu untuk dropdown
        $waktuMakanOptions = ['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'];
        $tipePasienOptions = ['VVIP', 'VIP', 'Normal'];
        return view('ahli-gizi.siklus-menu.create', compact('menus', 'waktuMakanOptions', 'tipePasienOptions'));
    }

    /**
     * Menyimpan entri Siklus Menu baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hari_siklus' => 'required|integer|min:1|max:11',
            'waktu_makan' => ['required', Rule::in(['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'])],
            'tipe_pasien' => ['required', Rule::in(['VVIP', 'VIP', 'Normal'])],
            'menu_id' => 'required|exists:menus,id',
            // Rule unique gabungan untuk hari_siklus, waktu_makan, tipe_pasien
            Rule::unique('siklus_menus')->where(function ($query) use ($request) {
                return $query->where('hari_siklus', $request->hari_siklus)
                             ->where('waktu_makan', $request->waktu_makan)
                             ->where('tipe_pasien', $request->tipe_pasien);
            })->ignore($request->id), // Untuk update, agar tidak konflik dengan dirinya sendiri
        ]);

        SiklusMenu::create($request->all());

        return redirect()->route('ahli-gizi.siklus-menu.index')->with('success', 'Entri Siklus Menu berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail Siklus Menu (opsional).
     */
    public function show(SiklusMenu $siklusMenu) // Parameter harus singular
    {
        $siklusMenu->load('menu');
        return view('ahli-gizi.siklus-menu.show', compact('siklusMenu'));
    }

    /**
     * Menampilkan form untuk mengedit entri Siklus Menu.
     */
    public function edit(SiklusMenu $siklusMenu) // Parameter harus singular
    {
        $menus = Menu::all(); // Ambil semua Menu untuk dropdown
        $waktuMakanOptions = ['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'];
        $tipePasienOptions = ['VVIP', 'VIP', 'Normal'];
        return view('ahli-gizi.siklus-menu.edit', compact('siklusMenu', 'menus', 'waktuMakanOptions', 'tipePasienOptions'));
    }

    /**
     * Memperbarui entri Siklus Menu.
     */
    public function update(Request $request, SiklusMenu $siklusMenu) // Parameter harus singular
    {
        $request->validate([
            'hari_siklus' => 'required|integer|min:1|max:11',
            'waktu_makan' => ['required', Rule::in(['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'])],
            'tipe_pasien' => ['required', Rule::in(['VVIP', 'VIP', 'Normal'])],
            'menu_id' => 'required|exists:menus,id',
            Rule::unique('siklus_menus')->where(function ($query) use ($request) {
                return $query->where('hari_siklus', $request->hari_siklus)
                             ->where('waktu_makan', $request->waktu_makan)
                             ->where('tipe_pasien', $request->tipe_pasien);
            })->ignore($siklusMenu->id), // Ignore ID saat update
        ]);

        $siklusMenu->update($request->all());

        return redirect()->route('ahli-gizi.siklus-menu.index')->with('success', 'Entri Siklus Menu berhasil diperbarui.');
    }

    /**
     * Menghapus entri Siklus Menu.
     */
    public function destroy(SiklusMenu $siklusMenu) // Parameter harus singular
    {
        $siklusMenu->delete();
        return redirect()->route('ahli-gizi.siklus-menu.index')->with('success', 'Entri Siklus Menu berhasil dihapus.');
    }
}
