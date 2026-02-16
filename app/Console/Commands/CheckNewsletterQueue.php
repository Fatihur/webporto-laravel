<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckNewsletterQueue extends Command
{
    protected $signature = 'newsletter:queue-status';
    protected $description = 'Check newsletter email queue status';

    public function handle(): void
    {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        $this->info('Newsletter Queue Status');
        $this->line('=======================');
        $this->line("Pending emails: {$pendingJobs}");
        $this->line("Failed emails: {$failedJobs}");
        $this->line('');

        if ($pendingJobs > 0) {
            $this->warn('Run "php artisan queue:work" to process pending emails.');
        }

        if ($failedJobs > 0) {
            $this->error('Run "php artisan queue:retry all" to retry failed emails.');
        }

        if ($pendingJobs === 0 && $failedJobs === 0) {
            $this->info('Queue is empty. All emails have been processed!');
        }
    }
}
