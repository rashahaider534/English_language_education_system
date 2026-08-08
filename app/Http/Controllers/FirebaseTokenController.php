<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Http\Requests\RegisterFirebaseTokenRequest;
class FirebaseTokenController extends Controller
{
      public function __construct(
        private FirebaseService $firebaseService
    ) {}

    public function store(RegisterFirebaseTokenRequest $request)
    {
        $this->firebaseService->registerToken(
            auth()->user(),
            $request->token,
            $request->device_type,
            $request->device_name
        );

        return response()->json([
            'message' => 'Firebase token registered successfully.',
        ]);
    }
}
