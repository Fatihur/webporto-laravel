# ArtaPortfolio - Laravel 11 Conversion

## Overview

Ini adalah konversi lengkap dari project ArtaPortfolio (React TypeScript) ke Laravel 11 dengan Blade templates. Semua fitur, desain, animasi, dan layout dipertahankan persis sama.

## Fitur yang Dikonversi

### 1. Pages/Routes
- **Home** (`/`) - Landing page dengan hero, stats, experience timeline
- **Projects Index** (`/projects`) - Semua project
- **Projects by Category** (`/projects/{category}`) - Filter berdasarkan kategori
- **Project Detail** (`/project/{id}`) - Detail project individual
- **Blog** (`/blog`) - Journal/artikel
- **Contact** (`/contact`) - Form kontak

### 2. Desain System
- **Color Palette**: Mint (#76D7A4), Violet (#C4A1FF), Lime (#E8FF8E), Zinc 950 (#09090b)
- **Typography**: Inter font (400, 500, 600, 700, 800)
- **Border Radius**: 2xl, 3xl, 4xl (2rem), 5xl (2.5rem)
- **Dark Mode**: Toggle dengan localStorage persistence

### 3. Animations & Interactions
- Mega menu dropdown dengan animasi slide-in
- Mobile sidebar off-canvas
- Hover effects pada cards (scale, border color changes)
- Smooth scroll behavior
- Theme toggle dengan icon transition

### 4. Components
- **Navbar**: Fixed header dengan backdrop blur, mega menu, mobile sidebar
- **Footer**: Simple footer dengan links
- **Layout**: Master layout dengan Tailwind CDN

## Struktur Project

```
artaportfolio-laravel11/
├── app/
│   ├── Data/
│   │   ├── BlogData.php          # Data blog posts
│   │   ├── CategoryData.php      # Data kategori project
│   │   └── ProjectData.php       # Data projects
│   ├── Http/
│   │   └── Controllers/
│   │       ├── HomeController.php
│   │       ├── ProjectController.php
│   │       ├── BlogController.php
│   │       └── ContactController.php
│   └── Models/
│       ├── BlogPost.php
│       ├── Category.php (Enum)
│       └── Project.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php     # Master layout
│       ├── components/
│       │   ├── navbar.blade.php  # Navigation component
│       │   └── footer.blade.php  # Footer component
│       ├── projects/
│       │   ├── index.blade.php   # Project list
│       │   └── show.blade.php    # Project detail
│       ├── blog/
│       │   └── index.blade.php
│       ├── contact/
│       │   └── index.blade.php
│       └── home.blade.php
└── routes/
    └── web.php
```

## Cara Menjalankan

1. **Install Dependencies** (jika belum):
```bash
cd artaportfolio-laravel11
composer install
```

2. **Copy Environment File**:
```bash
cp .env.example .env
php artisan key:generate
```

3. **Start Development Server**:
```bash
php artisan serve
```

4. **Browser**:
Buka http://localhost:8000

## Kategori Project

| Kategori | Icon | Warna | Deskripsi |
|----------|------|-------|-----------|
| Graphic Design | Palette | Mint | Visual identity, branding |
| Software Dev | Code | Black/White | Full-stack applications |
| Data Analysis | Chart | Violet | Data processing |
| Networking | Network | Lime | Infrastructure |

## Data Projects

1. **Neon Brand Identity** - Graphic Design
2. **Flow SaaS Platform** - Software Dev
3. **E-commerce Trends 2024** - Data Analysis
4. **HyperNet Infrastructure** - Networking

## Teknologi

- **Laravel 11** - PHP Framework
- **Tailwind CSS** - Styling (via CDN)
- **Lucide Icons** - Icon library (via CDN)
- **Inter Font** - Typography (Google Fonts)
- **Blade Templates** - Templating engine

## Perbedaan dengan React Version

| Aspek | React | Laravel |
|-------|-------|---------|
| Routing | React Router | Laravel Routes |
| State | useState, useEffect | PHP Data Classes |
| Components | JSX | Blade Components |
| Build | Vite | PHP Artisan Serve |
| Data | constants.tsx | Data Classes |

## Clean Code Principles

1. **Separation of Concerns**: Data, Controllers, dan Views terpisah
2. **DRY (Don't Repeat Yourself)**: Layout master dengan yield/section
3. **Single Responsibility**: Setiap controller handle satu resource
4. **Reusable Components**: Navbar dan footer sebagai components
5. **Type Safety**: PHP 8+ dengan typed properties

## Catatan

- Database tidak diperlukan karena menggunakan Data Classes (array-based)
- Semua images menggunakan placeholder dari Picsum Photos
- Contact form hanya menampilkan success message (bisa diextend untuk email)
