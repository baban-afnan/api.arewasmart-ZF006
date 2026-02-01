<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    /**
     * Display the 2FA form.
     */
    public function index()
    {
        return view('auth.two-factor');
    }

    /**
     * Validate the OTP.
     */
    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|integer',
        ]);

        $user = auth()->user();

        if ($request->two_factor_code == $user->two_factor_code) {
            
            // Check expiry
            if ($user->two_factor_expires_at->lt(now())) {
                $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
                return redirect()->back()->withErrors(['two_factor_code' => 'The two factor code has expired. Please resend.']);
            }

            $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            session(['two_factor_verified' => true]);

            return redirect()->route('dashboard');
        }

        return redirect()->back()->withErrors(['two_factor_code' => 'The two factor code you entered is incorrect.']);
    }

    /**
     * Resend the OTP.
     */
    public function resend()
    {
        $user = auth()->user();
        
        $user->update([
            'two_factor_code' => rand(100000, 999999),
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new TwoFactorCode($user->two_factor_code));
            return redirect()->back()->with('status', 'The two factor code has been resent.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Unable to send email. Please try again later.']);
        }
    }
    
    /**
     * Toggle 2FA status
     */
    public function toggle(Request $request)
    {
        $user = auth()->user();
        // Inverted Logic: 0 (false) = Active, 1 (true) = Inactive
        $user->two_factor_enabled = !$user->two_factor_enabled;
        $user->save();
        
        // If (new value is true/1) -> Inactive/Disabled
        // If (new value is false/0) -> Active/Enabled
        $status = $user->two_factor_enabled ? 'disabled' : 'enabled';
        return redirect()->back()->with('status', "Two Factor Authentication has been {$status}.");
    }
}
