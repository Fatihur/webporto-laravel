<?php

declare(strict_types=1);

namespace App\Livewire\Admin\SiteContacts;

use App\Models\SiteContact;
use Livewire\Component;

class Index extends Component
{
    // Email
    public ?string $email = '';

    public ?string $email_label = 'Email';

    // WhatsApp
    public ?string $whatsapp = '';

    public ?string $whatsapp_label = 'WhatsApp';

    // Social Media
    public ?string $instagram = '';

    public ?string $instagram_label = 'Instagram';

    public ?string $linkedin = '';

    public ?string $linkedin_label = 'LinkedIn';

    public ?string $github = '';

    public ?string $github_label = 'GitHub';

    public ?string $twitter = '';

    public ?string $twitter_label = 'Twitter';

    public ?string $facebook = '';

    public ?string $facebook_label = 'Facebook';

    public ?string $youtube = '';

    public ?string $youtube_label = 'YouTube';

    public ?string $tiktok = '';

    public ?string $tiktok_label = 'TikTok';

    // Location/Address
    public ?string $address = '';

    public ?string $maps_url = '';

    // Working Hours
    public ?string $working_hours = '';

    // Phone
    public ?string $phone = '';

    public ?string $phone_label = 'Phone';

    public ?int $contactId = null;

    public function mount(): void
    {
        $contact = SiteContact::first();

        if ($contact) {
            $this->contactId = $contact->id;
            $this->email = $contact->email;
            $this->email_label = $contact->email_label ?? 'Email';
            $this->whatsapp = $contact->whatsapp;
            $this->whatsapp_label = $contact->whatsapp_label ?? 'WhatsApp';
            $this->instagram = $contact->instagram;
            $this->instagram_label = $contact->instagram_label ?? 'Instagram';
            $this->linkedin = $contact->linkedin;
            $this->linkedin_label = $contact->linkedin_label ?? 'LinkedIn';
            $this->github = $contact->github;
            $this->github_label = $contact->github_label ?? 'GitHub';
            $this->twitter = $contact->twitter;
            $this->twitter_label = $contact->twitter_label ?? 'Twitter';
            $this->facebook = $contact->facebook;
            $this->facebook_label = $contact->facebook_label ?? 'Facebook';
            $this->youtube = $contact->youtube;
            $this->youtube_label = $contact->youtube_label ?? 'YouTube';
            $this->tiktok = $contact->tiktok;
            $this->tiktok_label = $contact->tiktok_label ?? 'TikTok';
            $this->address = $contact->address;
            $this->maps_url = $contact->maps_url;
            $this->working_hours = $contact->working_hours;
            $this->phone = $contact->phone;
            $this->phone_label = $contact->phone_label ?? 'Phone';
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'email' => 'nullable|email',
            'email_label' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:20',
            'whatsapp_label' => 'nullable|string|max:50',
            'instagram' => 'nullable|url|max:255',
            'instagram_label' => 'nullable|string|max:50',
            'linkedin' => 'nullable|url|max:255',
            'linkedin_label' => 'nullable|string|max:50',
            'github' => 'nullable|url|max:255',
            'github_label' => 'nullable|string|max:50',
            'twitter' => 'nullable|url|max:255',
            'twitter_label' => 'nullable|string|max:50',
            'facebook' => 'nullable|url|max:255',
            'facebook_label' => 'nullable|string|max:50',
            'youtube' => 'nullable|url|max:255',
            'youtube_label' => 'nullable|string|max:50',
            'tiktok' => 'nullable|url|max:255',
            'tiktok_label' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'maps_url' => 'nullable|url|max:500',
            'working_hours' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'phone_label' => 'nullable|string|max:50',
        ]);

        if ($this->contactId) {
            SiteContact::find($this->contactId)->update($validated);
        } else {
            $contact = SiteContact::create($validated);
            $this->contactId = $contact->id;
        }

        $this->dispatch('notify', type: 'success', message: 'Contact settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.site-contacts.index')
            ->layout('layouts.admin');
    }
}
