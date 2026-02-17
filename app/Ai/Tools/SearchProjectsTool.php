<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchProjectsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search for projects in the portfolio by keyword, category, or tech stack. Returns project titles, descriptions, categories, tech stacks, and links.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $query = $request['query'] ?? '';
        $category = $request['category'] ?? null;
        $tech = $request['tech'] ?? null;
        $limit = $request['limit'] ?? 5;

        $projects = Project::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                });
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->when($tech, function ($q) use ($tech) {
                $q->whereJsonContains('tech_stack', $tech);
            })
            ->recent()
            ->limit($limit)
            ->get();

        if ($projects->isEmpty()) {
            return 'No projects found matching the criteria.';
        }

        $result = [];
        foreach ($projects as $project) {
            $result[] = [
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => strip_tags($project->description),
                'category' => $project->category,
                'tech_stack' => $project->tech_stack ?? [],
                'link' => $project->link,
                'project_date' => $project->project_date?->format('F Y'),
            ];
        }

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Search keyword for project title, description, or content'),
            'category' => $schema->string()->description('Filter by category: graphic-design, software-dev, data-analysis, networking')->nullable(),
            'tech' => $schema->string()->description('Filter by technology in tech stack (e.g., Laravel, Python, Figma)')->nullable(),
            'limit' => $schema->integer()->min(1)->max(10)->description('Maximum number of results to return')->nullable(),
        ];
    }
}
