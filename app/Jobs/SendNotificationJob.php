<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $userIds,
        public string $title,
        public string $body,
        public array $data = [],
        public string $type = 'general',
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FirebaseService $firebaseService): void
    {
$users = User::whereIn('id', $this->userIds)->get();

        if ($users->isEmpty()) {
            return;
        }

        $firebaseService->sendToUsers(
            $users,
            $this->title,
            $this->body,
            $this->data,
            $this->type
        );
    }
}
