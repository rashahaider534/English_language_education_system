<?php

use App\Enums\ContentStatus;
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
        Schema::create('tests', function (Blueprint $table)
        {
            $table->id();
            $table->morphs('testable');
            $table->integer('passing_score')->nullable();
            $table->string('title_en');
            $table->string('title_ar');
            $table->enum('status', array_column(ContentStatus::cases(), 'value'))->default(ContentStatus::DRAFT->value);
            $table->foreignId('previous_test_id')
                ->nullable()
                ->constrained('tests')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
