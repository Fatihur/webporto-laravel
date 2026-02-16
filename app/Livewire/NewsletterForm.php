<?php

namespace App\Livewire;

use App\Jobs\SendNewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use Livewire\Component;

class NewsletterForm extends Component
{
    public $email = '';
    public $success = false;

    protected $rules = [
        'email' => 'required|email|unique:newsletter_subscribers,email',
    ];

    protected $messages = [
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already subscribed.',
    ];

    public function subscribe()
    {
        $this->validate();

        $subscriber = NewsletterSubscriber::subscribe($this->email);

        // Send confirmation email
        SendNewsletterConfirmation::dispatch($subscriber);

        $this->reset('email');
        $this->success = true;

        // Reset success message after 5 seconds
        $this->dispatch('hide-success');
    }

    public function render()
    {
        return view('livewire.newsletter-form');
    }
}
