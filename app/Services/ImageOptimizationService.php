<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageOptimizationService
{
    /**
     * Konfigurasi default untuk optimasi gambar
     */
    private array $config = [
        'format' => 'webp',
        'quality' => 85,
        'max_width' => 1200,
        'max_height' => 1200,
    ];

    /**
     * Optimasi dan simpan gambar
     *
     * @param UploadedFile $file File yang diupload
     * @param string $directory Direktori tujuan
     * @param array $options Opsi kustom (format, quality, max_width, max_height)
     * @return string Path file yang disimpan
     */
    public function optimize(
        UploadedFile $file,
        string $directory,
        array $options = []
    ): string {
        $options = array_merge($this->config, $options);

        // Buat nama file unique dengan extension webp
        $filename = Str::uuid() . '.' . $options['format'];
        $path = $directory . '/' . $filename;

        // Baca gambar menggunakan Intervention Image
        $image = Image::read($file->getRealPath());

        // Resize jika gambar terlalu besar (maintain aspect ratio)
        $image = $this->resizeIfNeeded($image, $options['max_width'], $options['max_height']);

        // Encode ke format yang diinginkan dengan kualitas tertentu
        $encoded = $this->encodeImage($image, $options['format'], $options['quality']);

        // Simpan ke storage
        Storage::disk('public')->put($path, $encoded);

        return $path;
    }

    /**
     * Optimasi dan simpan thumbnail (ukuran kecil)
     */
    public function optimizeThumbnail(
        UploadedFile $file,
        string $directory,
        int $width = 400,
        int $height = 300,
        array $options = []
    ): string {
        $options = array_merge($this->config, $options);

        $filename = Str::uuid() . '_thumb.' . $options['format'];
        $path = $directory . '/' . $filename;

        $image = Image::read($file->getRealPath());

        // Resize dan crop untuk thumbnail (cover mode)
        $image = $image->cover($width, $height);

        $encoded = $this->encodeImage($image, $options['format'], $options['quality']);

        Storage::disk('public')->put($path, $encoded);

        return $path;
    }

    /**
     * Buat multiple sizes untuk responsive images
     *
     * @return array Paths untuk masing-masing size
     */
    public function optimizeResponsive(
        UploadedFile $file,
        string $directory,
        array $sizes = [
            'sm' => ['width' => 400, 'height' => 300],
            'md' => ['width' => 800, 'height' => 600],
            'lg' => ['width' => 1200, 'height' => 900],
        ],
        array $options = []
    ): array {
        $paths = [];
        $baseFilename = Str::uuid();
        $options = array_merge($this->config, $options);

        foreach ($sizes as $size => $dimensions) {
            $filename = $baseFilename . '_' . $size . '.' . $options['format'];
            $path = $directory . '/' . $filename;

            $image = Image::read($file->getRealPath());

            // Resize dengan cover untuk maintain aspect ratio
            $image = $image->cover($dimensions['width'], $dimensions['height']);

            $encoded = $this->encodeImage($image, $options['format'], $options['quality']);

            Storage::disk('public')->put($path, $encoded);
            $paths[$size] = $path;
        }

        return $paths;
    }

    /**
     * Resize gambar jika melebihi dimensi maksimal
     */
    private function resizeIfNeeded(\Intervention\Image\Image $image, int $maxWidth, int $maxHeight): \Intervention\Image\Image
    {
        $currentWidth = $image->width();
        $currentHeight = $image->height();

        // Jika gambar lebih besar dari batas, resize dengan scaleDown
        if ($currentWidth > $maxWidth || $currentHeight > $maxHeight) {
            return $image->scaleDown($maxWidth, $maxHeight);
        }

        return $image;
    }

    /**
     * Encode gambar ke format yang diinginkan
     */
    private function encodeImage(\Intervention\Image\Image $image, string $format, int $quality): string
    {
        return match ($format) {
            'webp' => $image->toWebp($quality)->toString(),
            'jpg', 'jpeg' => $image->toJpeg($quality)->toString(),
            'png' => $image->toPng()->toString(),
            'avif' => $image->toAvif($quality)->toString(),
            default => $image->toWebp($quality)->toString(),
        };
    }

    /**
     * Hapus gambar dari storage
     */
    public function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Update konfigurasi default
     */
    public function setConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
}
