<?php

namespace App\Listeners;

use App\Events\ContentPublished;
use App\Jobs\SendNewsletterEmail;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Log;

class SendNewContentNotification
{
    /**
     * Handle the event.
     */
    public function handle(ContentPublished $event): void
    {
        // Check if auto-newsletter is enabled (default: true)
        $autoNewsletterEnabled = config('newsletter.auto_send', true);

        if (!$autoNewsletterEnabled) {
            Log::info('Auto-newsletter disabled. Skipping newsletter for new ' . $event->type);
            return;
        }

        $content = $event->content;
        $type = $event->type;

        // Get active subscribers
        $subscribers = NewsletterSubscriber::active()->get();

        if ($subscribers->isEmpty()) {
            Log::info('No active subscribers. Skipping newsletter.');
            return;
        }

        // Prepare email content based on type
        if ($type === 'blog') {
            $subject = '📝 New Blog Post: ' . $content->title;
            $emailContent = $this->getBlogEmailContent($content);
        } else {
            $subject = '🚀 New Project: ' . $content->title;
            $emailContent = $this->getProjectEmailContent($content);
        }

        // Send to all subscribers
        foreach ($subscribers as $subscriber) {
            SendNewsletterEmail::dispatch($subscriber, $subject, $emailContent);
        }

        Log::info("Newsletter queued for {$subscribers->count()} subscribers for new {$type}: {$content->title}");
    }

    /**
     * Get email content for blog post
     */
    private function getBlogEmailContent($blog): string
    {
        $url = route('blog.show', $blog->slug);
        $excerpt = $blog->excerpt ?? strip_tags(substr($blog->content, 0, 200)) . '...';

        return <<<HTML
<h2>📝 New Blog Post Published!</h2>

<p>Hi there! 👋</p>

<p>We just published a new blog post that we think you'll love:</p>

<h3 style="color: #76D7A4; margin: 20px 0;">{$blog->title}</h3>

<p>{$excerpt}</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{$url}" class="button">Read Full Article →</a>
</div>

<p style="font-size: 14px; color: #6b7280;">
    Category: {$blog->category} |
    Author: {$blog->author}</p>

<hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

<p style="font-size: 14px; color: #6b7280;">
    💡 Don't want to miss any updates? <a href="{{ config('app.url') }}/blog" style="color: #76D7A4;">Browse all posts →</a>
</p>
HTML;
    }

    /**
     * Get email content for project
     */
    private function getProjectEmailContent($project): string
    {
        $url = route('projects.show', $project->slug);
        $description = strip_tags(substr($project->description, 0, 250)) . '...';
        $techStack = $project->tech_stack ? implode(', ', array_slice($project->tech_stack, 0, 5)) : 'Various Technologies';

        return <<<HTML
<h2>🚀 New Project Showcase!</h2>

<p>Hi there! 👋</p>

<p>We're excited to share our latest project with you:</p>

<h3 style="color: #76D7A4; margin: 20px 0;">{$project->title}</h3>

<p>{$description}</p>

<p style="font-size: 14px; color: #6b7280; margin: 15px 0;">
    <strong>Tech Stack:</strong> {$techStack}
</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{$url}" class="button">View Project Details →</a>
</div>

<hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

<p style="font-size: 14px; color: #6b7280;">
    🎯 Want to see more? <a href="{{ config('app.url') }}" style="color: #76D7A4;">Explore all projects →</a>
</p>
HTML;
    }
}
