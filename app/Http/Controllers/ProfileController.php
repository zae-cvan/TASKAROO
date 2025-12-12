<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\PasswordOtp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;



class ProfileController extends Controller
{
    /**
     * Display the user's profile page using Taskaroo layout.
     */
    public function edit(Request $request): View
    {
        $user = $request->user(); // get current user
        return view('profile', compact('user')); // use profile.blade.php
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle profile photo removal
        if ($request->input('remove_profile_photo') == 1) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = null;
        }
        // Handle profile photo upload if present
        elseif ($request->hasFile('profile_photo')) {
            try {
                $file = $request->file('profile_photo');
                $path = $file->store('profile_photos', 'public');

                // Optionally delete old photo (if exists)
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $data['profile_photo'] = $path;
            } catch (\Throwable $e) {
                // swallow; still allow profile update
            }
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile')->with('status', 'Profile updated!');
    }

    /**
     * Delete the user's account.
     */
  public function destroy(Request $request): RedirectResponse
{
    $request->validate([
        'password' => ['required', 'current_password'],
        'reason' => ['nullable', 'string', 'max:500'],
    ]);

    $user = $request->user();

    $reason = $request->input('reason');
    // Optional: log reason here

    Auth::logout();
    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/login')->with('status', 'Your account has been deleted.');
}


    
   public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $user = auth()->user();
    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Password updated successfully!');
}

    // Send OTP for password change
    public function sendPasswordOtp(Request $request)
    {
        $user = $request->user();
        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(3); // Set OTP expiry to 3 minutes

        PasswordOtp::updateOrCreate(
            ['email' => $user->email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        Mail::raw("Your OTP code is: $otp", function($message) use($user){
            $message->to($user->email)->subject('Change Password OTP');
        });

        return response()->json(['message' => 'OTP sent to your email.']);

    }

    // Verify OTP from profile
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = $request->user();

        $otpEntry = PasswordOtp::where('email', $user->email)
                        ->where('otp', $request->otp)
                        ->first();

        if(!$otpEntry || $otpEntry->isExpired()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        return back()->with('otp_verified', true);
    }

   // Update password after OTP verification
public function updatePasswordWithOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:6',
        'password' => 'required|min:8|confirmed',
    ]);

    $user = $request->user();

    $otpEntry = PasswordOtp::where('email', $user->email)
                    ->where('otp', $request->otp)
                    ->first();

    if(!$otpEntry || $otpEntry->isExpired()) {
        return response()->json(['error' => 'Invalid or expired OTP'], 422);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    $otpEntry->delete();

    return response()->json(['message' => 'Password changed successfully.']);
}
}

