<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\V1\Auth\AuthSessionService;

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
}
