<?php

namespace App\Livewire;

use App\Models\Contact;
use Livewire\Component;

class ContactPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';
    public bool $success = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|min:3|max:255',
            'message' => 'required|min:10|max:5000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'is_read' => false,
        ]);

        // Reset form
        $this->reset(['name', 'email', 'subject', 'message']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.contact-page')->layout('layouts.app');
    }
}
