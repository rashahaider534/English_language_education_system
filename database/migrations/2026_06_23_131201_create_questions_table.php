<?php

use App\Enums\QuestionType;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type',array_column(QuestionType::cases(), 'value'));
            $table->integer('score');
            $table->string('title_question_en');
            $table->string('title_question_ar');
            $table->text('text_question')->nullable();
            $table->enum('difficulty' , ['EASY' , 'MEDIUM' , 'HARD']);
            $table->foreignId('previous_question_id')
                ->nullable()
                ->constrained('questions')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
