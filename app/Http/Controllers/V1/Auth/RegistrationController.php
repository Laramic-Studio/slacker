<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\V1\Auth\RegistrationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public RegistrationService $registrationService) {}


public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check referral code if provided
        $referredBy = null;
        if ($request->has('referral_code') && $request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referred_by' => $referredBy,
            'email_verified_at' => null, // Email not verified yet
        ]);

        // Generate referral code for new user
        $user->generateReferralCode();

        // Create referral record if user was referred
        if ($referredBy) {
            \App\Models\Referral::create([
                'referrer_id' => $referredBy,
                'referred_id' => $user->id,
                'status' => 'pending', // Will be changed to 'rewarded' when user subscribes
            ]);
        }

        // Generate and send OTP for email verification
        $otp = \App\Models\Otp::createForEmailVerification($user);
        
        // Send OTP via notification (queued)
        try {
            $user->notify(new \App\Notifications\EmailVerificationNotification($otp->otp));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to queue OTP email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please verify your email.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'email_verified' => false,
                'otp' => config('app.debug') ? $otp->otp : null, // Only in development
            ],
        ], 201);
    }
   
}
