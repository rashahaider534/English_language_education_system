<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\LevelExceptionStatus;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('level_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('requested_level_id')->constrained('levels')->onDelete('cascade');
            $table->foreignId('recommended_level_id')->constrained('levels')->onDelete('cascade');
            $table->enum(
                'status',array_column(LevelExceptionStatus::cases(), 'value')
            )->default(LevelExceptionStatus::PENDING->value);
            $table->text('reason');
            $table->text('review_note')->nullable();
            $table->foreignId('executed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_exceptions');
    }
};
