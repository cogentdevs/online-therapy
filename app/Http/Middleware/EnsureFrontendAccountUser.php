<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendAccountUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web');

        if (! $user instanceof User) {
            return redirect()->guest(route('front.login'));
        }

        if (! $user->hasRole('user', 'web')) {
            abort(403);
        }

        if (! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('front.login')->withErrors([
                'email' => 'آپ کا اکاؤنٹ غیر فعال ہے۔ براہ کرم انتظامیہ سے رابطہ کریں۔',
            ]);
        }

        return $next($request);
    }
}
