<?php

namespace App\Livewire\Admin\Experiences;

use App\Models\Experience;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $id): void
    {
        $experience = Experience::find($id);

        if ($experience) {
            $experience->delete();
            $this->dispatch('notify', type: 'success', message: 'Experience deleted successfully.');
        }
    }

    public function moveUp(int $id): void
    {
        $experience = Experience::find($id);
        if ($experience && $experience->order > 0) {
            $swapWith = Experience::where('order', $experience->order - 1)->first();
            if ($swapWith) {
                $swapWith->order = $experience->order;
                $swapWith->save();
                $experience->order = $experience->order - 1;
                $experience->save();
            }
        }
    }

    public function moveDown(int $id): void
    {
        $experience = Experience::find($id);
        $maxOrder = Experience::max('order');
        if ($experience && $experience->order < $maxOrder) {
            $swapWith = Experience::where('order', $experience->order + 1)->first();
            if ($swapWith) {
                $swapWith->order = $experience->order;
                $swapWith->save();
                $experience->order = $experience->order + 1;
                $experience->save();
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.experiences.index', [
            'experiences' => Experience::ordered()->get(),
        ])->layout('layouts.admin');
    }
}
