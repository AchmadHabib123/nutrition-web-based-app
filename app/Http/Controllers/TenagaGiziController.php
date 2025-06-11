<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FoodConsumption;
use Illuminate\Http\Request;
use Carbon\Carbon; // Untuk tanggal hari ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

class TenagaGiziController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Pastikan user terautentikasi

        // Middleware khusus untuk memeriksa role
        // $this->middleware(function ($request, $next) {
        //     // Log role user untuk debugging
        //     Log::info('Middleware TenagaGiziController: User ID ' . (Auth::id() ?? 'Guest') . ', Role: ' . (Auth::user()->role ?? 'N/A'));

        //     if (Auth::user()->role !== 'tenaga-gizi') {
        //         // Jika request adalah AJAX, kembalikan JSON 403
        //         if ($request->expectsJson()) {
        //             Log::warning('Unauthorized AJAX request for TenagaGiziController: User role is not tenaga-gizi.');
        //             return response()->json(['message' => 'Anda tidak memiliki hak akses untuk aksi ini.'], 403);
        //         }
        //         // Jika bukan AJAX, lakukan abort seperti biasa (akan mengembalikan halaman HTML 403)
        //         abort(403, 'Anda tidak memiliki hak akses untuk aksi ini.');
        //     }
        //     return $next($request);
        // })->only(['index', 'markAsDelivered']);
    }
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        
        // Ambil semua food_consumption yang berstatus 'planned' untuk hari ini
        $foodConsumptions = FoodConsumption::where('status', 'planned')
            ->whereDate('tanggal', $today)
            ->with('patient', 'menu') // Eager load pasien dan menu
            ->get();

        return view('tenaga-gizi.food-delivery.index', compact('foodConsumptions'));
    }

    /**
     * Mengubah status food_consumption menjadi 'delivered'.
     */
    public function markAsDelivered(Request $request, FoodConsumption $foodConsumption)
    {
        Log::info('MarkAsDelivered method called for FoodConsumption ID: ' . $foodConsumption->id);
        Log::info('User role: ' . Auth::user()->role);
        Log::info('Current food status: ' . $foodConsumption->status);

        try {
            // Cek otorisasi menggunakan Policy
            // Policy juga harus memastikan user->role adalah 'tenaga-gizi' dan status adalah 'planned'
            $this->authorize('markAsDelivered', $foodConsumption);

            $foodConsumption->status = 'delivered';
            $foodConsumption->save();

            Log::info('FoodConsumption ID ' . $foodConsumption->id . ' status successfully updated to delivered.');
            return response()->json(['message' => 'Makanan berhasil ditandai sebagai diantar.'], 200);

        } catch (AuthorizationException $e) {
            Log::warning('Policy authorization failed for FoodConsumption ID ' . $foodConsumption->id . ': ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            Log::error('Failed to update FoodConsumption ID ' . $foodConsumption->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }
}
