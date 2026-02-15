<?php

namespace App\Livewire\Admin\Analytics;

use App\Services\AnalyticsService;
use Livewire\Component;

class Dashboard extends Component
{
    public int $days = 30;
    public array $demographics = [];
    public array $popularContent = [];

    public function mount(): void
    {
        $this->loadAnalytics();
    }

    public function updatedDays(): void
    {
        $this->loadAnalytics();
    }

    protected function loadAnalytics(): void
    {
        $analyticsService = app(AnalyticsService::class);
        
        $this->demographics = $analyticsService->getDemographics($this->days);
        $this->popularContent = $analyticsService->getPopularContent($this->days);
    }

    public function render()
    {
        return view('livewire.admin.analytics.dashboard')->layout('layouts.admin');
    }
}
