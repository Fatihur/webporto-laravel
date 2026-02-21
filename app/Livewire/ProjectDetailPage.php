<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ProjectDetailPage extends Component
{
    public ?Project $project = null;

    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->project = Cache::flexible('project.'.$slug, [300, 1800], function () use ($slug): ?Project {
            return Project::query()
                ->select(['id', 'title', 'slug', 'description', 'content', 'category', 'thumbnail', 'project_date', 'tags', 'tech_stack', 'gallery', 'stats', 'link', 'meta_title', 'meta_description', 'meta_keywords', 'created_at', 'updated_at'])
                ->where('slug', $slug)
                ->first();
        });

        if (! $this->project) {
            abort(404);
        }
    }

    public function render()
    {
        $cachedRelated = Cache::flexible('projects.related.'.$this->project->category, [300, 1800], function (): Collection {
            return Project::query()
                ->select(['id', 'title', 'slug', 'description', 'category', 'thumbnail', 'project_date', 'tags', 'created_at'])
                ->where('category', $this->project->category)
                ->recent()
                ->limit(12)
                ->get();
        });

        $relatedProjects = $cachedRelated
            ->where('id', '!=', $this->project->id)
            ->take(3)
            ->values();

        return view('livewire.project-detail-page', [
            'relatedProjects' => $relatedProjects,
        ])->layout('layouts.app');
    }
}
