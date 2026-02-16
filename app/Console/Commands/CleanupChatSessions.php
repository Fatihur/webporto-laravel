<?php

namespace App\Console\Commands;

use App\Models\ChatSession;
use Illuminate\Console\Command;

class CleanupChatSessions extends Command
{
    protected $signature = 'chat:cleanup
                            {--days=30 : Delete sessions older than this many days}
                            {--inactive : Only delete inactive sessions}
                            {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Clean up old chat sessions and messages';

    public function handle(): int
    {
        $days = $this->option('days');
        $inactiveOnly = $this->option('inactive');
        $dryRun = $this->option('dry-run');

        $query = ChatSession::olderThanDays($days);

        if ($inactiveOnly) {
            $query->where('is_active', false);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('No chat sessions found for cleanup.');
            return self::SUCCESS;
        }

        $this->info("Found {$count} chat sessions older than {$days} days.");

        if ($dryRun) {
            $this->warn('Dry run mode - no deletions performed.');
            $sessions = $query->get();
            foreach ($sessions as $session) {
                $this->line("  - Session {$session->session_id} (Last active: {$session->last_activity_at})");
            }
            return self::SUCCESS;
        }

        if (!$this->confirm("Delete {$count} chat sessions and their messages?", true)) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        // Delete in chunks to avoid memory issues
        $deleted = 0;
        $query->chunkById(100, function ($sessions) use (&$deleted) {
            foreach ($sessions as $session) {
                // Messages will be deleted via cascade
                $session->delete();
                $deleted++;
            }
        });

        $this->info("Successfully deleted {$deleted} chat sessions.");

        return self::SUCCESS;
    }
}
