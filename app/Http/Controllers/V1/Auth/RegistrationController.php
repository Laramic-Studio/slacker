<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificatonRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\V1\Auth\RegistrationService;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

    public function __construct(public RegistrationService $registrationService) {}

    public function register(RegisterRequest $request)
    {
        $response = $this->registrationService->register($request->validated());
        return $this->respondWithCustomData('User registered successfully. Please verify your email.', $response, 201);
    }

    public function verifyEmail(EmailVerificatonRequest $request) {
        $response = $this->registrationService->verifyEmail($request->validated());
        return $this->respondWithCustomData('Email verified successfully.', $response);
    }


    public function resendOtp() {
        $response = $this->registrationService->resendOtp();
        return $this->respondWithCustomData('Otp resend successfully.', $response);
    }

    
}
