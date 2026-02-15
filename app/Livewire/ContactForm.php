<?php

namespace App\Livewire;

use App\Models\Contact;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $project_type = '';
    public string $message = '';
    public ?string $successMessage = null;

    public array $projectTypes = [
        'Graphic Design',
        'Software Development',
        'Data Analysis',
        'Networking / IT',
    ];

    protected array $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|max:255',
        'project_type' => 'required|string|max:255',
        'message' => 'required|string|min:10',
    ];

    protected array $messages = [
        'name.required' => 'Please enter your name.',
        'name.min' => 'Name must be at least 3 characters.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'project_type.required' => 'Please select a project type.',
        'message.required' => 'Please enter your message.',
        'message.min' => 'Message must be at least 10 characters.',
    ];

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function submit(): void
    {
        $validated = $this->validate();

        // Save to database
        Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['project_type'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $this->successMessage = 'Thank you! Your message has been sent successfully.';

        // Reset form
        $this->reset(['name', 'email', 'project_type', 'message']);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
