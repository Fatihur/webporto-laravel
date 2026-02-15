<?php

namespace App\Livewire;

use App\Models\ImageGallery;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageGallery extends Component
{
    use WithFileUploads;

    public $gallerableType;
    public $gallerableId;
    public $images = [];
    public $uploadProgress = 0;
    public $isUploading = false;

    protected $listeners = [
        'imageUploaded' => '$refresh',
        'imageReordered' => '$refresh',
    ];

    public function mount($gallerableType, $gallerableId)
    {
        $this->gallerableType = $gallerableType;
        $this->gallerableId = $gallerableId;
        $this->loadImages();
    }

    public function loadImages(): void
    {
        $this->images = ImageGallery::where('gallerable_type', $this->gallerableType)
            ->where('gallerable_id', $this->gallerableId)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function updatedImages(): void
    {
        $this->loadImages();
    }

    public function handleFileUpload($file): void
    {
        $this->isUploading = true;
        $this->uploadProgress = 0;

        // Process file upload
        $this->processImageUpload($file);
    }

    protected function processImageUpload($file): void
    {
        // Dispatch job to optimize image
        dispatch(function () use ($file) {
            $imageService = app(\App\Services\ImageOptimizationService::class);
            
            // Store original file temporarily
            $tempPath = $file->store('temp-uploads', $file->getClientOriginalName());
            
            // Optimize image with multiple sizes
            $optimizedPaths = $imageService->optimizeResponsive(
                $file,
                'uploads/images',
                ['sm' => ['width' => 400, 'height' => 300]]
            );
            
            // Create image gallery entries
            foreach ($optimizedPaths as $size => $path) {
                ImageGallery::create([
                    'gallerable_type' => $this->gallerableType,
                    'gallerable_id' => $this->gallerableId,
                    'image_path' => $path,
                    'thumbnail_path' => $optimizedPaths['sm'] ?? null,
                    'medium_path' => $optimizedPaths['md'] ?? null,
                    'large_path' => $optimizedPaths['lg'] ?? null,
                    'alt_text' => '',
                    'title' => '',
                    'order' => ImageGallery::where('gallerable_type', $this->gallerableType)
                        ->where('gallerable_id', $this->gallerableId)
                        ->max('order') + 1,
                ]);
            }
            
            // Delete temp file
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
        });
    }

    public function reorderImages(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            ImageGallery::where('id', $id)->update(['order' => $index]);
        }
        
        $this->loadImages();
        $this->dispatch('imageReordered');
    }

    public function updateAltText(int $imageId, string $altText): void
    {
        ImageGallery::where('id', $imageId)->update(['alt_text' => $altText]);
    }

    public function updateTitle(int $imageId, string $title): void
    {
        ImageGallery::where('id', $imageId)->update(['title' => $title]);
    }

    public function deleteImage(int $imageId): void
    {
        $image = ImageGallery::find($imageId);
        
        if ($image) {
            // Delete files from storage
            Storage::disk('public')->delete($image->image_path);
            if ($image->thumbnail_path) {
                Storage::disk('public')->delete($image->thumbnail_path);
            }
            if ($image->medium_path) {
                Storage::disk('public')->delete($image->medium_path);
            }
            if ($image->large_path) {
                Storage::disk('public')->delete($image->large_path);
            }
            
            $image->delete();
        }
        
        $this->loadImages();
    }

    public function render()
    {
        return view('livewire.image-gallery');
    }
}
