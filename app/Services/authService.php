<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Http\Requests\Api\EmailOtpRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
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

    public function register(array $data):array
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



        $this->sendOtp($user , OtpType::REGISTER);

        return [
            'message' => 'OTP sent successfully'
        ];
    }

    private function sendOtp(User $user , OtpType $type):void
    {
        $otp = (string)random_int(100000, 999999);
        Cache::put(
            "otp:{$type->value}:{$user->email}",
            $otp,
            now()->addMinutes(5)
        );
        Cache::put("otp_attempts:{$type->value}:{$user->email}", 0, now()->addMinutes(5));
        SendOtpEmailJob::dispatch($user->email, $otp);
    }

    public function verifyOtp(array $data , OtpType $type):array
    {

        $email = $data['email'];

        $cachedOtp = Cache::get("otp:{$type->value}:{$email}");

        if (!$cachedOtp) {
            throw ValidationException::withMessages([
                'otp' => ['OTP expired']
            ]);
        }

        $attempts = Cache::increment("otp_attempts:{$type->value}:{$email}");
        if ($attempts > 5) {
            Cache::forget("otp:{$type->value}:{$email}");
            Cache::forget("otp_attempts:{$type->value}:{$email}");
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

        Cache::forget("otp:{$type->value}:{$user->email}");
        Cache::forget("otp_attempts:{$type->value}:{$user->email}");

        return match ($type) {
            OtpType::REGISTER => $this->handleRegisterVerification($user),
            OtpType::FORGOT_PASSWORD => $this->handleForgotPasswordVerification($user),
        };
    }

    private function handleRegisterVerification(User $user):array
    {
        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Already verified']
            ]);
        }

        $user->update([
            'email_verified_at' => now()
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;
        $user->assignRole('student');
        return [
            'message' => 'Account verified successfully',
            'token' => $token,
            'user' => new UserResource($user)
        ];
    }

    private function handleForgotPasswordVerification(User $user):array
    {
        if (!$user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Your email address is not verified yet.']
            ]);
        }

        Cache::put("password_reset_verified:{$user->email}", true, now()->addMinutes(10));
        return [
            'message' => 'Otp Verified successfully',
        ];
    }
    public function resendOtp(string $email,OtpType $type):array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found']
            ]);
        }

        if ($type === OtpType::REGISTER && $user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Email already verified']
            ]);
        }
        if (Cache::has("resend_lock:{$type->value}:$email")) {
            throw ValidationException::withMessages([
                'email' => ['Please wait before requesting another OTP']
            ]);
        }

        Cache::put("resend_lock:{$type->value}:$email", true, 60);
        Cache::forget("otp:{$type->value}:$email");
        Cache::forget("otp_attempts:{$type->value}:$email");

        $this->sendOtp($user , $type);

        return [
            'message' => 'OTP resent successfully'
        ];

    }
    public function login(array $data):array
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
            'role' => $user->roles->pluck('name'),
        ];
    }

    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();

        return [
            'message' => 'Logged out successfully'
        ];
    }

    public function forgotPassword(string $email):array
    {
        $type = OtpType::FORGOT_PASSWORD;
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw ValidationException::withMessages(['email' => ['Email not found.']]);
        }

        if (Cache::has("resend_lock:{$type->value}:{$email}")) {
            throw ValidationException::withMessages([
                'email' => ['Please wait before requesting another OTP']
            ]);
        }

        Cache::put("resend_lock:{$type->value}:{$email}", true, 60);
        Cache::forget("otp:{$type->value}:{$email}");
        Cache::forget("otp_attempts:{$type->value}:{$email}");

        $this->sendOtp($user, $type);

        return [
            'message' => 'OTP sent successfully to your email.'
        ];
    }

    public function resetPassword(array $data): array
    {
        $email = $data['email'];
        $cacheKey = "password_reset_verified:{$email}";

        if (!Cache::has($cacheKey)) {
            throw ValidationException::withMessages([
                'email' => ['unauthorized. Please verify OTP again.']
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.']
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        Cache::forget($cacheKey);

        return [
            'message' => 'Password reset successfully. You can now login with your new password.'
        ];
    }
}
