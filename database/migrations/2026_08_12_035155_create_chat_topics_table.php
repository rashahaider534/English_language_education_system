<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();

            // فقرة قصيرة يكتبها الأدمن توضح النقاط المطلوب تغطيتها بالمحادثة
            $table->text('focus_points')->nullable();

            // تعليمات إضافية تنضاف على البرومبت الأساسي عند اختيار هالموضوع
            $table->text('system_prompt_addon')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_topics');
    }
};
