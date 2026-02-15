<?php

namespace App\Jobs;

use App\Services\ImageOptimizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessImageOptimization implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $imagePath,
        public string $storageDisk = 'public'
    ) {}

    public function handle(ImageOptimizationService $service): void
    {
        $file = Storage::disk($this->storageDisk)->get($this->imagePath);
        
        // Process image with multiple sizes
        $optimizedPaths = $service->processImage($file, dirname($this->imagePath));
        
        // Delete original if optimization successful
        if (!empty($optimizedPaths)) {
            Storage::disk($this->storageDisk)->delete($this->imagePath);
        }
    }
}
