<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller{
//     public function chartData(Request $request)
// {
//     $today = Carbon::today()->toDateString();

//     // Ambil semua menu yang dijadwalkan hari ini
//     $data = DB::table('jadwal_makanan_menu')
//         ->join('bahan_makanans', 'jadwal_makanan_menu.menu_id', '=', 'bahan_makanans.id')
//         ->join('jadwal_makanans', 'jadwal_makanan_menu.jadwal_makanan_id', '=', 'jadwal_makanans.id')
//         ->selectRaw('
//             SUM(bahan_makanans.protein) as total_protein,
//             SUM(bahan_makanans.karbohidrat) as total_karbohidrat,
//             SUM(bahan_makanans.lemak) as total_lemak
//         ')
//         ->where('jadwal_makanans.tanggal_mulai', '<=', $today)
//         ->where('jadwal_makanans.tanggal_selesai', '>=', $today)
//         ->where('jadwal_makanan_menu.tanggal', '=', $today)
//         ->first();

//     return response()->json([
//         'protein' => $data->total_protein ?? 0,
//         'karbohidrat' => $data->total_karbohidrat ?? 0,
//         'lemak' => $data->total_lemak ?? 0,
//     ]);
// }
}
