<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_session_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->unique()->constrained('chat_sessions')->cascadeOnDelete();

            $table->text('overall_feedback')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->string('estimated_level')->nullable();
            $table->unsignedInteger('xp_awarded')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_session_summaries');
    }
};
