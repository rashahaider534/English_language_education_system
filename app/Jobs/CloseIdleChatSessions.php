<?php

namespace App\Jobs;

use App\Enums\ChatSessionStatus;
use App\Models\ChatSession;
use App\Services\Chat\ChatSessionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseIdleChatSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const IDLE_MINUTES = 20;

    public function handle(ChatSessionService $chatSessionService): void
    {
        $idleSessions = ChatSession::where('status', ChatSessionStatus::ACTIVE->value)
            ->where(function ($query) {
                $query->whereDoesntHave('messages', function ($q) {
                    $q->where('created_at', '>=', now()->subMinutes(self::IDLE_MINUTES));
                });
            })
            ->get();

        foreach ($idleSessions as $session) {
            $chatSessionService->endSession($session);
        }
    }
}
