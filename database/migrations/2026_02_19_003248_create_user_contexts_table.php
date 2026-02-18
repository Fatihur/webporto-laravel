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
        Schema::create('user_contexts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->string('user_identifier', 64)->nullable()->index();
            $table->string('context_type', 50)->index();
            $table->text('context_value');
            $table->boolean('is_sensitive')->default(false);
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            // Composite indexes for faster queries
            $table->index(['session_id', 'context_type']);
            $table->index(['session_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contexts');
    }
};
