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

   
}
