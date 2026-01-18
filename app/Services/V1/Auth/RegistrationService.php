<?php

namespace App\Services\V1\Auth;

use App\Exceptions\ClientErrorException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RegistrationService
{

    /**
     * Registers a new user.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'username' => Str::slug($data['full_name']) . '-' . Str::random(5),
                'last_login' => now()
            ]);

            $token = JWTAuth::fromUser($user);
            DB::commit();

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'email_verified' => false,
            ];
        } catch (Exception $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Verify the user's email address using the OTP provided in the request body.
     *
     * @param array $data The request body containing the OTP.
     * @return array The response containing the verified user.
     * @throws ClientErrorException If the OTP is invalid.
     */
    public function verifyEmail(array $data): array
    {
        try {
            $user = User::where('otp', $data['otp'])
                ->where('otp_expires_at', '>', now())
                ->first();

            if (!$user) throw new ClientErrorException("Invalid otp");

            $user->verifyEmail();

            return [
                'user' => $user->fresh(),
            ];
        } catch (Exception $th) {
            throw $th;
        }
    }
}
