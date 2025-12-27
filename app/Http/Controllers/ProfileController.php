<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    /**
     * Handle the forced profile update (KYC).
     */
    public function updateRequired(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'phone_no' => ['required', 'string', 'max:15'], // Adjusted max len
            'bvn' => ['required', 'string', 'size:11'], // BVN usually 11 digits
            'nin' => ['nullable', 'string', 'max:20'],
            'state' => ['required', 'string', 'max:255'],
            'lga' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'pin' => ['required', 'numeric', 'digits:5'],
        ]);

        $user = $request->user();
        
        $user->forceFill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'],
            'phone_no' => $validated['phone_no'],
            'bvn' => $validated['bvn'],
            'nin' => $validated['nin'],
            'state' => $validated['state'],
            'lga' => $validated['lga'],
            'address' => $validated['address'],
            'pin' => $validated['pin'],
        ])->save();

        return Redirect::route('dashboard')->with('status', 'profile-updated-success');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'], // 2MB Max
        ]);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::delete($user->photo);
            }

            $path = $request->file('photo')->store('public/photos');
            $user->photo = Storage::url($path);
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'Photo updated successfully.');
    }

    /**
     * Update the user's transaction PIN.
     */
    public function updatePin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'pin' => ['required', 'numeric', 'digits:5', 'confirmed'],
        ]);

        $request->user()->forceFill([
            'pin' => $validated['pin'],
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'PIN updated successfully.');
    }
}
