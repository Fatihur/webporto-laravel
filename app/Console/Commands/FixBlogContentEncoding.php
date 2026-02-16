<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;

class FixBlogContentEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:fix-encoding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix JSON-encoded blog content in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking blog posts for JSON-encoded content...');

        $blogs = Blog::all();
        $fixedCount = 0;

        foreach ($blogs as $blog) {
            $needsUpdate = false;
            $content = $blog->content;
            $excerpt = $blog->excerpt;

            // Check and fix content
            if ($this->isJsonEncoded($content)) {
                $this->info("Fixing JSON-encoded content for blog: {$blog->title}");
                $content = $this->decodeJsonContent($content);
                $needsUpdate = true;
            }

            // Check and fix excerpt
            if ($this->isJsonEncoded($excerpt)) {
                $this->info("Fixing JSON-encoded excerpt for blog: {$blog->title}");
                $excerpt = $this->decodeJsonContent($excerpt);
                $needsUpdate = true;
            }

            // Clean escaped characters
            $content = $this->cleanHtml($content);
            $excerpt = $this->cleanHtml($excerpt);

            if ($needsUpdate || $content !== $blog->content || $excerpt !== $blog->excerpt) {
                $blog->update([
                    'content' => $content,
                    'excerpt' => $excerpt,
                ]);
                $fixedCount++;
            }
        }

        $this->info("Done! Fixed {$fixedCount} blog post(s).");

        return Command::SUCCESS;
    }

    /**
     * Check if string is JSON encoded
     */
    private function isJsonEncoded(string $str): bool
    {
        if (strlen($str) < 2) {
            return false;
        }

        $trimmed = trim($str);
        return ($trimmed[0] === '"' && $trimmed[strlen($trimmed) - 1] === '"');
    }

    /**
     * Decode JSON-encoded content iteratively
     */
    private function decodeJsonContent(string $content): string
    {
        $maxIterations = 5;
        while ($maxIterations-- > 0) {
            $trimmed = trim($content);
            if (strlen($trimmed) > 2 && $trimmed[0] === '"' && $trimmed[strlen($trimmed) - 1] === '"') {
                $decoded = json_decode($trimmed, true);
                if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                    $content = $decoded;
                    continue;
                }
            }
            break;
        }

        return $content;
    }

    /**
     * Clean HTML content by fixing escaped characters
     */
    private function cleanHtml(string $html): string
    {
        // Fix escaped forward slashes
        $html = str_replace('\\/', '/', $html);

        // Fix other common escaped characters
        $html = str_replace('\\"', '"', $html);
        $html = str_replace("\\'", "'", $html);
        $html = str_replace('\\\\', '\\', $html);

        // Fix escaped newlines and tabs
        $html = str_replace('\\n', "\n", $html);
        $html = str_replace('\\r', "\r", $html);
        $html = str_replace('\\t', "\t", $html);

        return $html;
    }
}
