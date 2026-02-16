<?php

namespace App\Livewire\Admin\Newsletter;

use App\Jobs\SendNewsletterEmail;
use App\Models\NewsletterSubscriber;
use Livewire\Component;

class Send extends Component
{
    public $subject = '';
    public $content = '';
    public $previewMode = false;
    public $testMode = true;
    public $testEmail = '';
    public $isSending = false;
    public $sendProgress = 0;
    public $totalSubscribers = 0;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'content' => 'required|string|min:10',
        'testEmail' => 'required_if:testMode,true|email',
    ];

    protected $messages = [
        'subject.required' => 'Subject is required.',
        'content.required' => 'Content is required.',
        'content.min' => 'Content must be at least 10 characters.',
        'testEmail.required_if' => 'Test email is required for test mode.',
    ];

    public function mount()
    {
        $this->testEmail = auth()->user()?->email ?? '';
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function togglePreview()
    {
        $this->validate(['subject' => 'required', 'content' => 'required']);
        $this->previewMode = !$this->previewMode;
    }

    public function sendTest()
    {
        $this->validate();

        try {
            $subscriber = NewsletterSubscriber::firstOrNew([
                'email' => $this->testEmail,
            ], [
                'name' => 'Test User',
                'status' => 'active',
                'unsubscribe_token' => \Illuminate\Support\Str::random(64),
            ]);

            // Save if new
            if (!$subscriber->exists) {
                $subscriber->subscribed_at = now();
                $subscriber->save();
            }

            SendNewsletterEmail::dispatch($subscriber, $this->subject, $this->content);

            $this->dispatch('notify', type: 'success', message: 'Test email sent to ' . $this->testEmail);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to send test email: ' . $e->getMessage());
        }
    }

    public function sendNewsletter()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $activeSubscribers = NewsletterSubscriber::active()->get();
        $count = $activeSubscribers->count();

        if ($count === 0) {
            $this->dispatch('notify', type: 'error', message: 'No active subscribers found.');
            return;
        }

        $this->isSending = true;
        $this->totalSubscribers = $count;
        $this->sendProgress = 0;

        // Check if using sync driver - send emails immediately
        $isSync = config('queue.default') === 'sync';

        foreach ($activeSubscribers as $index => $subscriber) {
            SendNewsletterEmail::dispatch($subscriber, $this->subject, $this->content);
            $this->sendProgress = $index + 1;

            // If using sync driver, dispatch event after each email to update UI
            if ($isSync) {
                $this->dispatch('notify', type: 'info', message: "Sent to {$subscriber->email} ({$this->sendProgress}/{$count})");
            }
        }

        $this->isSending = false;
        $this->reset(['subject', 'content', 'testMode', 'testEmail', 'previewMode']);

        $message = $isSync
            ? "Newsletter sent successfully to {$count} subscribers!"
            : "Newsletter queued for {$count} subscribers. Emails will be sent shortly.";

        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function render()
    {
        $activeSubscribersCount = NewsletterSubscriber::active()->count();

        return view('livewire.admin.newsletter.send', [
            'activeSubscribersCount' => $activeSubscribersCount,
        ])->layout('layouts.admin');
    }
}
