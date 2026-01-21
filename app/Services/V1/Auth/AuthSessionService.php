<?php

namespace App\Services\V1\Auth;

use App\Exceptions\ClientErrorException;
use App\Models\User;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthSessionService
{

    /**
     * Log the user into the application.
     *
     * @param array $payload
     * @return array
     * @throws ClientErrorException
     */
    public function login(array $payload): array
    {

        try {
            if (! $token = JWTAuth::attempt($payload)) {
                throw new ClientErrorException('Invalid credentials provided.', 401);
            }

            $user = JWTAuth::user();

            return  [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'email_verified' => !empty($user->email_verified_at),
            ];
        } catch (Exception $th) {
            throw $th;
        }
    }

    /**
     * Logout the user from the application.
     *
     * @return array
     * @throws Exception
     */
    public function logout(): array
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return [];
        } catch (Exception  $th) {
            throw $th;
        }
    }

    /**
     * Refresh the user's authentication token.
     *
     * This method refreshes the user's authentication token by generating a new one
     * based on the current token. If the token is invalid or expired, an exception
     * will be thrown.
     *
     * @return array The refreshed token information.
     * @throws Exception If the token is invalid or expired.
     */
    public function refresh(): array
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());

            return  [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ];
        } catch (Exception $th) {
            throw $th;
        }
    }

    /**
     * Return the authenticated user.
     *
     * @return array The authenticated user.
     * @throws Exception If unable to retrieve the authenticated user.
     */
    public function me(): array
    {
        try {
            return [
                'user' => JWTAuth::user(),
            ];
        } catch (Exception $th) {
            throw $th;
        }
    }

    /**
     * Sends a password reset email to the user.
     *
     * @param array $payload The request body containing the user's email.
     * @return array An empty array.
     * @throws ClientErrorException If the user's email is invalid.
     */
    public function sendPasswordResetUr(array $payload)
    {
        try {
            $user = User::where('email', $payload['email'])->first();

            if (!$user) throw new ClientErrorException("Invalid email");

            $user->generatePassswordResetToken();

            return [];
        } catch (Exception $th) {
            throw $th;
        }
    }

    /**
     * Reset the user's password using the reset token provided in the request body.
     *
     * @param array $payload The request body containing the reset token and new password.
     * @return array The response containing an empty array.
     * @throws ClientErrorException If the reset token is invalid or expired.
     */
    public function resetPassword(array $payload)
    {
        try {
            $user = User::query()
                ->where('reset_token', $payload['reset_token'])
                ->where('reset_token_expires_at', '>', now())
                ->first();

            if (!$user) throw new ClientErrorException("Invalid or expired reset token");

            $user->update([
                'password' => $payload['password'],
                'reset_token' => null,
                'reset_token_expires_at' => null
            ]);

            return [];
        } catch (Exception $th) {
            throw $th;
        }
    }
}
