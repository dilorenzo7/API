<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Ambil role user saat ini dan ubah ke huruf kecil (mencegah bug Kapital/Kecil)
        $userRole = strtolower(trim(auth()->user()->role));

        // 3. Gabungkan semua parameter role (baik berupa 'admin' maupun 'petugas,admin')
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowedRoles[] = strtolower(trim($r));
            }
        }

        // 4. Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}