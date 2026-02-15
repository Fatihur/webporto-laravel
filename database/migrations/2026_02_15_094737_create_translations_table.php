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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable'); // translatable_type, translatable_id (unsignedBigInteger)
            $table->string('locale', 5); // en, id, etc.
            $table->string('field'); // title, description, content, etc.
            $table->longText('value');
            $table->boolean('is_auto_translated')->default(true);
            $table->timestamps();

            // Unique constraint to prevent duplicate translations
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'unique_translation');

            // Index for faster lookups
            $table->index(['locale', 'field']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
