<?php

namespace App\Http\Controllers;

use App\Enums\OtpType;
use App\Http\Requests\Api\Auth\EmailOtpRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\resetPasswordRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Services\authService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function verifyOTP(VerifyOtpRequest $request , OtpType $type):  JsonResponse
    {
        $data = $request->validated();
        return response()->json(
            $this->authService->verifyOtp($data , $type)
        );
    }

    public function resendOtp(EmailOtpRequest $request , OtpType $type):  JsonResponse
    {
        $data = $request->validated();
        return response()->json(
            $this->authService->resendOtp($data['email'] , $type)
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

    public function forgotPassword(EmailOtpRequest $request ): JsonResponse
    {
        $data = $request->validated();
        return response()->json([
            $this->authService->forgotPassword($data['email'])
        ]);
    }

    public function resetPassword(resetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        return response()->json([
            $this->authService->resetPassword($data)
        ]);
    }
}
