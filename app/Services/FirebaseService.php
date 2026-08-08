<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\FirebaseToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Collection;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(
                config('firebase.credentials')
            );

        $this->messaging = $factory->createMessaging();
    }

    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = [],
        string $type = 'general'
    ): bool {
        try {
            $tokens = $user->firebaseTokens()
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                Log::info(
                    "No Firebase tokens found for user: {$user->id}"
                );

                return false;
            }

            $this->saveNotification(
                $user,
                $title,
                $body,
                $data,
                $type
            );

            $message = $this->buildMessage(
                $title,
                $body,
                $data,
                $type
            );

            $this->sendViaMulticast($tokens, $message);

            return true;
        } catch (\Throwable $e) {
            Log::error('Firebase send error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendToUsers(
        Collection $users,
        string $title,
        string $body,
        array $data = [],
        string $type = 'general'
    ): bool {
        try {
            if ($users->isEmpty()) {
                return false;
            }
            $userIds = $users->pluck('id');

            $firebaseTokens = FirebaseToken::whereIn('user_id', $userIds)
                ->get(['id', 'user_id', 'token']);

            if ($firebaseTokens->isEmpty()) {
                Log::info('No Firebase tokens found.');

                return false;
            }

            $tokens = $firebaseTokens
                ->pluck('token')
                ->unique()
                ->values()
                ->toArray();


            $data = collect($data)
                ->map(fn($value) => (string) $value)
                ->toArray();

            $data['type'] = $type;
            $message = CloudMessage::new()
                ->withNotification(
                    Notification::create($title, $body)
                )
                ->withData($data);

            foreach (array_chunk($tokens, 500) as $batch) {

                $report = $this->messaging->sendMulticast(
                    $message,
                    $batch
                );

                foreach ($report->invalidTokens() as $invalidToken) {
                    $this->deleteInvalidToken($invalidToken);
                }

                foreach ($report->validTokens() as $validToken) {
                    $this->updateTokenUsage($validToken);
                }
            }

            $notifications = $users->map(function (User $user) use (
                $title,
                $body,
                $data,
                $type
            ) {
                return [
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                    'data' => json_encode($data),
                    'type' => $type,
                    'read' => false,
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            AppNotification::insert($notifications);

            return true;
        } catch (\Throwable $e) {

            Log::error('Firebase sendToUsers error', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
    
    private function saveNotification(
        User $user,
        string $title,
        string $body,
        array $data,
        string $type
    ): void {
        AppNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'type' => $type,
        ]);
    }

    private function buildMessage(
        string $title,
        string $body,
        array $data,
        string $type
    ): CloudMessage {
        $data = collect($data)
            ->map(fn($value) => (string) $value)
            ->toArray();

        $data['type'] = $type;

        return CloudMessage::new()
            ->withNotification(
                Notification::create($title, $body)
            )
            ->withData($data);
    }
    private function sendViaMulticast(
        array $tokens,
        CloudMessage $message
    ): void {
        foreach (array_chunk($tokens, 500) as $batch) {
            $report = $this->messaging->sendMulticast(
                $message,
                $batch
            );

            foreach ($report->invalidTokens() as $token) {
                $this->deleteInvalidToken($token);
            }

            foreach ($report->validTokens() as $token) {
                $this->updateTokenUsage($token);
            }

            foreach ($report->unknownTokens() as $token) {
                $this->deleteInvalidToken($token);
            }
        }
    }

    private function deleteInvalidToken(string $token): void
    {
        FirebaseToken::where('token', $token)->delete();

        Log::info(
            "Deleted invalid Firebase token: {$token}"
        );
    }
    private function updateTokenUsage(string $token): void
    {
        FirebaseToken::where('token', $token)
            ->update([
                'last_used_at' => now(),
            ]);
    }

    public function registerToken(
        User $user,
        string $token,
        ?string $deviceType = null,
        ?string $deviceName = null
    ): FirebaseToken {
        return FirebaseToken::updateOrCreate(
            [
                'token' => $token,
            ],
            [
                'user_id' => $user->id,
                'device_type' => $deviceType,
                'device_name' => $deviceName,
                'last_used_at' => now(),
            ]
        );
    }
}
