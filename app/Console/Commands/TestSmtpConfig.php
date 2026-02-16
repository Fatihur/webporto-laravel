<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterEmail;
use App\Models\NewsletterSubscriber;

class TestSmtpConfig extends Command
{
    protected $signature = 'smtp:test {email?}';
    protected $description = 'Test SMTP configuration by sending test email';

    public function handle(): int
    {
        $testEmail = $this->argument('email') ?? 'fatihur17@gmail.com';

        $this->info('Testing SMTP Configuration...');
        $this->line('');
        $this->line('Mailer: ' . config('mail.default'));
        $this->line('Host: ' . config('mail.mailers.smtp.host'));
        $this->line('Port: ' . config('mail.mailers.smtp.port'));
        $this->line('Username: ' . config('mail.mailers.smtp.username'));
        $this->line('From: ' . config('mail.from.address'));
        $this->line('');

        try {
            $subscriber = new NewsletterSubscriber([
                'email' => $testEmail,
                'name' => 'Test User',
                'unsubscribe_token' => 'test-token',
                'status' => 'active',
            ]);

            $subscriber->exists = true; // Fake that it exists

            Mail::to($testEmail)->send(new NewsletterEmail(
                $subscriber,
                "This is a test email from your Laravel application.\n\nIf you received this, your SMTP configuration is working correctly!",
                'SMTP Test Email'
            ));

            $this->info('Test email sent successfully to: ' . $testEmail);
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send test email: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
