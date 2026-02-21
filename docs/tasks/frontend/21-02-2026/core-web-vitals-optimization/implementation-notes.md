# Implementation Notes

## Technical Decisions

1. **SEO fallback di layout**
   - Dipilih untuk memastikan semua halaman punya baseline meta tanpa mengandalkan disiplin manual per page.

2. **Schema global + page-specific**
   - Global (`WebSite`, `Person`) ditaruh di layout untuk konsistensi.
   - Context page-specific ditaruh di komponen Livewire masing-masing agar relevansi tetap tinggi.

3. **Route-name based cache matching**
   - Lebih robust terhadap perubahan path dibanding `Request::is()` string matching.

4. **Absolute media URL di sitemap/schema**
   - Dipilih untuk kompatibilitas crawler dan validator structured data.

## Trade-offs

- Menambahkan pemanggilan service (`app(SeoService::class)`) di beberapa Livewire component meningkatkan konsistensi SEO, dengan trade-off coupling ringan ke service layer.
- Tidak mengganti seluruh `<img>` sekaligus untuk menghindari blast radius besar di area admin; fokus pada halaman publik yang berdampak SEO.

## Risk Mitigation

- Perubahan diproteksi test di:
  - `tests/Feature/SeoServiceTest.php`
  - `tests/Feature/SeoMetaRenderingTest.php`
- Semua perubahan diformat ulang via Pint.
