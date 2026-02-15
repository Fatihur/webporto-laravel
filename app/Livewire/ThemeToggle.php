<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public string $theme = 'light';

    public function mount(): void
    {
        $this->theme = session('theme', 'light');
        $this->applyTheme();
    }

    public function toggleTheme(): void
    {
        $this->theme = $this->theme === 'light' ? 'dark' : 'light';
        session(['theme' => $this->theme]);
        $this->applyTheme();
    }

    private function applyTheme(): void
    {
        if ($this->theme === 'dark') {
            $this->dispatch('theme-changed', theme: 'dark');
        } else {
            $this->dispatch('theme-changed', theme: 'light');
        }
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
