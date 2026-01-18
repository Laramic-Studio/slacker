<?php

namespace App\Services\V1\Auth;

use App\Exceptions\ClientErrorException;
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
}
