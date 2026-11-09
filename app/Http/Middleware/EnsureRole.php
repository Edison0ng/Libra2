<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Pembatas peran. Pemakaian: ->middleware('role:admin') atau 'role:admin,kurir'
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $mine = $user->isAdmin() ? 'admin' : ($user->isKurir() ? 'kurir' : 'user');
        if (!in_array($mine, $roles, true)) {
            return response()->json(['message' => 'Anda tidak berhak mengakses ini.'], 403);
        }
        return $next($request);
    }
}