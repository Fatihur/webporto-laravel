<?php

namespace App\Jobs;

use App\Services\TranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTranslation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $text,
        public string $targetLocale,
        public ?string $sourceLocale = null
    ) {}

    public function handle(TranslationService $service): string
    {
        return $service->translate(
            $this->text,
            $this->targetLocale,
            $this->sourceLocale
        );
    }
}
