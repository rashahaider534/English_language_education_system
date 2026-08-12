<?php

use App\Enums\ChatMode;
use App\Enums\ChatSessionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('chat_topics')->nullOnDelete();

            $table->enum('mode', array_column(ChatMode::cases(), 'value'));
            $table->enum('status', array_column(ChatSessionStatus::cases(), 'value'))
                ->default(ChatSessionStatus::ACTIVE->value);

            // مستوى الطالب وقت بدء الجلسة (snapshot، لأنه مستوى الطالب ممكن يتغير بعدين)
            $table->foreignId('level_id_snapshot')->nullable()->constrained('levels')->nullOnDelete();

            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']); // لتسريع query "هل عنده جلسة active؟"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
