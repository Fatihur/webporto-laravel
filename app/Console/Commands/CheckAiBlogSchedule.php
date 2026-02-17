<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAiBlogQueue;
use Illuminate\Console\Command;

class CheckAiBlogSchedule extends Command
{
    protected $signature = 'ai-blog:schedule-check';

    protected $description = 'Check and process AI blog automation schedule';

    public function handle(): int
    {
        $this->info('Checking AI blog automation schedule...');

        ProcessAiBlogQueue::dispatch();

        $this->info('Queue processing job dispatched.');

        return self::SUCCESS;
    }
}
