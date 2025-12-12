<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordOtp;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpPasswordResetController extends Controller
{
    // Step 1: show forgot-password form
    public function showForgotForm() {
        return view('auth.forgot-password');
    }

    // Step 2: send OTP
    public function sendOtp(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);

        PasswordOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        Mail::raw("Your OTP code is: $otp", function($message) use($request){
            $message->to($request->email)->subject('Password Reset OTP');
        });

        return redirect()->route('otp.verify.form', ['email' => $request->email]);
    }

    // Step 3: show OTP form
    public function showOtpForm(Request $request) {
        return view('auth.verify-otp', ['email' => $request->email]);
    }

    // Step 4: verify OTP
    public function verifyOtp(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $otpEntry = PasswordOtp::where('email', $request->email)
                        ->where('otp', $request->otp)
                        ->first();

        if(!$otpEntry || $otpEntry->isExpired()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        return redirect()->route('password.reset.form', ['email' => $request->email, 'otp' => $request->otp]);
    }

    // Step 5: show reset password form
    public function showResetForm(Request $request) {
        return view('auth.reset-password', ['email' => $request->email, 'otp' => $request->otp]);
    }

    // Step 6: update password
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $otpEntry = PasswordOtp::where('email', $request->email)
                        ->where('otp', $request->otp)
                        ->first();

        if(!$otpEntry || $otpEntry->isExpired()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete OTP after successful reset
        $otpEntry->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully. Please login.');
    }
}
