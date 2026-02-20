<?php

namespace App\Livewire\Admin;

use App\Models\Blog;
use App\Models\Contact;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public int $projectsCount = 0;

    public int $blogsCount = 0;

    public int $contactsCount = 0;

    public int $unreadContactsCount = 0;

    public int $experiencesCount = 0;

    public function mount(): void
    {
        $stats = Cache::remember('admin.dashboard.stats', now()->addMinutes(2), function (): array {
            return [
                'projectsCount' => Project::count(),
                'blogsCount' => Blog::count(),
                'contactsCount' => Contact::count(),
                'unreadContactsCount' => Contact::unread()->count(),
                'experiencesCount' => Experience::count(),
            ];
        });

        $this->projectsCount = $stats['projectsCount'];
        $this->blogsCount = $stats['blogsCount'];
        $this->contactsCount = $stats['contactsCount'];
        $this->unreadContactsCount = $stats['unreadContactsCount'];
        $this->experiencesCount = $stats['experiencesCount'];
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
}
