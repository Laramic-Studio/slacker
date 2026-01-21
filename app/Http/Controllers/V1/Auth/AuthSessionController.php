<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\V1\Auth\AuthSessionService;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;

class AuthSessionController extends Controller
{

    public function __construct(public AuthSessionService $authSessionService) {}

    public function login(LoginRequest $request)
    {
        $response = $this->authSessionService->login($request->validated());
        return $this->respondWithCustomData('User logged in successfully.', $response);
    }

    public function logout()
    {
        $response = $this->authSessionService->logout();
        return $this->respondWithCustomData('User logged out successfully.', $response);
    }

    public function refresh()
    {
        $response = $this->authSessionService->refresh();
        return $this->respondWithCustomData('Token refreshed successfully.', $response);
    }


    public function me()
    {
        $response = $this->authSessionService->me();
        return $this->respondWithCustomData('User details retrieved successfully.', $response);
    }

    public function forgotPassword(ForgotPasswordRequest $request) {
        $response = $this->authSessionService->sendPasswordResetUr($request->validated());
        return $this->respondWithCustomData('Password reset instruction sent to your email provided.', $response);
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $response = $this->authSessionService->resetPassword($request->validated());
        return $this->respondWithCustomData('Password reset successfully.', $response);
    }
}
