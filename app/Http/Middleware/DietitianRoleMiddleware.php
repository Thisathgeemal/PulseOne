<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DietitianRoleMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->roles()->where('role_name', 'Dietitian')->wherePivot('is_active', 1)->exists()) {
            return $next($request);
        }
        abort(403, 'Unauthorized. Only dietitians can access this page.');
    }
}
