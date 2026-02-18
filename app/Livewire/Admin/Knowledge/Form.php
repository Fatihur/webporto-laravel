<?php

namespace App\Livewire\Admin\Knowledge;

use App\Models\KnowledgeEntry;
use Livewire\Component;

class Form extends Component
{
    public ?int $entryId = null;

    public string $title = '';

    public string $content = '';

    public string $category = '';

    public string $tags = '';

    public bool $is_active = true;

    public array $categories = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:50',
            'tags' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'content.required' => 'Content is required.',
            'category.required' => 'Category is required.',
        ];
    }

    public function mount(?int $id = null): void
    {
        $this->entryId = $id;
        $this->categories = KnowledgeEntry::distinct()->pluck('category')->sort()->values()->toArray();

        if ($this->entryId) {
            $entry = KnowledgeEntry::find($this->entryId);
            if ($entry) {
                $this->title = $entry->title;
                $this->content = $entry->content;
                $this->category = $entry->category;
                $this->tags = $entry->tags ? implode(', ', $entry->tags) : '';
                $this->is_active = $entry->is_active;
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        $tagArray = $this->tags ? array_map('trim', explode(',', $this->tags)) : [];

        if ($this->entryId) {
            $entry = KnowledgeEntry::find($this->entryId);
            if ($entry) {
                $entry->update([
                    'title' => $this->title,
                    'content' => $this->content,
                    'category' => $this->category,
                    'tags' => $tagArray,
                    'is_active' => $this->is_active,
                ]);
                $this->dispatch('notify', type: 'success', message: 'Knowledge entry updated successfully.');
            }
        } else {
            KnowledgeEntry::create([
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->category,
                'tags' => $tagArray,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('notify', type: 'success', message: 'Knowledge entry created successfully.');
        }

        $this->redirect(route('admin.knowledge.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.knowledge.form')->layout('layouts.admin');
    }
}
