<?php

namespace App\Livewire;

use App\Data\CategoryData;
use Livewire\Component;

class Navigation extends Component
{
    public bool $isMegaMenuOpen = false;
    public bool $isMobileMenuOpen = false;
    public array $categories = [];

    public function mount(): void
    {
        $this->categories = CategoryData::all();
    }

    public function toggleMegaMenu(): void
    {
        $this->isMegaMenuOpen = !$this->isMegaMenuOpen;
    }

    public function closeMegaMenu(): void
    {
        $this->isMegaMenuOpen = false;
    }

    public function toggleMobileMenu(): void
    {
        $this->isMobileMenuOpen = !$this->isMobileMenuOpen;
    }

    public function closeMobileMenu(): void
    {
        $this->isMobileMenuOpen = false;
    }

    public function render()
    {
        return view('livewire.navigation');
    }
}
