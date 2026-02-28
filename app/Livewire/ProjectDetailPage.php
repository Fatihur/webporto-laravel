<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\SeoService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ProjectDetailPage extends Component
{
    public ?Project $project = null;

    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->project = Cache::flexible('project.detail.'.$slug, [300, 1800], function () use ($slug): ?Project {
            return Project::query()
                ->select($this->projectSelectColumns())
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

        $seoService = app(SeoService::class);

        $structuredData = [
            $seoService->generateProjectStructuredData($this->project),
            $seoService->generateBreadcrumbStructuredData([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Projects', 'url' => route('projects.category', $this->project->category)],
                ['name' => $this->project->title, 'url' => route('projects.show', $this->project->slug)],
            ]),
        ];

        return view('livewire.project-detail-page', [
            'relatedProjects' => $relatedProjects,
            'structuredData' => $structuredData,
        ])->layout('layouts.app');
    }

    /**
     * @return list<string>
     */
    private function projectSelectColumns(): array
    {
        $requiredColumns = [
            'id',
            'title',
            'slug',
            'description',
            'content',
            'category',
            'thumbnail',
            'project_date',
            'tags',
            'tech_stack',
            'gallery',
            'stats',
            'link',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'created_at',
            'updated_at',
        ];

        $optionalColumns = [
            'case_study_problem',
            'case_study_process',
            'case_study_result',
            'case_study_metrics',
        ];

        $availableColumns = array_flip(Schema::getColumnListing('projects'));

        $selectedColumns = [];

        foreach (array_merge($requiredColumns, $optionalColumns) as $column) {
            if (isset($availableColumns[$column])) {
                $selectedColumns[] = $column;
            }
        }

        return $selectedColumns;
    }
}
