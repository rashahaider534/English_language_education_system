<?php

use App\Enums\ChatMessageRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained('chat_sessions')->cascadeOnDelete();

            $table->enum('role', array_column(ChatMessageRole::cases(), 'value'));
            $table->text('content'); // النص الأصلي كما هو
            $table->text('corrected_content')->nullable(); // بس لرسايل role=user

            $table->json('metadata')->nullable(); // أي بيانات إضافية من رد الـ AI

            $table->timestamps();

            $table->index('chat_session_id'); // لجلب رسايل جلسة معينة بسرعة، مرتبة
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
