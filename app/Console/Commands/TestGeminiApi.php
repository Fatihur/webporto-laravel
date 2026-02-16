<?php

namespace App\Console\Commands;

use App\Services\Ai\GeminiService;
use Illuminate\Console\Command;

class TestGeminiApi extends Command
{
    protected $signature = 'gemini:test';
    protected $description = 'Test AI API connection (OpenRouter or Gemini)';

    public function handle(): int
    {
        $service = new GeminiService();
        $provider = $service->getProvider();

        $this->info("AI Service Configuration:");
        $this->info("  Provider: {$provider}");

        if (!$service->isConfigured()) {
            $this->error('ERROR: API key is not configured!');
            $this->info("\nTo use OpenRouter (recommended for free models):");
            $this->info("  1. Get API key from https://openrouter.ai/keys");
            $this->info("  2. Add OPENROUTER_API_KEY to your .env file");
            $this->info("\nFree models available:");
            foreach (GeminiService::getFreeModels() as $model => $name) {
                $this->info("    - {$name}");
            }
            $this->info("\nOr use Gemini:");
            $this->info("  1. Get API key from https://aistudio.google.com/app/apikey");
            $this->info("  2. Add GEMINI_API_KEY to your .env file");
            return self::FAILURE;
        }

        $this->info("\nTesting API connection...");

        try {
            $result = $service->chat(
                [['role' => 'user', 'content' => 'Say "AI service is working!" and introduce yourself briefly.']],
                'You are a helpful AI assistant.'
            );

            $this->info("\n✓ SUCCESS! API is working.");
            $this->info("Response: {$result}");
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("\n✗ Exception: " . $e->getMessage());

            if (str_contains($e->getMessage(), 'quota') || str_contains($e->getMessage(), '429')) {
                $this->warn("\nYour API key has exceeded the daily quota.");
                if ($provider === 'openrouter') {
                    $this->warn("Try switching to another free model in your .env file:");
                    foreach (GeminiService::getFreeModels() as $model => $name) {
                        $this->warn("  OPENROUTER_MODEL={$model}");
                    }
                } else {
                    $this->warn("Please wait 24 hours or use a different API key.");
                    $this->warn("Or switch to OpenRouter for free models:");
                    $this->warn("  https://openrouter.ai/keys");
                }
            }

            return self::FAILURE;
        }
    }
}
