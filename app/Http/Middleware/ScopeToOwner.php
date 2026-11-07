<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Untuk pengguna biasa: paksa user_id / peminjam_id pada request menjadi id
 * miliknya sendiri, sehingga tidak bisa melihat/mengubah data orang lain.
 * Admin dan kurir tidak dibatasi.
 */
class ScopeToOwner
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && !$user->isKurir()) {
            $request->merge([
                'user_id'     => $user->id,
                'peminjam_id' => $user->id,
            ]);
            $request->query->set('user_id', $user->id);
            $request->query->set('peminjam_id', $user->id);
        }
        return $next($request);
    }
}