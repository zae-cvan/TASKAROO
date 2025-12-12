<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\OneTimePasswords\OneTimePassword;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Generate OTP, valid for 5 minutes
        $otp = OneTimePassword::generate(now()->addMinutes(5));

        // Send OTP via Gmail SMTP
        Mail::raw("Your Taskaroo OTP is: {$otp->plainTextToken}", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Taskaroo OTP Code');
        });

        return response()->json([
            'message' => 'OTP sent successfully'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $isValid = OneTimePassword::isValid($request->otp);

        if ($isValid) {
            return response()->json(['message' => 'OTP verified successfully']);
        }

        return response()->json(['message' => 'Invalid OTP'], 422);
    }
}
