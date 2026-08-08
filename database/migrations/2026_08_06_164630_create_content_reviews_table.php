<?php

use App\Enums\ReviewStatus;
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
        Schema::create('content_reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable');
            $table->foreignId('reviewer_id')->constrained('users');
            $table->enum('status', array_column(ReviewStatus::cases(), 'value'))->default(ReviewStatus::IN_REVIEW);
            $table->timestamp('claimed_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_reviews');
    }
};
