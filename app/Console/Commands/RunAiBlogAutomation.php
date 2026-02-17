<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAiBlogArticle;
use App\Models\AiBlogAutomation;
use Illuminate\Console\Command;

class RunAiBlogAutomation extends Command
{
    protected $signature = 'ai-blog:run
                            {--automation-id= : Run a specific automation by ID}
                            {--force : Force run even if not scheduled}';

    protected $description = 'Run AI blog automation to generate articles';

    public function handle(): int
    {
        $automationId = $this->option('automation-id');
        $force = $this->option('force');

        if ($automationId) {
            // Run specific automation
            $automation = AiBlogAutomation::find($automationId);

            if (! $automation) {
                $this->error("Automation with ID {$automationId} not found.");

                return self::FAILURE;
            }

            if (! $automation->is_active) {
                $this->warn("Automation '{$automation->name}' is inactive.");

                return self::FAILURE;
            }

            if (! $force && ! $automation->shouldRunNow()) {
                $this->warn("Automation '{$automation->name}' is not due to run yet.");
                $this->info("Next run: {$automation->next_run_at?->format('Y-m-d H:i:s')}");

                return self::FAILURE;
            }

            $this->info("Dispatching automation: {$automation->name}");
            GenerateAiBlogArticle::dispatch($automation);
            $this->info('Job dispatched successfully!');
        } else {
            // Run all due automations
            $automations = AiBlogAutomation::active()
                ->when(! $force, fn ($q) => $q->dueForRun())
                ->get();

            if ($automations->isEmpty()) {
                $this->warn('No automations to run.');

                return self::SUCCESS;
            }

            $this->info("Found {$automations->count()} automation(s) to process.");

            foreach ($automations as $automation) {
                if ($force || $automation->shouldRunNow()) {
                    $this->info("Dispatching: {$automation->name}");
                    GenerateAiBlogArticle::dispatch($automation);
                }
            }

            $this->info('All jobs dispatched successfully!');
        }

        return self::SUCCESS;
    }
}
