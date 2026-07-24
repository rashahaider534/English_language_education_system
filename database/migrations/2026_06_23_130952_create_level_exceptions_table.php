<?php

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
        Schema::create('level_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('requested_level_id')->constrained('levels')->onDelete('cascade');
            $table->enum('status',['pending','approved','rejected']);
            $table->text('reason');
            $table->text('review_note')->nullable();
            $table->foreignId('executed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->timestamp('executed_at')->nullable();
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
