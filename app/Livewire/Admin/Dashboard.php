<?php

namespace App\Livewire\Admin;

use App\Models\Blog;
use App\Models\Contact;
use App\Models\Experience;
use App\Models\Project;
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
        $this->projectsCount = Project::count();
        $this->blogsCount = Blog::count();
        $this->contactsCount = Contact::count();
        $this->unreadContactsCount = Contact::unread()->count();
        $this->experiencesCount = Experience::count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
}
