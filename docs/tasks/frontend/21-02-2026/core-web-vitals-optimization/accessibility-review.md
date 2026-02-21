# Accessibility Review (SEO Impact)

## WCAG-Oriented Fixes with SEO Impact

### Implemented

- Menambahkan `rel="noopener noreferrer"` untuk link eksternal WhatsApp pada hero (`resources/views/livewire/home-page.blade.php`).
- Menjaga `aria-label` pada elemen interaktif yang sudah ada (chat toggle, menu, social links).
- Memastikan semantic heading hierarchy tetap konsisten pada page utama (single H1 per page section context).
- Menambahkan skip link keyboard (`Skip to main content`) di `resources/views/layouts/app.blade.php`.
- Melengkapi alt text di beberapa image yang sebelumnya kosong pada komponen pencarian dan listing admin.

## Semantic HTML

- Struktur semantic utama (`<main>`, `<header>`, `<article>`, `<aside>`, `<section>`) sudah dipakai dengan baik di halaman publik.
- Structured data breadcrumb menambah konteks semantic untuk crawler.

## Keyboard Navigation

- Komponen interaktif mayoritas sudah berupa `<button>` / `<a>` sehingga keyboard focus native tetap tersedia.
- Tidak ada perubahan JavaScript yang menurunkan akses keyboard pada flow existing.

## Outstanding Opportunities

- Audit lanjutan untuk memastikan semua custom interactive div pada area chat widget memiliki fallback keyboard behavior yang setara.
- Penyesuaian alt text di area admin bisa dilakukan terpisah jika target termasuk SEO media indexing untuk backend previews.
