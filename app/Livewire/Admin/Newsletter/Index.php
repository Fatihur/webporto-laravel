<?php

namespace App\Livewire\Admin\Newsletter;

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $sortField = 'subscribed_at';

    public $sortDirection = 'desc';

    public $autoNewsletterEnabled;

    // Bulk Actions
    public array $selected = [];

    public bool $selectAll = false;

    public string $bulkAction = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->autoNewsletterEnabled = config('newsletter.auto_send', true);
    }

    public function toggleAutoNewsletter()
    {
        $newValue = ! $this->autoNewsletterEnabled;
        $this->autoNewsletterEnabled = $newValue;

        // Update .env file (in production, you might want to use database settings instead)
        $this->updateEnvValue('NEWSLETTER_AUTO_SEND', $newValue ? 'true' : 'false');

        $this->dispatch('notify',
            type: 'success',
            message: $newValue ? 'Auto-newsletter enabled!' : 'Auto-newsletter disabled!'
        );
    }

    private function updateEnvValue($key, $value)
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        if (str_contains($content, $key.'=')) {
            $content = preg_replace('/'.$key.'=.*/', $key.'='.$value, $content);
        } else {
            $content .= "\n".$key.'='.$value;
        }

        file_put_contents($envPath, $content);

        // Clear config cache
        if (app()->configurationIsCached()) {
            Artisan::call('config:clear');
        }
    }

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

    public function updatedSelectAll($value): void
    {
        $subscribers = $this->getSubscribersForBulkAction();

        if ($value) {
            $this->selected = $subscribers->pluck('id')->toArray();
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
            case 'activate':
                $this->bulkActivate();
                break;
            case 'deactivate':
                $this->bulkDeactivate();
                break;
        }

        $this->reset('selected', 'selectAll', 'bulkAction');
    }

    private function bulkDelete(): void
    {
        NewsletterSubscriber::whereIn('id', $this->selected)->delete();
        $this->dispatch('notify', type: 'success', message: count($this->selected).' subscribers deleted successfully.');
    }

    private function bulkActivate(): void
    {
        NewsletterSubscriber::whereIn('id', $this->selected)->update([
            'status' => 'active',
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);
        $this->dispatch('notify', type: 'success', message: count($this->selected).' subscribers activated successfully.');
    }

    private function bulkDeactivate(): void
    {
        NewsletterSubscriber::whereIn('id', $this->selected)->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
        $this->dispatch('notify', type: 'success', message: count($this->selected).' subscribers deactivated successfully.');
    }

    private function getSubscribersForBulkAction()
    {
        $query = NewsletterSubscriber::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%'.$this->search.'%')
                    ->orWhere('name', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->get();
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
                $q->where('email', 'like', '%'.$this->search.'%')
                    ->orWhere('name', 'like', '%'.$this->search.'%');
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

        // Check pending jobs count
        $pendingJobs = \DB::table('jobs')->count();
        $failedJobs = \DB::table('failed_jobs')->count();

        return view('livewire.admin.newsletter.index', [
            'subscribers' => $subscribers,
            'stats' => $stats,
            'pendingJobs' => $pendingJobs,
            'failedJobs' => $failedJobs,
        ])->layout('layouts.admin');
    }
}
