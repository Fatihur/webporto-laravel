<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Change column type from json to longText using raw SQL
        try {
            DB::statement('ALTER TABLE blogs MODIFY content LONGTEXT');
        } catch (\Exception $e) {
            // Column may already be changed
        }

        // Step 2: Fix existing JSON-encoded HTML content
        $blogs = DB::table('blogs')->select('id', 'content')->get();
        foreach ($blogs as $blog) {
            $decoded = json_decode($blog->content, false);
            if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                DB::table('blogs')->where('id', $blog->id)->update([
                    'content' => $decoded,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE blogs MODIFY content JSON');
    }
};

