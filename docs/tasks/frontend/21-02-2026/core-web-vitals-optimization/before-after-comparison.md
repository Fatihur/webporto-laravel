# Before vs After Comparison

## Technical SEO

### Before

- Beberapa halaman publik tidak punya SEO slot eksplisit.
- Structured data belum konsisten per halaman.
- Sitemap kategori project masih parsial.

### After

- Semua halaman publik mendapat baseline meta via fallback layout.
- Structured data tersedia global + per-page (blog/project/contact/listing).
- Sitemap memuat seluruh kategori project dari sumber data kategori.
- Canonical URL lebih bersih karena query string tidak ikut ditandai sebagai URL utama.
- Robots dan sitemap caching behavior lebih predictable.
- robots policy eksplisit untuk `GPTBot` dan `ClaudeBot`.

## Performance Signals

### Before

- Optimasi image belum konsisten antar template.
- Prioritas loading LCP image belum eksplisit.
- Cache behavior endpoint SEO berpotensi tertimpa middleware default.

### After

- Komponen `optimized-image` mendukung `fetchpriority` dan dipakai di halaman publik inti.
- Image prioritas memakai eager + high fetch priority.
- Endpoint `sitemap`/`robots` mempertahankan cache directive yang benar.
- Sejumlah image card/search kini memakai lazy+async dan alt text deskriptif.

## Test Evidence

- SEO-related tests: **8 passed**
  - `tests/Feature/SeoServiceTest.php`
  - `tests/Feature/SeoMetaRenderingTest.php`

## Expected SEO Outcome

- Cakupan metadata lebih lengkap untuk indexing dan rich preview.
- Data terstruktur lebih kaya untuk understanding entity/page context.
- Potensi peningkatan crawl efficiency dan mobile render performance.

Catatan: Nilai PageSpeed final perlu divalidasi ulang setelah deploy/build production environment karena skor sangat dipengaruhi kondisi server, cache edge, dan third-party runtime saat pengujian.
