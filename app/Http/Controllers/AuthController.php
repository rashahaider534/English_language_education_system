<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;

use App\Models\User;
use App\Services\authService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

    protected $authService;

    public function __construct(authService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        return response()->json(
            $this->authService->register($data)
        );

    }

    public function verifyOTP(VerifyOtpRequest $request):  JsonResponse
    {
        $data = $request->validated();
        return response()->json(
            $this->authService->verifyOtp($data)
        );
    }

    public function resendOtp(ResendOtpRequest $request):  JsonResponse
    {
        $data = $request->validated();
        return response()->json(
            $this->authService->resendOtp($data['email'])
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
         $data = $request->validated();
         return response()->json(
             $this->authService->login($data)
         );
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json(
            $this->authService->logout($request->user())
        );
    }
}
