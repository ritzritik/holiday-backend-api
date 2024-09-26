<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check() && (Auth::guard('admin')->user()->user_type === 1 || Auth::guard('admin')->user()->user_type === 2 || Auth::guard('admin')->user()->user_type === 3)) {
            return $next($request);
        }
        return redirect('/login')->with('error', 'Unauthorized Access');
    }
}
