<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public SocialAuthService $socialAuthService;
    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }


//    public function redirect()
//    {
//        return response()->json([
//            $this->socialAuthService->redirect()
//        ]);
//
//    }

    public function login(Request $request)
    {
        return response()->json([
            $this->socialAuthService->login($request)
        ]);
    }
}
