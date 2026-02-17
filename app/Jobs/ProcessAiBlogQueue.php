<?php

namespace App\Jobs;

use App\Models\AiBlogAutomation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAiBlogQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('Processing AI Blog Queue');

        // Get all active automations that are due for run
        $automations = AiBlogAutomation::active()
            ->dueForRun()
            ->get();

        Log::info('Found automations to process', [
            'count' => $automations->count(),
        ]);

        foreach ($automations as $automation) {
            if ($automation->shouldRunNow()) {
                Log::info('Dispatching AI blog generation', [
                    'automation_id' => $automation->id,
                    'name' => $automation->name,
                ]);

                GenerateAiBlogArticle::dispatch($automation);
            } else {
                Log::info('Automation not ready to run', [
                    'automation_id' => $automation->id,
                    'name' => $automation->name,
                ]);
            }
        }

        Log::info('AI Blog Queue processing completed');
    }
}
