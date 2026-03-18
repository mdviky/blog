<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
        /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check 1: is the user logged in?
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check 2: is the user an admin?
        if (auth()->user()->role !== 'admin') {
            return redirect('/')->with('error', 'Access denied. Admins only.');
        }

        // Both checks passed — allow the request through
        return $next($request);
    }
}