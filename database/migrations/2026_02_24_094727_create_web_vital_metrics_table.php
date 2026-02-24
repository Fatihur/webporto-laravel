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
        Schema::create('web_vital_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('path', 255);
            $table->string('metric', 20);
            $table->decimal('value', 10, 3);
            $table->string('rating', 20);
            $table->string('page_group', 50)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('connection_type', 30)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['metric', 'recorded_at'], 'idx_web_vitals_metric_time');
            $table->index(['path', 'metric', 'recorded_at'], 'idx_web_vitals_path_metric_time');
            $table->index(['page_group', 'metric'], 'idx_web_vitals_group_metric');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_vital_metrics');
    }
};
