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
        Schema::create('image_galleries', function (Blueprint $table) {
            $table->id();
            $table->morphs('gallerable'); // For projects, blogs, etc.
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('medium_path')->nullable();
            $table->string('large_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_galleries');
    }
};
