<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleUsernameSpaces
{
    public function handle(Request $request, Closure $next)
    {
        // Jika username mengandung spasi, replace dengan -
        $username = $request->route('username');
        if ($username && str_contains($username, ' ')) {
            $username = str_replace(' ', '-', $username);
            $request->route()->setParameter('username', $username);
        }
        
        return $next($request);
    }
}