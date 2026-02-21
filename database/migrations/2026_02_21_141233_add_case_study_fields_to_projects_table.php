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
        Schema::table('projects', function (Blueprint $table) {
            $table->text('case_study_problem')->nullable()->after('content');
            $table->text('case_study_process')->nullable()->after('case_study_problem');
            $table->text('case_study_result')->nullable()->after('case_study_process');
            $table->json('case_study_metrics')->nullable()->after('case_study_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'case_study_problem',
                'case_study_process',
                'case_study_result',
                'case_study_metrics',
            ]);
        });
    }
};
