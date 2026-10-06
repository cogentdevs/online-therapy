<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('admin', 'admin/*')) {
            $user = $request->user('web');
            if ($user !== null && $user->hasRole('user', 'web') && ! $user->is_active) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('front.login')->withErrors([
                    'email' => 'آپ کا اکاؤنٹ غیر فعال ہے۔ براہ کرم انتظامیہ سے رابطہ کریں۔',
                ]);
            }
        }

        return $next($request);
    }
}
