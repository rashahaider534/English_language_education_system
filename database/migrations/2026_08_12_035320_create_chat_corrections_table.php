<?php

use App\Enums\ChatErrorType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained('chat_messages')->cascadeOnDelete();

            $table->enum('error_type', array_column(ChatErrorType::cases(), 'value'));
            $table->string('original_fragment');
            $table->string('corrected_fragment');
            $table->text('explanation'); // بالعربي، متل ما اتفقنا (explanation_ar بالـ JSON)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_corrections');
    }
};
