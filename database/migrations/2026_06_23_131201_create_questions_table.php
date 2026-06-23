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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['MCQ' ,'PAIR', 'FILL' , 'ARRANGE']);
            $table->integer('score');
            $table->string('title_question_en');
            $table->string('title_question_ar');
            $table->text('text_question');
            $table->enum('difficulty' , ['EASY' , 'MEDIUM' , 'HARD']);
            $table->timestamps();
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
