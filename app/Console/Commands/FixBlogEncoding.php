<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;

class FixBlogEncoding extends Command
{
    protected $signature = 'blog:fix-encoding {--dry-run : Preview changes without applying}';
    protected $description = 'Fix JSON-encoded HTML content in blogs table';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $blogs = Blog::all();
        $fixed = 0;

        $this->info("Checking {$blogs->count()} blog posts...\n");

        foreach ($blogs as $blog) {
            $originalContent = $blog->getRawOriginal('content') ?? '';
            $originalExcerpt = $blog->getRawOriginal('excerpt') ?? '';

            $newContent = $this->cleanValue($originalContent);
            $newExcerpt = $this->cleanValue($originalExcerpt);

            $needsFix = ($newContent !== $originalContent) || ($newExcerpt !== $originalExcerpt);

            if ($needsFix) {
                $this->info("[FIX] Blog ID {$blog->id}: {$blog->title}");

                if ($this->output->isVerbose()) {
                    $this->line("  Before: " . substr($originalContent, 0, 100) . '...');
                    $this->line("  After:  " . substr($newContent, 0, 100) . '...');
                }

                if (!$dryRun) {
                    // Update directly using query builder to bypass mutators
                    \DB::table('blogs')
                        ->where('id', $blog->id)
                        ->update([
                            'content' => $newContent,
                            'excerpt' => $newExcerpt,
                        ]);
                }
                $fixed++;
            } else {
                $this->line("[OK] Blog ID {$blog->id}: {$blog->title}");
            }
        }

        $this->newLine();
        $this->info("Summary: {$fixed} of {$blogs->count()} blogs fixed");

        if ($dryRun && $fixed > 0) {
            $this->warn("\nRun without --dry-run to apply changes.");
        }

        return self::SUCCESS;
    }

    private function cleanValue(string $value): string
    {
        if (empty($value)) {
            return $value;
        }

        // Iteratively decode JSON-encoded strings
        $maxIterations = 10;
        while ($maxIterations-- > 0 && strlen($value) > 2) {
            if ($value[0] === '"' && $value[strlen($value) - 1] === '"') {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                    $value = $decoded;
                    continue;
                }
            }
            break;
        }

        // Fix remaining escaped characters
        $value = str_replace('\\/', '/', $value);
        $value = str_replace('\\"', '"', $value);
        $value = str_replace("\\'", "'", $value);

        return $value;
    }
}
