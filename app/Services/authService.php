<?php

namespace App\Services;

use App\Http\Requests\Api\RegisterRequest;
use App\Jobs\SendOtpEmailJob;
use App\Mail\otpMail;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class authService
{

    public function register(array $data)
    {
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already exists']
            ]);
        }

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => null,
        ]);



        $this->sendOtp($user);

        return [
            'message' => 'OTP sent successfully'
        ];
    }

    public function sendOtp(User $user)
    {
        $otp = (string)random_int(100000, 999999);
        Cache::put(
            "otp:{$user->email}",
            $otp,
            now()->addMinutes(5)
        );
        Cache::put("otp_attempts:{$user->email}", 0, now()->addMinutes(5));
        SendOtpEmailJob::dispatch($user->email, $otp);
    }

    public function verifyOtp(array $data)
    {

        $email = $data['email'];

        $cachedOtp = Cache::get("otp:{$email}");

        if (!$cachedOtp) {
            throw ValidationException::withMessages([
                'otp' => ['OTP expired']
            ]);
        }

//        if ((string)$cachedOtp !== (string)$data['otp']) {
//
//            $attempts = Cache::increment("otp_attempts:{$email}");
//
//            if ($attempts >= 5) {
//
//                Cache::forget("otp:{$email}");
//                Cache::forget("otp_attempts:{$email}");
//
//                throw ValidationException::withMessages([
//                    'otp' => ['Too many attempts']
//                ]);
//            }
//
//            throw ValidationException::withMessages([
//                'otp' => ['Invalid OTP']
//            ]);
//        }

        $attempts = Cache::increment("otp_attempts:{$email}");
        if ($attempts > 5) {
            Cache::forget("otp:{$email}");
            Cache::forget("otp_attempts:{$email}");
            throw ValidationException::withMessages(['otp' => ['Too many attempts. Request a new OTP.']]);
        }

        if ((string)$cachedOtp !== (string)$data['otp']) {
            throw ValidationException::withMessages(['otp' => ['Invalid OTP']]);
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found']
            ]);
        }

        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Already verified']
            ]);
        }

        $user->update([
            'email_verified_at' => now()
        ]);


        Cache::forget("otp:{$user->email}");
        Cache::forget("otp_attempts:{$user->email}");

        $token = $user->createToken('auth-token')->plainTextToken;
        $user->assignRole('student');
        return [
            'message' => 'Account verified successfully',
            'token' => $token,
            'user' => $user
        ];
    }

    public function resendOtp(string $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found']
            ]);
        }

        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Email already verified']
            ]);
        }
        if (Cache::has("resend_lock:$email")) {
            throw ValidationException::withMessages([
                'email' => ['Please wait before requesting another OTP']
            ]);
        }

        Cache::put("resend_lock:$email", true, 60);
        Cache::forget("otp:$email");
        Cache::forget("otp_attempts:$email");

        $this->sendOtp($user);

        return [
            'message' => 'OTP resent successfully'
        ];

    }
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if(!$user)
        {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }
        if(!Hash::check($data['password'], $user->password))
        {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }
        if (!$user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Please verify your email first']
            ]);
        }

       $token =  $user->createToken('auth-token')->plainTextToken;
        return [
            'message' => 'Login successfully',
            'token' => $token,
            'role' => $user->roles,
        ];
    }

    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();

        return [
            'message' => 'Logged out successfully'
        ];
    }
}
