<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Experience;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetExperiencesTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Get work experience and career history including companies, roles, descriptions, and date ranges.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $currentOnly = $request['current_only'] ?? false;
        $limit = $request['limit'] ?? 10;

        $experiences = Experience::query()
            ->when($currentOnly, function ($q) {
                $q->current();
            })
            ->ordered()
            ->limit($limit)
            ->get();

        if ($experiences->isEmpty()) {
            return 'No experiences found.';
        }

        $result = [];
        foreach ($experiences as $exp) {
            $result[] = [
                'company' => $exp->company,
                'role' => $exp->role,
                'description' => $exp->description,
                'date_range' => $exp->dateRange(),
                'is_current' => $exp->is_current,
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
            'current_only' => $schema->boolean()->description('Only return current positions')->nullable(),
            'limit' => $schema->integer()->min(1)->max(20)->description('Maximum number of experiences to return')->nullable(),
        ];
    }
}
