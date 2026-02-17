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
        Schema::create('site_contacts', function (Blueprint $table) {
            $table->id();

            // Email
            $table->string('email')->nullable();
            $table->string('email_label')->nullable()->default('Email');

            // WhatsApp
            $table->string('whatsapp')->nullable();
            $table->string('whatsapp_label')->nullable()->default('WhatsApp');

            // Social Media
            $table->string('instagram')->nullable();
            $table->string('instagram_label')->nullable()->default('Instagram');

            $table->string('linkedin')->nullable();
            $table->string('linkedin_label')->nullable()->default('LinkedIn');

            $table->string('github')->nullable();
            $table->string('github_label')->nullable()->default('GitHub');

            $table->string('twitter')->nullable();
            $table->string('twitter_label')->nullable()->default('Twitter');

            $table->string('facebook')->nullable();
            $table->string('facebook_label')->nullable()->default('Facebook');

            $table->string('youtube')->nullable();
            $table->string('youtube_label')->nullable()->default('YouTube');

            $table->string('tiktok')->nullable();
            $table->string('tiktok_label')->nullable()->default('TikTok');

            // Location/Address
            $table->text('address')->nullable();
            $table->string('maps_url')->nullable();

            // Working Hours
            $table->string('working_hours')->nullable();

            // Phone (non-WhatsApp)
            $table->string('phone')->nullable();
            $table->string('phone_label')->nullable()->default('Phone');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_contacts');
    }
};
