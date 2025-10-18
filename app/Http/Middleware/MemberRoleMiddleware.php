<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MemberRoleMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->roles()->where('role_name', 'Member')->wherePivot('is_active', 1)->exists()) {
            return $next($request);
        }
        abort(403, 'Unauthorized. Only members can access this page.');
    }
}
