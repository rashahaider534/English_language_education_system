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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->integer('order');
            $table->integer('minimum_score');
            $table->integer('maximum_score');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'published','closed','archived'])->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->integer('estimated_duration');
            $table->foreignId('created_by')
                ->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
