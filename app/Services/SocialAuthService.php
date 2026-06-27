<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    public function redirect()
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return[
            'url' => $url
        ];
    }

    public function callback(Request $request){
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = User::where('google_id', $googleUser->getId())->first();
        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();
            if (!$user) {
                $name = trim($googleUser->getName());
                $parts = preg_split('/\s+/', $name);
                $firstName = array_shift($parts);
                $lastName = count($parts) ? implode(' ', $parts) : null;
                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('student');
            } elseif (is_null($user->google_id) || $user->google_id !== $googleUser->getId()) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }
        $token = $user->createToken('auth-token')->plainTextToken;
        $user->load('roles');
        return [
            'token' => $token,
            'user' => new UserResource($user)
        ];
    }

}
