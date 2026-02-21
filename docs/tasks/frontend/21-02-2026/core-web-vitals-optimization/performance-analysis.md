# Performance Analysis

## Core Web Vitals Focus

Optimasi difokuskan pada area yang mempengaruhi LCP dan TBT/FID mobile:

- konsistensi loading image
- pengurangan resource non-kritis pada initial render
- cache headers untuk route publik/SEO endpoints

## Image Optimization

### Changes

- `resources/views/components/optimized-image.blade.php` ditingkatkan:
  - support `fetchpriority="high"` untuk image prioritas
  - default `loading="lazy"` tetap dipakai untuk non-priority images
  - `decoding="async"` dipertahankan

- Penggunaan `x-optimized-image` diperluas ke page publik:
  - `resources/views/livewire/blog-page.blade.php`
  - `resources/views/livewire/blog-detail-page.blade.php`
  - `resources/views/livewire/project-filter.blade.php`
  - `resources/views/livewire/project-detail-page.blade.php`

### Expected Impact

- LCP lebih stabil untuk hero image/featured image (priority images)
- Pengurangan network contention dari image non-kritis (lazy load)

## Caching and Response Headers

### Changes

- `app/Http/Middleware/CacheHeaders.php`:
  - route matching publik diperjelas lewat route names
  - route `sitemap` dan `robots` tidak lagi tertimpa default no-cache branch
  - `Vary: Accept-Encoding` ditetapkan untuk endpoint SEO

### Expected Impact

- TTFB lebih konsisten untuk halaman publik di traffic berulang
- Crawl endpoint (`sitemap.xml`, `robots.txt`) lebih cache-friendly

## CSS/JS Minimization

- Bundling tetap mengikuti Vite existing flow (`@vite`) tanpa perubahan dependency.
- Tidak ada penambahan package frontend baru agar bundle baseline tetap terjaga.

## Verification Snapshot

- Test command: `php artisan test --compact tests/Feature/SeoMetaRenderingTest.php tests/Feature/SeoServiceTest.php`
- Result: 8 passed, 34 assertions, ~11s
