<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Project;
use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class OptimizeExistingImages extends Command
{
    protected $signature = 'images:optimize
                            {--model= : Model yang akan dioptimasi (blog/project/all)}
                            {--quality=85 : Kualitas WebP (1-100)}
                            {--max-width=1200 : Lebar maksimal gambar}';

    protected $description = 'Optimize existing images and convert to WebP';

    public function handle(): int
    {
        $model = $this->option('model') ?? 'all';
        $quality = (int) $this->option('quality');
        $maxWidth = (int) $this->option('max-width');

        $this->info("Starting image optimization...");
        $this->info("Quality: {$quality}, Max Width: {$maxWidth}px");

        $optimized = 0;
        $errors = 0;

        if ($model === 'blog' || $model === 'all') {
            $this->info("\nProcessing Blog images...");
            $result = $this->optimizeBlogs($quality, $maxWidth);
            $optimized += $result['optimized'];
            $errors += $result['errors'];
        }

        if ($model === 'project' || $model === 'all') {
            $this->info("\nProcessing Project images...");
            $result = $this->optimizeProjects($quality, $maxWidth);
            $optimized += $result['optimized'];
            $errors += $result['errors'];
        }

        $this->info("\n✅ Optimization complete!");
        $this->info("Total optimized: {$optimized}");
        if ($errors > 0) {
            $this->error("Errors: {$errors}");
        }

        return self::SUCCESS;
    }

    private function optimizeBlogs(int $quality, int $maxWidth): array
    {
        $blogs = Blog::whereNotNull('image')->get();
        $optimized = 0;
        $errors = 0;

        foreach ($blogs as $blog) {
            try {
                $this->info("Processing Blog: {$blog->title}");

                // Skip jika sudah webp
                if (str_ends_with(strtolower($blog->image), '.webp')) {
                    $this->comment("  Already WebP, skipping...");
                    continue;
                }

                // Cek file exists
                if (!Storage::disk('public')->exists($blog->image)) {
                    $this->warn("  File not found: {$blog->image}");
                    continue;
                }

                // Read dan optimize
                $path = Storage::disk('public')->path($blog->image);
                $image = Image::read($path);

                // Resize jika perlu
                if ($image->width() > $maxWidth) {
                    $image = $image->scaleDown($maxWidth);
                }

                // Generate new filename
                $directory = dirname($blog->image);
                $newFilename = pathinfo($blog->image, PATHINFO_FILENAME) . '.webp';
                $newPath = $directory . '/' . $newFilename;

                // Encode dan save
                $encoded = $image->toWebp($quality)->toString();
                Storage::disk('public')->put($newPath, $encoded);

                // Delete old file
                Storage::disk('public')->delete($blog->image);

                // Update database
                $blog->image = $newPath;
                $blog->save();

                $optimized++;
                $this->info("  ✅ Optimized: {$newPath}");

            } catch (\Exception $e) {
                $errors++;
                $this->error("  ❌ Error: {$e->getMessage()}");
            }
        }

        return ['optimized' => $optimized, 'errors' => $errors];
    }

    private function optimizeProjects(int $quality, int $maxWidth): array
    {
        $projects = Project::whereNotNull('thumbnail')->orWhereNotNull('gallery')->get();
        $optimized = 0;
        $errors = 0;

        foreach ($projects as $project) {
            try {
                $this->info("Processing Project: {$project->title}");

                // Optimize thumbnail
                if ($project->thumbnail && !str_ends_with(strtolower($project->thumbnail), '.webp')) {
                    if (Storage::disk('public')->exists($project->thumbnail)) {
                        $path = Storage::disk('public')->path($project->thumbnail);
                        $image = Image::read($path);

                        $directory = dirname($project->thumbnail);
                        $newFilename = pathinfo($project->thumbnail, PATHINFO_FILENAME) . '.webp';
                        $newPath = $directory . '/' . $newFilename;

                        $encoded = $image->toWebp($quality)->toString();
                        Storage::disk('public')->put($newPath, $encoded);
                        Storage::disk('public')->delete($project->thumbnail);

                        $project->thumbnail = $newPath;
                        $this->info("  ✅ Thumbnail optimized");
                    }
                }

                // Optimize gallery
                if ($project->gallery) {
                    $newGallery = [];
                    foreach ($project->gallery as $imagePath) {
                        if (!str_ends_with(strtolower($imagePath), '.webp')) {
                            if (Storage::disk('public')->exists($imagePath)) {
                                $path = Storage::disk('public')->path($imagePath);
                                $image = Image::read($path);

                                $directory = dirname($imagePath);
                                $newFilename = pathinfo($imagePath, PATHINFO_FILENAME) . '.webp';
                                $newPath = $directory . '/' . $newFilename;

                                $encoded = $image->toWebp($quality)->toString();
                                Storage::disk('public')->put($newPath, $encoded);
                                Storage::disk('public')->delete($imagePath);

                                $newGallery[] = $newPath;
                                $this->info("  ✅ Gallery image optimized: {$newFilename}");
                            } else {
                                $newGallery[] = $imagePath;
                            }
                        } else {
                            $newGallery[] = $imagePath;
                        }
                    }
                    $project->gallery = $newGallery;
                }

                $project->save();
                $optimized++;

            } catch (\Exception $e) {
                $errors++;
                $this->error("  ❌ Error: {$e->getMessage()}");
            }
        }

        return ['optimized' => $optimized, 'errors' => $errors];
    }
}
