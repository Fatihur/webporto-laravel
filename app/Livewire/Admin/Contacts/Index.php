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

    // Bulk Actions
    public array $selected = [];

    public bool $selectAll = false;

    public string $bulkAction = '';

    protected $queryString = ['search', 'statusFilter'];

    public function updatedSelectAll($value): void
    {
        $contacts = $this->getContactsForBulkAction();

        if ($value) {
            $this->selected = $contacts->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function executeBulkAction(): void
    {
        if (empty($this->selected)) {
            $this->dispatch('notify', type: 'error', message: 'No items selected.');

            return;
        }

        if (empty($this->bulkAction)) {
            $this->dispatch('notify', type: 'error', message: 'Please select an action.');

            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
            case 'markAsRead':
                $this->bulkMarkAsRead();
                break;
            case 'markAsUnread':
                $this->bulkMarkAsUnread();
                break;
        }

        $this->reset('selected', 'selectAll', 'bulkAction');
    }

    private function bulkDelete(): void
    {
        Contact::whereIn('id', $this->selected)->delete();
        $this->dispatch('notify', type: 'success', message: count($this->selected).' messages deleted successfully.');
    }

    private function bulkMarkAsRead(): void
    {
        Contact::whereIn('id', $this->selected)->update(['is_read' => true]);
        $this->dispatch('notify', type: 'success', message: count($this->selected).' messages marked as read.');
    }

    private function bulkMarkAsUnread(): void
    {
        Contact::whereIn('id', $this->selected)->update(['is_read' => false]);
        $this->dispatch('notify', type: 'success', message: count($this->selected).' messages marked as unread.');
    }

    private function getContactsForBulkAction()
    {
        $query = Contact::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('subject', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter === 'unread') {
            $query->unread();
        } elseif ($this->statusFilter === 'read') {
            $query->where('is_read', true);
        }

        return $query->get();
    }

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
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('subject', 'like', '%'.$this->search.'%');
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
