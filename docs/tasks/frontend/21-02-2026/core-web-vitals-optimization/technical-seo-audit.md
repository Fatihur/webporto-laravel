# Technical SEO Audit

## Meta Titles and Descriptions

### Before

- Hanya sebagian page memiliki `x-seo-meta`.
- Tidak ada fallback global ketika slot SEO tidak diisi.

### After

- Fallback meta otomatis ditambahkan di `resources/views/layouts/app.blade.php` lewat `<x-seo-meta />` jika slot SEO kosong.
- Page-level SEO ditambahkan/ditingkatkan pada:
  - `resources/views/livewire/home-page.blade.php`
  - `resources/views/livewire/blog-page.blade.php`
  - `resources/views/livewire/blog-detail-page.blade.php`
  - `resources/views/livewire/project-filter.blade.php`
  - `resources/views/livewire/project-detail-page.blade.php`
  - `resources/views/livewire/contact-page.blade.php`

## Open Graph and Twitter Cards

### Before

- Struktur dasar tersedia tapi kurang lengkap.

### After

- `resources/views/components/seo-meta.blade.php` sekarang mendukung:
  - `og:image:alt`
  - `og:locale`
  - `article:published_time` dan `article:modified_time`
  - `twitter:site` (jika tersedia di config)

## Canonical URLs

- Canonical tetap dipertahankan di `x-seo-meta` dan kini dipastikan aktif juga untuk page yang sebelumnya belum punya slot SEO eksplisit (via fallback layout).
- Canonical dinormalisasi agar tidak memuat query string, sehingga mencegah duplicate URL index untuk variasi parameter filter/tracking.

## Structured Data (JSON-LD)

### Global

- Layout sekarang memuat:
  - WebSite schema
  - Person schema

### Per Page

- Blog index: `CollectionPage` + breadcrumb
- Blog detail: `BlogPosting` + breadcrumb
- Project list: `CollectionPage` + breadcrumb
- Project detail: `SoftwareSourceCode` + breadcrumb
- Contact: `ContactPage` + breadcrumb

## robots.txt and sitemap.xml

### Sitemap

- `app/Services/SeoService.php` diperbarui:
  - Menyertakan seluruh kategori project dari `CategoryData::all()`
  - URL gambar dibuat absolut (`url(Storage::url(...))`)

### Robots

- Menambahkan `Disallow: /livewire/*`
- Menambahkan directive `Host: <app host>`
- Mempertahankan `Sitemap: ...`
- Menambahkan blok khusus untuk crawler modern (`GPTBot`, `ClaudeBot`) dengan policy publik yang sama dan pembatasan area admin.
