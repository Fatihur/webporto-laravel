<?php

namespace App\Livewire\Admin\Newsletter;

use App\Models\NewsletterSubscriber;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortField = 'subscribed_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function toggleStatus($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if ($subscriber) {
            if ($subscriber->isActive()) {
                $subscriber->unsubscribe();
                $this->dispatch('notify', type: 'success', message: 'Subscriber unsubscribed successfully.');
            } else {
                $subscriber->update([
                    'status' => 'active',
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                ]);
                $this->dispatch('notify', type: 'success', message: 'Subscriber activated successfully.');
            }
        }
    }

    public function deleteSubscriber($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if ($subscriber) {
            $subscriber->delete();
            $this->dispatch('notify', type: 'success', message: 'Subscriber deleted successfully.');
        }
    }

    public function exportCsv()
    {
        $subscribers = NewsletterSubscriber::active()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="newsletter-subscribers.csv"',
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Name', 'Subscribed At']);

            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name ?? 'N/A',
                    $subscriber->subscribed_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $query = NewsletterSubscriber::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $subscribers = $query->paginate(15);

        // Statistics
        $stats = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::active()->count(),
            'unsubscribed' => NewsletterSubscriber::unsubscribed()->count(),
        ];

        return view('livewire.admin.newsletter.index', [
            'subscribers' => $subscribers,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}
