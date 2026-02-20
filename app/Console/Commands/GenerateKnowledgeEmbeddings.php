<?php

namespace App\Console\Commands;

use App\Models\KnowledgeEntry;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;

class GenerateKnowledgeEmbeddings extends Command
{
    protected $signature = 'knowledge:embed
                            {--id= : Generate embedding for specific entry ID}
                            {--all : Generate embeddings for all entries without embedding}';

    protected $description = 'Generate vector embeddings for knowledge base entries';

    public function handle(): int
    {
        $embeddingService = new EmbeddingService;

        // Jika specific ID
        if ($this->option('id')) {
            $entry = KnowledgeEntry::find($this->option('id'));
            if (! $entry) {
                $this->error('Entry not found!');

                return self::FAILURE;
            }

            $this->info("Generating embedding for: {$entry->title}");
            $success = $embeddingService->embedEntry($entry);

            if ($success) {
                $this->info('✓ Embedding generated successfully!');

                return self::SUCCESS;
            } else {
                $this->error('✗ Failed to generate embedding');

                return self::FAILURE;
            }
        }

        // Generate untuk semua entries tanpa embedding
        $entries = KnowledgeEntry::whereNull('embedding')->get();

        if ($entries->isEmpty()) {
            $this->info('All entries already have embeddings!');

            return self::SUCCESS;
        }

        $this->info("Found {$entries->count()} entries without embeddings");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($entries->count());
        $progressBar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($entries as $entry) {
            try {
                $success = $embeddingService->embedEntry($entry);
                if ($success) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            } catch (\Throwable $e) {
                $failCount++;
                $this->warn("\nFailed for {$entry->title}: {$e->getMessage()}");
            }

            $progressBar->advance();

            // Small delay to avoid rate limiting
            usleep(100000); // 100ms
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Done! Success: {$successCount}, Failed: {$failCount}");

        return self::SUCCESS;
    }
}
