# Favicon Usage

## Files Included

| File | Purpose | Size |
|------|---------|------|
| `favicon.svg` | Main favicon (modern browsers) | Scalable |
| `apple-touch-icon.svg` | iOS/Android home screen icon | 180x180 |
| `site.webmanifest` | PWA manifest | - |

## Converting to PNG/ICO (Optional)

For older browser support, convert SVG to PNG/ICO:

### Online Tools
1. **SVG to PNG**: https://svgtopng.com/
2. **SVG to ICO**: https://convertio.co/svg-ico/
3. **Favicon Generator**: https://realfavicongenerator.net/

### Recommended Sizes
- `favicon-16x16.png` - Browser tabs
- `favicon-32x32.png` - Browser tabs (retina)
- `favicon.ico` - Legacy browsers (multi-size)
- `apple-touch-icon.png` - iOS devices (180x180)

### Using Inkscape (Command Line)
```bash
# Convert to PNG
inkscape favicon.svg --export-filename=favicon-32x32.png --export-width=32 --export-height=32
inkscape favicon.svg --export-filename=favicon-16x16.png --export-width=16 --export-height=16
inkscape apple-touch-icon.svg --export-filename=apple-touch-icon.png --export-width=180 --export-height=180
```

## Design

- **Letter**: "F" for Fatih
- **Colors**: Mint gradient (#76D7A4 to #5bc48a) on dark background (#09090b)
- **Style**: Modern, bold, tech-inspired
- **Font**: System font (Segoe UI, Arial, sans-serif)

## Browser Support

- ✅ Chrome 80+ (SVG favicon)
- ✅ Firefox 41+ (SVG favicon)
- ✅ Safari 9+ (SVG with fallback)
- ✅ Edge 80+ (SVG favicon)
- ⚠️ IE11 (Requires .ico fallback)
