# Performance Optimization Guide

## Hasil Optimasi yang Sudah Dilakukan

### 1. Critical CSS Inline
- CSS kritis untuk above-the-fold content dimasukkan inline di `<head>`
- Mengurangi render-blocking resources
- Font di-preload untuk first paint yang lebih cepat

### 2. Async Loading
- Tailwind CSS dimuat dengan `defer`
- Google Fonts dimuat dengan `preload` + `onload` async pattern
- MathJax hanya dimuat di halaman yang membutuhkan (blog dengan formula)

### 3. Font Optimization
- Font Inter di-preload dengan `font-display: swap`
- Unicode-range subsetting untuk mengurangi ukuran font
- Preconnect ke font CDN

### 4. Caching Headers
- Static assets: 1 year cache (immutable)
- Public pages: 5 menit dengan stale-while-revalidate
- Preload headers untuk critical fonts

### 5. Image Optimization
- Lazy loading untuk semua gambar
- Content-visibility untuk off-screen content
- Async decoding

## Konfigurasi Server (Hosting)

### Untuk Apache (.htaccess)

Tambahkan file `.htaccess` di root:

```apache
# Enable Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript text/javascript application/json
    AddOutputFilterByType DEFLATE text/xml application/xml text/x-component
    AddOutputFilterByType DEFLATE application/xhtml+xml application/rss+xml application/atom+xml
    AddOutputFilterByType DEFLATE image/svg+xml font/truetype font/opentype application/vnd.ms-fontobject
</IfModule>

# Enable Browser Caching
<IfModule mod_expires.c>
    ExpiresActive On

    # Images
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"

    # Fonts
    ExpiresByType font/ttf "access plus 1 year"
    ExpiresByType font/otf "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType application/font-woff2 "access plus 1 year"

    # CSS & JS
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType text/javascript "access plus 1 year"
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

### Untuk Nginx

```nginx
# Gzip Compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_proxied expired no-cache no-store private auth;
gzip_types
    text/plain
    text/css
    text/xml
    text/javascript
    application/javascript
    application/xml+rss
    application/json
    image/svg+xml
    font/truetype
    font/opentype
    application/vnd.ms-fontobject;

# Browser Caching
location ~* \.(jpg|jpeg|png|gif|webp|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    add_header Vary "Accept-Encoding";
}

location ~* \.(css|js)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    add_header Vary "Accept-Encoding";
}

location ~* \.(woff|woff2|ttf|otf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    add_header Access-Control-Allow-Origin "*";
}

# Security Headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

## Rekomendasi Tambahan

### 1. Image Optimization
- Gunakan format WebP untuk gambar baru
- Kompres gambar sebelum upload (TinyPNG, Squoosh)
- Pertimbangkan menggunakan CDN untuk gambar (Cloudflare, Imgix)

### 2. CDN Usage
- Aktifkan Cloudflare untuk caching global
- Gunakan Cloudflare Workers untuk edge optimization
- Enable Auto Minify di Cloudflare (CSS, JS, HTML)

### 3. Database Optimization
- Enable query caching di MySQL
- Use Redis untuk session dan cache
- Optimize eager loading di Laravel

### 4. Monitoring
- Gunakan Laravel Telescope untuk development
- Monitor dengan New Relic atau Laravel Pulse
- Track Core Web Vitals di Google Search Console

## Checklist Sebelum Deploy

- [ ] Jalankan `php artisan optimize`
- [ ] Jalankan `php artisan config:cache`
- [ ] Jalankan `php artisan route:cache`
- [ ] Jalankan `php artisan view:cache`
- [ ] Pastikan compression enabled di server
- [ ] Test di PageSpeed Insights
- [ ] Check Core Web Vitals
