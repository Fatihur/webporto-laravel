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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function togglePreview()
    {
        $this->validate(['subject', 'content']);
        $this->previewMode = !$this->previewMode;
    }

    public function sendTest()
    {
        $this->validate();

        $subscriber = NewsletterSubscriber::firstOrNew([
            'email' => $this->testEmail,
        ], [
            'name' => 'Test User',
            'status' => 'active',
            'unsubscribe_token' => \Illuminate\Support\Str::random(64),
        ]);

        SendNewsletterEmail::dispatch($subscriber, $this->subject, $this->content);

        $this->dispatch('notify', type: 'success', message: 'Test email sent to ' . $this->testEmail);
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

        // Dispatch jobs for each subscriber
        foreach ($activeSubscribers as $subscriber) {
            SendNewsletterEmail::dispatch($subscriber, $this->subject, $this->content);
        }

        $this->reset(['subject', 'content', 'testMode', 'testEmail']);
        $this->dispatch('notify', type: 'success', message: "Newsletter queued for {$count} subscribers.");
    }

    public function render()
    {
        $activeSubscribersCount = NewsletterSubscriber::active()->count();

        return view('livewire.admin.newsletter.send', [
            'activeSubscribersCount' => $activeSubscribersCount,
        ])->layout('layouts.admin');
    }
}
