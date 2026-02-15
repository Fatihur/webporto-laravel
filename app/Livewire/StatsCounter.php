<?php

namespace App\Livewire;

use Livewire\Component;

class StatsCounter extends Component
{
    public array $stats = [];

    public function mount(): void
    {
        $this->stats = [
            ['label' => 'Years Exp.', 'value' => 5, 'display' => '05', 'suffix' => ''],
            ['label' => 'Live Projects', 'value' => 42, 'display' => '42', 'suffix' => ''],
            ['label' => 'Global Clients', 'value' => 18, 'display' => '18', 'suffix' => ''],
            ['label' => 'Cups of Coffee', 'value' => 1000, 'display' => '1k+', 'suffix' => '+'],
        ];
    }

    public function render()
    {
        return view('livewire.stats-counter');
    }
}
