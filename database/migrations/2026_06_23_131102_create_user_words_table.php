<?php

use App\Enums\WordStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('word_id')->constrained('words')->onDelete('cascade');
            $table->unique(['user_id', 'word_id']);
             $table->enum('status', array_column(WordStatus::cases(), 'value'))->default(WordStatus::LEARNING->value);
            $table->timestamp('added_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_words');
    }
};
