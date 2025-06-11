<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    // public function handle(Request $request, Closure $next, $role)
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login');
    //     }

    //     $user = Auth::user();

    //     if ($user->role != $role) {
    //         return abort(403, 'Unauthorized action.');
    //     }

    //     return $next($request);
    // }
    public function handle(Request $request, Closure $next, $role)
    {
        // Log untuk debugging middleware role
        Log::info('RoleMiddleware: Path=' . $request->path() . ', Required Role=' . $role . ', User ID=' . (Auth::id() ?? 'Guest') . ', User Role=' . (Auth::user()->role ?? 'N/A') . ', Is AJAX=' . ($request->expectsJson() ? 'Yes' : 'No'));

        if (!Auth::check()) {
            // Jika tidak login, dan ini AJAX, kembalikan JSON 401 Unauthenticated
            if ($request->expectsJson()) {
                Log::warning('RoleMiddleware: Unauthorized (Not logged in) AJAX request for role ' . $role);
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            // Jika tidak login dan bukan AJAX, redirect ke login
            Log::warning('RoleMiddleware: Unauthenticated (Not logged in) non-AJAX request for role ' . $role . '. Redirecting to login.');
            return redirect('/login'); // Ganti dengan route login Anda jika berbeda
        }

        $user = Auth::user();

        if ($user->role != $role) {
            // Jika role tidak sesuai, dan ini AJAX, kembalikan JSON 403 Forbidden
            if ($request->expectsJson()) {
                Log::warning('RoleMiddleware: Forbidden (Role Mismatch) AJAX request. Expected: ' . $role . ', Actual: ' . $user->role);
                return response()->json(['message' => 'Anda tidak memiliki hak akses untuk aksi ini.'], 403);
            }
            // Jika bukan AJAX, lakukan abort seperti biasa (akan mengembalikan halaman HTML 403)
            Log::warning('RoleMiddleware: Forbidden (Role Mismatch) non-AJAX request. Aborting 403. Expected: ' . $role . ', Actual: ' . $user->role);
            abort(403, 'Anda tidak memiliki hak akses untuk aksi ini.');
        }

        // Jika semua lolos, lanjutkan ke request berikutnya
        return $next($request);
    }
}
