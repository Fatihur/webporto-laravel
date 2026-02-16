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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['user', 'assistant']);
            $table->longText('content');
            $table->json('metadata')->nullable(); // For tokens, latency, etc
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index('chat_session_id');
            $table->index('role');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
