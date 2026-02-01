<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCode;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Inverted Logic: 0 (false) = Active/Enabled
        if (auth()->check() && !$user->two_factor_enabled) {
            
            // If already verified in session, allow access
            if ($request->session()->has('two_factor_verified')) {
                 return $next($request);
            }

            // If OTP exists and is not expired
            if ($user->two_factor_code) {
                 // If not on dashboard, redirect to dashboard to show modal
                 if (!$request->routeIs('dashboard') && !$request->is('verify*') && !$request->is('logout')) {
                     return redirect()->route('dashboard');
                 }
            } else {
                // Generate new code
                $user->update([
                    'two_factor_code' => rand(100000, 999999),
                    'two_factor_expires_at' => now()->addMinutes(10),
                ]);

                try {
                    Mail::to($user->email)->send(new TwoFactorCode($user->two_factor_code));
                } catch (\Exception $e) {
                     // Log error
                }
                
                if (!$request->routeIs('dashboard') && !$request->is('verify*') && !$request->is('logout')) {
                    return redirect()->route('dashboard');
                }
            }
             
             // Allow request to proceed (Dashboard will render modal via app.blade.php)
             return $next($request);
        }

        return $next($request);
    }
}
