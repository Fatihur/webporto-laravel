<?php

// Run this once to fix existing escaped HTML content
// php artisan tinker --execute="require 'fix_blog_content.php';"

use Illuminate\Support\Facades\DB;

$blogs = DB::table('blogs')->select('id', 'content', 'excerpt')->get();
$fixed = 0;

foreach ($blogs as $blog) {
    $originalContent = $blog->content;
    $originalExcerpt = $blog->excerpt;

    // Fix escaped characters
    $cleanContent = str_replace('\\/', '/', $originalContent);
    $cleanContent = str_replace('\\"', '"', $cleanContent);
    $cleanContent = str_replace("\\'", "'", $cleanContent);

    $cleanExcerpt = str_replace('\\/', '/', $originalExcerpt);
    $cleanExcerpt = str_replace('\\"', '"', $cleanExcerpt);
    $cleanExcerpt = str_replace("\\'", "'", $cleanExcerpt);

    if ($cleanContent !== $originalContent || $cleanExcerpt !== $originalExcerpt) {
        DB::table('blogs')->where('id', $blog->id)->update([
            'content' => $cleanContent,
            'excerpt' => $cleanExcerpt,
        ]);
        $fixed++;
        echo "Fixed blog ID: {$blog->id}\n";
    }
}

echo "Total fixed: {$fixed} blogs\n";
