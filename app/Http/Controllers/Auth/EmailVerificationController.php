<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\EmailVerificationRequest;
use Otp;




class EmailVerificationController extends Controller
{
    private $otp;
    public function __construct()
    {
        $this->otp = new Otp;
    }
    public function email_verification(EmailVerificationRequest $request){
        $otpValidation = $this->otp->validate($request->email, $request->otp);

    \Log::info('OTP Validation Result:', ['status' => $otpValidation->status]);

    if (!$otpValidation->status) {
        return response()->json(['error' => 'Invalid OTP'], 401);
    }

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    if ($user) {
        // Update email verification timestamp
        $user->update(['email_verified_at' => now()]);
        return response()->json(['success' => true], 200);
    } else {
        return response()->json(['error' => 'User not found'], 404);
    }
    }
}
 