# Webporto Laravel - Codebase Index

## Project Overview
A modern Laravel portfolio website with multi-language support, admin panel, and performance optimizations.

**Tech Stack:**
- **Backend:** Laravel 12.0, PHP 8.2+
- **Frontend:** Livewire 4.1, Tailwind CSS 4.0, Vite 7.0
-**Database:** MySQL/SQLite
- **Testing:** PHPUnit, Playwright (E2E)
- **Key Packages:** Intervention Image, Google Translate

---

## Directory Structure

### 📁 Core Application (`app/`)

#### Models (`app/Models/`)
- **[`Project.php`](app/Models/Project.php)** - Portfolio projects model
  - Translatable fields: title, description, content, meta fields
  - Scopes: `featured()`, `byCategory()`, `recent()`
  - Cache invalidation on save/delete
  
- **[`Blog.php`](app/Models/Blog.php)** - Blog posts model
  - Translatable fields: title, excerpt, content, meta fields
  - Scopes: `published()`, `drafts()`, `byCategory()`
  - Related posts functionality
  
- **[`Contact.php`](app/Models/Contact.php)** - Contact form submissions
- **[`Experience.php`](app/Models/Experience.php)** - Work experience entries
- **[`User.php`](app/Models/User.php)** - Admin users
- **[`Translation.php`](app/Models/Translation.php)** - Polymorphic translations

#### Livewire Components (`app/Livewire/`)

**Frontend Components:**
- **[`HomePage.php`](app/Livewire/HomePage.php)** - Main landing page
- **[`Navigation.php`](app/Livewire/Navigation.php)** - Site navigation with mega menu
- **[`ProjectFilter.php`](app/Livewire/ProjectFilter.php)** - Filter projects by category
- **[`ProjectDetailPage.php`](app/Livewire/ProjectDetailPage.php)** - Single project view
- **[`BlogPage.php`](app/Livewire/BlogPage.php)** - Blog listing with pagination
- **[`BlogDetailPage.php`](app/Livewire/BlogDetailPage.php)** - Single blog post
- **[`ContactPage.php`](app/Livewire/ContactPage.php)** - Contact page
- **[`ContactForm.php`](app/Livewire/ContactForm.php)** - Contact form handler
- **[`LanguageSwitcher.php`](app/Livewire/LanguageSwitcher.php)** - Locale switcher
- **[`ThemeToggle.php`](app/Livewire/ThemeToggle.php)** - Dark/light mode toggle
- **[`RandomQuote.php`](app/Livewire/RandomQuote.php)** - Dynamic quote display
- **[`StatsCounter.php`](app/Livewire/StatsCounter.php)** - Animated statistics

**Admin Components:**
- **[`Admin/Dashboard.php`](app/Livewire/Admin/Dashboard.php)** - Admin dashboard
- **[`Admin/Auth/Login.php`](app/Livewire/Admin/Auth/Login.php)** - Admin login
- **[`Admin/Projects/Index.php`](app/Livewire/Admin/Projects/Index.php)** - Projects list
- **[`Admin/Projects/Form.php`](app/Livewire/Admin/Projects/Form.php)** - Project CRUD
- **[`Admin/Blogs/Index.php`](app/Livewire/Admin/Blogs/Index.php)** - Blog posts list
- **[`Admin/Blogs/Form.php`](app/Livewire/Admin/Blogs/Form.php)** - Blog CRUD
- **[`Admin/Contacts/Index.php`](app/Livewire/Admin/Contacts/Index.php)** - Contact messages
- **[`Admin/Experiences/Index.php`](app/Livewire/Admin/Experiences/Index.php)** - Experience list
- **[`Admin/Experiences/Form.php`](app/Livewire/Admin/Experiences/Form.php)** - Experience CRUD

#### Services (`app/Services/`)
- **[`TranslationService.php`](app/Services/TranslationService.php)** - Google Translate integration
  - Auto-translation with caching
  - Rate limiting (60ms delay)
  - 30-day cache duration

#### Traits (`app/Traits/`)
- **[`Translatable.php`](app/Traits/Translatable.php)** - Auto-translation trait
  - Automatic field translation
  - Database caching of translations
  - Fallback to original on failure
  
- **[`CacheInvalidatable.php`](app/Traits/CacheInvalidatable.php)** - Cache management
  - Auto-clear cache on model save/delete
  - Abstract `clearModelCache()` method

#### Data Objects (`app/Data/`)
- **[`ProjectData.php`](app/Data/ProjectData.php)** - Static project data
- **[`BlogData.php`](app/Data/BlogData.php)** - Static blog data
- **[`CategoryData.php`](app/Data/CategoryData.php)** - Category definitions

#### Controllers (`app/Http/Controllers/`)
- **[`Controller.php`](app/Http/Controllers/Controller.php)** - Base controller
- **[`LocaleController.php`](app/Http/Controllers/LocaleController.php)** - Locale switching

#### Middleware (`app/Http/Middleware/`)
- **[`SetLocale.php`](app/Http/Middleware/SetLocale.php)** - Set application locale
- **[`CacheHeaders.php`](app/Http/Middleware/CacheHeaders.php)** - HTTP cache headers

---

### 📁 Views (`resources/views/`)

#### Layouts
- **[`layouts/app.blade.php`](resources/views/layouts/app.blade.php)** - Main frontend layout
- **[`layouts/admin.blade.php`](resources/views/layouts/admin.blade.php)** - Admin panel layout
- **[`layouts/auth.blade.php`](resources/views/layouts/auth.blade.php)** - Authentication layout

#### Components
- **[`components/navbar.blade.php`](resources/views/components/navbar.blade.php)** - Navigation bar
- **[`components/footer.blade.php`](resources/views/components/footer.blade.php)** - Footer
- **[`components/optimized-image.blade.php`](resources/views/components/optimized-image.blade.php)** - Image optimization
- **[`components/seo-meta.blade.php`](resources/views/components/seo-meta.blade.php)** - SEO meta tags

#### Livewire Views
- **[`livewire/home-page.blade.php`](resources/views/livewire/home-page.blade.php)** - Home page template
- **[`livewire/navigation.blade.php`](resources/views/livewire/navigation.blade.php)** - Navigation component
- **[`livewire/project-filter.blade.php`](resources/views/livewire/project-filter.blade.php)** - Project filter
- **[`livewire/project-detail-page.blade.php`](resources/views/views/livewire/project-detail-page.blade.php)** - Project detail
- **[`livewire/blog-page.blade.php`](resources/views/livewire/blog-page.blade.php)** - Blog listing
- **[`livewire/blog-detail-page.blade.php`](resources/views/livewire/blog-detail-page.blade.php)** - Blog post
- **[`livewire/contact-page.blade.php`](resources/views/livewire/contact-page.blade.php)** - Contact page
- **[`livewire/contact-form.blade.php`](resources/views/livewire/contact-form.blade.php)** - Contact form
- **[`livewire/language-switcher.blade.php`](resources/views/livewire/language-switcher.blade.php)** - Language switcher
- **[`livewire/theme-toggle.blade.php`](resources/views/livewire/theme-toggle.blade.php)** - Theme toggle
- **[`livewire/random-quote.blade.php`](resources/views/livewire/random-quote.blade.php)** - Quote display
- **[`livewire/stats-counter.blade.php`](resources/views/livewire/stats-counter.blade.php)** - Stats counter

#### Admin Views
- **[`livewire/admin/dashboard.blade.php`](resources/views/livewire/admin/dashboard.blade.php)** - Dashboard
- **[`livewire/admin/auth/login.blade.php`](resources/views/livewire/admin/auth/login.blade.php)** - Login form
- **[`livewire/admin/projects/index.blade.php`](resources/views/livewire/admin/projects/index.blade.php)** - Projects list
- **[`livewire/admin/projects/form.blade.php`](resources/views/livewire/admin/projects/form.blade.php)** - Project form
- **[`livewire/admin/blogs/index.blade.php`](resources/views/livewire/admin/blogs/index.blade.php)** - Blogs list
- **[`livewire/admin/blogs/form.blade.php`](resources/views/livewire/admin/blogs/form.blade.php)**
- **[`livewire/admin/contacts/index.blade.php`](resources/views/livewire/admin/contacts/index.blade.php)**
- **[`livewire/admin/experiences/index.blade.php`](resources/views/livewire/admin/experiences/index.blade.php)**
- **[`livewire/admin/experiences/form.blade.php`](resources/views/livewire/admin/experiences/form.blade.php)**

---

### 📁 Routes (`routes/`)

#### Web Routes ([`routes/web.php`](routes/web.php))

**Public Routes:**
- `GET /` - Home page
- `GET /projects` - Redirect to home
- `GET /projects/{category}` - Filter projects by category
- `GET /project/{slug}` - Single project
- `GET /blog` - Blog listing
- `GET /blog/{slug}` - Single blog post
- `GET /contact` - Contact page

**`Admin Routes (Protected):**
- `GET /admin/login` - Login (guest only)
- `POST /admin/logout` - Logout
- `GET /admin` - Dashboard
- `GET /admin/projects` - Projects list
- `GET /admin/projects/create` - Create project
- `GET /admin/projects/{id}/edit` - Edit project
- `GET /admin/blogs` - Blogs list
- `GET /admin/blogs/create` - Create blog
- `GET /admin/blogs/{id}/edit` - Edit blog
- `GET /admin/contacts` - Contact messages
- `GET /admin/experiences` - Experiences list
- `GET /admin/experiences/create` - Create experience
- `GET /admin/experiences/{id}/edit` - Edit experience

---

### 📁 Database (`database/`)

#### Migrations
- **`0001_01_01_000000_create_users_users_table.php`** - Users table
- **`0001_01_01_000001_create_cache_table.php`** - Cache table
- **`0001_01_01_000002_create_jobs_table.php`** - Queue jobs
- **`2026_02_14_123226_create_blogs_table.php`** - Blogs table
- **`2026_02_14_123229_create_projects_table.php`** - Projects table
- **`2026_02_14_123232_create_contacts_table.php`** - Contacts table
- **`2026_02_14_123235_create_experiences_table.php`** - Experiences table
- **`2026_02_14_154914_add_seo_fields_to_blogs_and_projects.php`** - SEO fields
- **`2026_02_15_000000_add_performance_indexes.php`** - Performance indexes
- **`2026_02_15_064257_add_link_to_projects_table.php`** - Project links
- **`2026_02_15_094737_create_translations_table.php`** - Translations

#### Seeders
- **`DatabaseSeeder.php`** - Main seeder
- **`AdminUserSeeder.php`** - Create admin user
- **`PortfolioDataSeeder.php`** - Seed portfolio data

#### Factories
- **`BlogFactory.php`** - Blog post factory
- **`ProjectFactory.php`** - Project factory
- **`ContactFactory.php`** - Contact factory
- **`ExperienceFactory.php`** - Experience factory
- **`UserFactory.php`** - User factory

---

### 📁 Configuration (`config/`)

- **`app.php`** - Application configuration
- **`auth.php`** - Authentication settings
- **`cache.php`** - Cache configuration
- **`database.php`** - Database settings
- **`filesystems.php`** - Filesystem configuration
- **`image.php`** - Image optimization settings
- **`livewire.php`** - Livewire configuration
- **`logging.php`** - Logging configuration
- **`mail.php`** - Mail settings
- **`queue.php`** - Queue configuration
- **`services.php`** - External services
- **`session.php`** - Session configuration
- **`translation.php`** - Translation settings

---

### 📁 Translations (`lang/`)

#### English (`lang/en/`)
- **`frontend.php`** - Frontend translations
- **`admin.php`** - Admin panel translations
- **`navigation.php`** - Navigation translations

#### Indonesian (`lang/id/`)
- **`frontend.php`** - Frontend translations
- **`admin.php`** - Admin panel translations
- **`navigation.php`** - Navigation translations

---

### 📁 E2E Tests (`e2e/`)

#### Pages (`e2e/pages/`)
- **`base.page.ts`** - Base page class
- **`index.ts`** - Page exports
- **`admin/dashboard.page.ts`** - Dashboard page object
- **`admin/login.page.ts`** - Login page object
- **`admin/projects.page.ts`** - Projects page object
- **`admin/blogs.page.ts`** - Blogs page object
- **`admin/EXperiences.page.ts`** - Experiences page object
- **`admin/contacts.page.ts`** - Contacts page object

#### Tests (`e2e/tests/`)
- **`admin/auth.spec.ts`** - Authentication tests
- **`admin/dashboard.spec.ts`** - Dashboard tests
- **`admin/projects.spec.ts`** - Projects CRUD tests
- **`admin/blogs.spec.ts`** - Blogs CRUD tests
- **`admin/experiences.spec.ts`** - Experiences CRUD tests
- **`admin/contacts.spec.ts`** - Contacts tests

#### Setup
- **`auth.setup.ts`** - Authentication test setup

---

## Key Features

### 1. Multi-Language Support
- **Supported Languages:** English (en), Indonesian (id)
- **Implementation:**
  - Static translations via Laravel's language files
  - Dynamic content translation via Google Translate API
  - Automatic translation caching (30 days)
  - Polymorphic translation storage in database

### 2. Caching Strategy
- **Cache Keys:**
  - `categories.all` - 24 hours
  - `projects.featured` - 1 hour
  - `experiences.ordered` - 6 hours
  - `blog.posts.page.{n}` - Blog pagination
  - `project.{slug}` - Individual projects
  - `blog.post.{slug}` - Individual blog posts
- **Auto-invalidation:** Models implement `CacheInvalidatable` trait

### 3. Image Optimization
- **Service:** [`ImageOptimizationService.php`](app/Services/ImageOptimizationService.php)
- **Features:**
  - Automatic WebP conversion
  - Quality optimization
  - Responsive image generation
  - Lazy loading support

### 4. SEO Optimization
- **Meta tags:** title, description, keywords
- **Open Graph tags**
- **Twitter Card tags**
- **Structured data (JSON-LD)**
- **Cache headers middleware**

### 5. Admin Panel
- **Authentication:** Laravel Auth
- **CRUD Operations:**
  - Projects management
  - Blog posts management
  - Experiences management
  - Contact messages viewing
- **Features:**
  - Real-time validation
  - Image uploads
  - Rich text editing
  - SEO field management

### 6. Performance Optimizations
- **Database Indexes:** Added for frequently queried fields
- **Eager Loading:** Prevents N+1 queries
- **Query Caching:** Reduces database load
- **Image Optimization:** Reduces bandwidth
- **Lazy Loading:** Improves initial page load

---

## Available Commands

### Composer Scripts
```bash
composer setup          # Full project setup
composer dev            # Run development server
composer test           # Run tests
```

### NPM Scripts
```bash
npm run build           # Build for production
npm run dev             # Run Vite dev server
npm run test:e2e        # Run E2E tests
npm run test:e2e:headed # Run E2E tests with UI
npm run test:e2e:debug  # Debug E2E tests
npm run test:admin      # Run admin E2E tests
npm run test:auth       # Run auth E2E tests
```

### Artisan Commands
```bash
php artisan serve                    # Start development server
php artisan migrate                  # Run migrations
php artisan db:seed                  # Seed database
php artisan optimize:clear           # Clear optimizations
php artisan cache:clear              # Clear cache
php artisan queue:work               # Process queue jobs
php artisan pail                    # View logs
```

---

## Environment Variables

Key `.env` variables:
- `APP_NAME` - Application name
- `APP_ENV` - Environment (local/production)
- `APP_KEY` - Application key
- `APP_URL` - Application URL
- `APP_LOCALE` - Default locale (en/id)
- `DB_CONNECTION` - Database connection
- `DB_DATABASE` - Database name
- `CACHE_DRIVER` - Cache driver (file/redis)
- `QUEUE_CONNECTION` - Queue driver

---

## Development Workflow

1. **Setup:**
   ```bash
   composer setup
   ```

2. **Development:**
   ```bash
   composer dev
   ```

3. **Testing:**
   ```bash
   composer test
   npm run test:e2e
   ```

4. **Deployment:**
   ```bash
   npm run build
   php artisan optimize
   ```

---

## Security Features

- CSRF protection
- XSS protection
- SQL injection prevention (Eloquent ORM)
- Authentication middleware
- Guest middleware for login page
- Input validation
- File upload validation

---

## Performance Metrics

- **Page Load:** < 2s (optimized images)
- **Database Queries:** Minimized via caching
- **Translation Cache:** 30 days
- **Image Compression:** WebP format
- **Cache Hit Rate:** High (static content cached)

---

## Future Enhancements

- [ ] API endpoints for mobile app
- [ ] Real-time notifications
- [ ] Advanced search functionality
- [ ] Comments system for blogs
- [ ] Social sharing integration
- [ ] Analytics dashboard
- [ ] Multi-user admin support
- [ ] Role-based permissions
- [ ] Email notifications
- [ ] Backup/restore functionality

---

## Documentation

- **Laravel Docs:** https://laravel.com/docs
- **Livewire Docs:** https://livewire.laravel.com
- **Tailwind