<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('settings.services', [
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

        return Redirect::route('settings.services')->with('status', 'profile-updated');
    }

    /**
     * Update required profile information for onboarding.
     */
    public function updateRequired(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255|min:2',
            'last_name' => 'required|string|max:255|min:2',
            'middle_name' => 'nullable|string|max:255',
            'phone_no' => 'required|string|max:15|min:10|regex:/^[0-9+\-\s()]+$/|unique:users,phone_no,' . $user->id,
            'lga' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'bvn' => 'required|digits:11|unique:users,bvn,' . $user->id,
            'pin' => 'required|digits:5',
            'termsCheck' => 'required|string|max:500', 
        ], [
            'pin.required' => 'Transaction PIN is required.',
            'pin.digits' => 'PIN must be exactly 5 digits.',
        ]);

        try {
            $user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'],
                'phone_no' => $validated['phone_no'],
                'lga' => $validated['lga'],
                'state' => $validated['state'],
                'address' => $validated['address'],
                'bvn' => $validated['bvn'],
                'pin' => bcrypt($validated['pin']), 
                'profile_completed' => true, 
            ]);

            return redirect()->route('dashboard')->with('success', 'Account successfully! Welcome aboard! 🎉');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1062) {
                return redirect()->back()->with('error', 'The provided data (phone number or BVN) is already registered to another account.');
            }
            return redirect()->back()->with('error', 'Failed to update profile due to a database error. Please try again later.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Upload or update profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        try {
            // ✅ Delete old photo if exists (with improved logic)
            if ($user->photo) {
                $this->deleteOldProfilePhoto($user->photo);
            }

            // ✅ Store new image using Laravel's Storage facade
            $file = $request->file('photo');
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in storage/app/public/uploads/profile_photos
            $path = $file->storeAs('uploads/profile_photos', $fileName, 'public');
            
            // ✅ Build full HTTP link
            $fullUrl = Storage::disk('public')->url($path);

            // ✅ Save to database
            $user->update([
                'photo' => $fullUrl,
            ]);

            return back()->with('status', '✅ Profile photo updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to update profile photo: ' . $e->getMessage());
        }
    }

    /**
     * Delete old profile photo with proper handling
     */
    private function deleteOldProfilePhoto(string $photoUrl): void
    {
        // Skip external URLs (like gravatar)
        if (Str::startsWith($photoUrl, 'http') && !Str::contains($photoUrl, '/storage/')) {
            return;
        }

        try {
            // If it's a storage URL, extract the path
            if (Str::contains($photoUrl, '/storage/')) {
                // Remove the base URL to get the storage path
                $baseUrl = config('app.url') . '/storage/';
                $path = str_replace($baseUrl, '', $photoUrl);
                Storage::disk('public')->delete($path);
            } 
            // If it's already a storage path (not full URL)
            elseif (Storage::disk('public')->exists($photoUrl)) {
                Storage::disk('public')->delete($photoUrl);
            }
            // For old-style public/uploads paths
            elseif (Str::contains($photoUrl, '/uploads/')) {
                // Extract filename from URL
                $filename = basename($photoUrl);
                Storage::disk('public')->delete('uploads/profile_photos/' . $filename);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Log::error('Failed to delete old profile photo: ' . $e->getMessage());
        }
    }

    /**
     * Check if user needs to complete profile (for modal trigger)
     */
    public function checkProfileCompletion(): bool
    {
        $user = Auth::user();
        
        // Define required fields that must be filled
        $requiredFields = [
            'first_name', 'last_name', 'phone_no', 'lga', 
            'state', 'address', 'bvn'
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * Update additional profile information (only if not already set).
     */
    public function updateAdditionalInfo(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'nin' => 'nullable|digits:11|unique:users,nin,' . $user->id,
            'bvn' => 'nullable|digits:11|unique:users,bvn,' . $user->id,
            'state' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $updates = [];

        // Only allow updating if the field is currently empty in the database
        if (empty($user->business_name) && !empty($validated['business_name'])) {
            $updates['business_name'] = $validated['business_name'];
        }
        if (empty($user->nin) && !empty($validated['nin'])) {
            $updates['nin'] = $validated['nin'];
        }
        if (empty($user->bvn) && !empty($validated['bvn'])) {
            $updates['bvn'] = $validated['bvn'];
        }
        if (empty($user->state) && !empty($validated['state'])) {
            $updates['state'] = $validated['state'];
        }
        if (empty($user->lga) && !empty($validated['lga'])) {
            $updates['lga'] = $validated['lga'];
        }
        if (empty($user->address) && !empty($validated['address'])) {
            $updates['address'] = $validated['address'];
        }

        if (!empty($updates)) {
            $user->update($updates);
            return back()->with('status', 'Additional information updated successfully.');
        }

        return back()->with('info', 'No changes made or fields are already set.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Update the user's transaction PIN.
     */
    public function updatePin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'pin' => ['required', 'digits:5', 'confirmed'],
        ]);

        $request->user()->update([
            'pin' => bcrypt($validated['pin']),
        ]);

        return back()->with('status', 'pin-updated');
    }
}