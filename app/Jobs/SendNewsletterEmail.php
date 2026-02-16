<?php

namespace App\Jobs;

use App\Mail\NewsletterEmail;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscriber $subscriber,
        public string $subject,
        public string $content
    ) {}

    public function handle(): void
    {
        if ($this->subscriber->status !== 'active') {
            return;
        }

        // Set email subject pada subscriber untuk digunakan di Mailable
        $this->subscriber->email_subject = $this->subject;

        Mail::to($this->subscriber->email)
            ->send(new NewsletterEmail(
                $this->subscriber,
                $this->content,
                $this->subject
            ));
    }
}
