<?php

namespace App\Livewire\Admin\Experiences;

use App\Models\Experience;
use Livewire\Component;

class Form extends Component
{
    public ?int $experienceId = null;

    // Form fields
    public string $company = '';
    public string $role = '';
    public string $description = '';
    public ?string $start_date = '';
    public ?string $end_date = '';
    public bool $is_current = false;
    public int $order = 0;

    protected function rules(): array
    {
        return [
            'company' => 'required|min:2|max:255',
            'role' => 'required|min:2|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'order' => 'integer|min:0',
        ];
    }

    public function mount(?int $id = null): void
    {
        $this->experienceId = $id;

        if ($id) {
            $experience = Experience::find($id);
            if ($experience) {
                $this->company = $experience->company;
                $this->role = $experience->role;
                $this->description = $experience->description;
                $this->start_date = $experience->start_date?->format('Y-m-d');
                $this->end_date = $experience->end_date?->format('Y-m-d');
                $this->is_current = $experience->is_current;
                $this->order = $experience->order;
            }
        } else {
            // Set default order to last
            $this->order = Experience::max('order') + 1;
        }
    }

    public function updatedIsCurrent(): void
    {
        if ($this->is_current) {
            $this->end_date = null;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'company' => $this->company,
            'role' => $this->role,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->is_current ? null : $this->end_date,
            'is_current' => $this->is_current,
            'order' => $this->order,
        ];

        if ($this->experienceId) {
            Experience::find($this->experienceId)->update($data);
            $message = 'Experience updated successfully.';
        } else {
            Experience::create($data);
            $message = 'Experience added successfully.';
        }

        $this->dispatch('notify', type: 'success', message: $message);
        $this->redirectRoute('admin.experiences.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.experiences.form')->layout('layouts.admin');
    }
}
