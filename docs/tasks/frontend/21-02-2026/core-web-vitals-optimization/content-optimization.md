# Content Optimization

## Keyword Coverage Improvements

Meta descriptions dan keywords diperkuat untuk halaman dengan intent utama:

- Home: positioning portfolio lintas domain (software, design, data, networking)
- Blog: intent edukasi dan insight teknologi/desain
- Contact: intent konversi (kolaborasi, hire)
- Projects: intent showcase/case study

## Internal Linking Structure

Struktur internal linking utama tetap dipertahankan:

- Navbar: Home / Projects / Blog / Contact
- Related content pada detail page (related posts, related projects)
- CTA lintas halaman (blog -> contact, project -> related)

Perbaikan dilakukan pada sinyal semantic page-level melalui structured data breadcrumbs, yang membantu mesin pencari memahami relasi antar halaman.

## URL Structure

URL structure sudah konsisten dan SEO-friendly:

- `/blog/{slug}`
- `/project/{slug}`
- `/projects/{category}`

Tidak ada perubahan path untuk menghindari risiko regression terhadap indexing yang sudah ada.

## Alt Text Coverage

Untuk halaman publik yang dioptimasi, gambar utama sudah menggunakan alt deskriptif berbasis judul konten saat tersedia.

Update tambahan:

- Alt text pada hasil pencarian (`resources/views/livewire/search-component.blade.php`) kini deskriptif (`thumbnail`/`cover image` berbasis judul konten).
- Alt text pada listing admin blog/project yang sebelumnya kosong kini diisi deskriptif untuk mengurangi a11y lint noise dan menjaga konsistensi kualitas markup.

Catatan: masih ada beberapa image preview editor/form yang bersifat helper internal; prioritas SEO tetap pada halaman publik.
