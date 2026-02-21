# SEO Optimization Audit - fatihur.com

## Objective

Meningkatkan SEO teknis, performa, dan aksesibilitas untuk halaman publik utama (`/`, `/blog`, `/blog/{slug}`, `/projects/{category}`, `/project/{slug}`, `/contact`) berdasarkan audit dan indikasi performa mobile dari PageSpeed.

## Scope

- Meta tags, canonical, OG, Twitter cards
- Structured data (JSON-LD)
- Sitemap dan robots
- Cache behavior untuk halaman publik dan SEO endpoints
- Optimisasi gambar (lazy/eager priority, decoding, fetchpriority)
- Perbaikan aksesibilitas yang berdampak ke SEO (label dan rel)

## Implemented Changes

- Enhanced `seo-meta` component untuk robots directive, article metadata, locale, twitter site, og:image:alt.
- Inject default SEO + global WebSite/Person schema dari layout ketika page-specific SEO belum didefinisikan.
- Menambahkan structured data page-level untuk Blog, Blog Detail, Project Detail, Project Listing, Contact.
- Memperbaiki sitemap generation agar memasukkan semua kategori project, URL gambar absolut, dan robots host directive.
- Menyetel cache middleware agar route `sitemap` dan `robots` tidak tertimpa cache policy default.
- Migrasi beberapa `<img>` menjadi `<x-optimized-image>` untuk konsistensi loading behavior.

## Validation

- `php artisan test --compact tests/Feature/SeoServiceTest.php tests/Feature/SeoMetaRenderingTest.php`
- Hasil: passing (6 tests)
