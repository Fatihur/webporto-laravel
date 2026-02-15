<?php

namespace App\Livewire\Admin\Contacts;

use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    public function delete(int $id): void
    {
        $contact = Contact::find($id);

        if ($contact) {
            $contact->delete();
            $this->dispatch('notify', type: 'success', message: 'Contact message deleted successfully.');
        }
    }

    public function markAsRead(int $id): void
    {
        $contact = Contact::find($id);

        if ($contact) {
            $contact->markAsRead();
            $this->dispatch('notify', type: 'success', message: 'Marked as read.');
        }
    }

    public function markAsUnread(int $id): void
    {
        $contact = Contact::find($id);

        if ($contact) {
            $contact->markAsUnread();
            $this->dispatch('notify', type: 'success', message: 'Marked as unread.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Contact::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter === 'unread') {
            $query->unread();
        } elseif ($this->statusFilter === 'read') {
            $query->where('is_read', true);
        }

        // Default order: unread first, then by date
        $query->orderBy('is_read', 'asc')
              ->orderBy('created_at', 'desc');

        $contacts = $query->paginate(10);

        // Get unread count for badge
        $unreadCount = Contact::unread()->count();

        return view('livewire.admin.contacts.index', [
            'contacts' => $contacts,
            'unreadCount' => $unreadCount,
        ])->layout('layouts.admin');
    }
}
