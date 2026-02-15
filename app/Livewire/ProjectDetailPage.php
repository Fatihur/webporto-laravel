<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectDetailPage extends Component
{
    public ?Project $project = null;
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->project = Project::where('slug', $slug)->first();

        if (!$this->project) {
            abort(404);
        }
    }

    public function render()
    {
        // Get related projects (same category, excluding current)
        $relatedProjects = Project::where('category', $this->project->category)
            ->where('id', '!=', $this->project->id)
            ->limit(3)
            ->get();

        return view('livewire.project-detail-page', [
            'relatedProjects' => $relatedProjects,
        ])->layout('layouts.app');
    }
}
