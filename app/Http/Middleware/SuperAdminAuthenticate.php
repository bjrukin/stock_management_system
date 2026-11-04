<?php

namespace App\Http\Middleware;

use App\Support\PharmaAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless((int) PharmaAccess::admin()->role_id === 1, 403);

        return $next($request);
    }
}
