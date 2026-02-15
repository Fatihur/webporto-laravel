# Webporto Laravel - Feature Implementation Plan

## Overview

This document outlines a comprehensive implementation plan for adding new features to the Webporto Laravel portfolio project. The plan is organized into 9 phases, each focusing on a specific area of functionality.

---

## Phase 1: Database & Infrastructure

### 1.1 Database Migrations

#### Comments Table
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('blog_id')->constrained()->onDelete('cascade');
    $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
    $table->string('name');
    $table->string('email');
    $table->text('content');
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent')->nullable();
    $table->enum('status', ['pending', 'approved', 'spam', 'trash'])->default('pending');
    $table->timestamps();
    $table->index(['blog_id', 'status']);
    $table->index('email');
});
```

#### Page Views Table
```php
Schema::create('page_views', function (Blueprint $table) {
    $table->id();
    $table->morphs('viewable'); // viewable_type, viewable_id
    $table->string('session_id', 100);
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent')->nullable();
    $table->string('referrer')->nullable();
    $table->string('country', 2)->nullable();
    $table->string('city')->nullable();
    $table->timestamps();
    $table->index(['viewable_type', 'viewable_id']);
    $table->index('session_id');
    $table->index('created_at');
});
```

#### Newsletter Subscribers Table
```php
Schema::create('newsletter_subscribers', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('name')->nullable();
    $table->timestamp('subscribed_at');
    $table->timestamp('unsubscribed_at')->nullable();
    $table->string('unsubscribe_token', 64)->unique();
    $table->enum('status', ['active', 'unsubscribed', 'bounced'])->default('active');
    $table->timestamps();
});
```

#### Social Accounts Table
```php
Schema::create('social_accounts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('provider'); // google, github, etc.
    $table->string('provider_id');
    $table->string('provider_token')->nullable();
    $table->string('provider_refresh_token')->nullable();
    $table->timestamps();
    $table->unique(['provider', 'provider_id']);
});
```

#### Image Galleries Table
```php
Schema::create('image_galleries', function (Blueprint $table) {
    $table->id();
    $table->morphs('gallerable'); // For projects, blogs, etc.
    $table->string('image_path');
    $table->string('thumbnail_path')->nullable();
    $table->string('medium_path')->nullable();
    $table->string('large_path')->nullable();
    $table->string('alt_text')->nullable();
    $table->string('title')->nullable();
    $table->integer('order')->default(0);
    $table->timestamps();
    $table->index(['gallerable_type', 'gallerable_id']);
});
```

#### SEO Meta Table
```php
Schema::create('seo_metas', function (Blueprint $table) {
    $table->id();
    $table->morphs('seoable');
    $table->string('canonical_url')->nullable();
    $table->enum('robots_index', ['index', 'noindex'])->default('index');
    $table->enum('robots_follow', ['follow', 'nofollow'])->default('follow');
    $table->json('structured_data')->nullable();
    $table->timestamps();
});
```

#### Two-Factor Authentication
```php
// Add to users table migration
$table->string('two_factor_secret', 100)->nullable();
$table->text('two_factor_recovery_codes')->nullable();
$table->timestamp('two_factor_confirmed_at')->nullable();
```

#### Sessions Management
```php
// Add to users table migration
$table->string('password_reset_token', 64)->nullable();
$table->timestamp('password_reset_expires')->nullable();
```

### 1.2 Redis Configuration

#### Install Redis
```bash
composer require predis/predis
```

#### Configure Cache
```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

#### Configure Queue
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'redis'),

'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

### 1.3 Queue Jobs Setup

#### Jobs to Create
- `ProcessImageOptimization` - Handle image resizing
- `ProcessTranslation` - Handle batch translations
- `SendContactEmail` - Send contact form emails
- `SendNewsletterEmail` - Send newsletter emails
- `GenerateSitemap` - Generate XML sitemap
- `TrackPageView` - Track page views asynchronously

---

## Phase 2: Blog & Content Features

### 2.1 Comments System

#### Model: `Comment.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'blog_id', 'parent_id', 'name', 'email', 
        'content', 'ip_address', 'user_agent', 'status'
    ];

    // Relationships
    public function blog() { return $this->belongsTo(Blog::class); }
    public function parent() { return $this->belongsTo(Comment::class, 'parent_id'); }
    public function replies() { return $this->hasMany(Comment::class, 'parent_id'); }

    // Scopes
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
}
```

#### Livewire Component: `CommentForm.php`
```php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Comment;
use Illuminate\Support\Facades\Session;

class CommentForm extends Component
{
    public $blogId;
    public $name = '';
    public $email = '';
    public $content = '';
    public $parentId = null;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:255',
        'content' => 'required|min:10|max:2000',
    ];

    public function submit()
    {
        $this->validate();

        Comment::create([
            'blog_id' => $this->blogId,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'email' => $this->email,
            'content' => $this->content,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'pending',
        ]);

        // Clear form
        $this->reset(['name', 'email', 'content', 'parentId']);
        
        session()->flash('message', 'Comment submitted for review.');
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
```

#### Admin: `Admin/Comments/Index.php`
- List all comments with filtering
- Approve/Reject/Spam actions
- Bulk actions
- Reply functionality

### 2.2 Tech Stack Filter

#### Update Project Model
```php
// Add scope for tech stack filtering
public function scopeByTechStack($query, array $techStacks)
{
    return $query->where(function ($q) use ($techStacks) {
        foreach ($techStacks as $tech) {
            $q->orWhereJsonContains('tech_stack', $tech);
        }
    });
}
```

#### Update ProjectFilter Component
```php
// Add tech stack filter
public array $selectedTechStacks = [];
public array $availableTechStacks = [];

public function mount()
{
    // Get unique tech stacks from all projects
    $this->availableTechStacks = Project::all()
        ->pluck('tech_stack')
        ->flatten()
        ->unique()
        ->values()
        ->toArray();
}

public function updatedSelectedTechStacks()
{
    $this->filterProjects();
}
```

---

## Phase 3: Search & Analytics

### 3.1 Scout/Meilisearch Setup

#### Install Packages
```bash
composer require laravel/scout
composer require meilisearch/meilisearch-php
```

#### Configure Scout
```php
// config/scout.php
'driver' => env('SCOUT_DRIVER', 'meilisearch'),

'meilisearch' => [
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key' => env('MEILISEARCH_KEY', null),
],
```

#### Make Models Searchable
```php
// app/Models/Project.php
use Laravel\Scout\Searchable;

class Project extends Model
{
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'category' => $this->category,
            'tech_stack' => $this->tech_stack,
            'tags' => $this->tags,
        ];
    }
}

// app/Models/Blog.php
class Blog extends Model
{
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category' => $this->category,
            'author' => $this->author,
        ];
    }
}
```

### 3.2 Search Livewire Component

#### `SearchComponent.php`
```php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Blog;

class SearchComponent extends Component
{
    use WithPagination;

    public string $query = '';
    public string $type = 'all'; // all, projects, blogs
    public array $filters = [];

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function search()
    {
        if (strlen($this->query) < 3) {
            return collect();
        }

        $results = collect();

        if ($this->type === 'all' || $this->type === 'projects') {
            $projects = Project::search($this->query)
                ->query(function ($builder) {
                    $builder->with('translations');
                })
                ->get();
            $results = $results->merge($projects->map(fn($p) => [
                'type' => 'project',
                'data' => $p,
            ]));
        }

        if ($this->type === 'all' || $this->type === 'blogs') {
            $blogs = Blog::search($this->query)
                ->query(function ($builder) {
                    $builder->published()->with('translations');
                })
                ->get();
            $results = $results->merge($blogs->map(fn($b) => [
                'type' => 'blog',
                'data' => $b,
            ]));
        }

        return $results;
    }

    public function render()
    {
        return view('livewire.search-component', [
            'results' => $this->search(),
        ]);
    }
}
```

### 3.3 Page Views Tracking

#### Model: `PageView.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'viewable_type', 'viewable_id', 'session_id',
        'ip_address', 'user_agent', 'referrer',
        'country', 'city'
    ];

    public function viewable()
    {
        return $this->morphTo();
    }

    // Get view count for a model
    public static function getCount($model): int
    {
        return static::where('viewable_type', get_class($model))
            ->where('viewable_id', $model->id)
            ->count();
    }

    // Get unique view count
    public static function getUniqueCount($model): int
    {
        return static::where('viewable_type', get_class($model))
            ->where('viewable_id', $model->id)
            ->distinct('session_id')
            ->count('session_id');
    }
}
```

#### Middleware: `TrackPageViews.php`
```php
namespace App\Http\Middleware;

use Closure;
use App\Models\PageView;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;

class TrackPageViews
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests
        if ($request->isMethod('get')) {
            // Dispatch job to track view asynchronously
            dispatch(function () use ($request) {
                $route = $request->route();
                $parameter = $route->parameter('project') ?? $route->parameter('blog');
                
                if ($parameter && method_exists($parameter, 'pageViews')) {
                    $location = Location::get($request->ip());
                    
                    $parameter->pageViews()->create([
                        'session_id' => Session::getId(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'referrer' => $request->header('referer'),
                        'country' => $location?->countryCode,
                        'city' => $location?->cityName,
                    ]);
                }
            });
        }

        return $response;
    }
}
```

### 3.4 Popular Content

#### Add to Models
```php
// Project.php
public function scopePopular($query, $days = 30)
{
    return $query->withCount(['pageViews as recent_views' => function ($q) use ($days) {
        $q->where('created_at', '>=', now()->subDays($days));
    }])->orderByDesc('recent_views');
}

// Blog.php
public function scopePopular($query, $days = 30)
{
    return $query->withCount(['pageViews as recent_views' => function ($q) use ($days) {
        $q->where('created_at', '>=', now()->subDays($days));
    }])->orderByDesc('recent_views');
}
```

### 3.5 Visitor Demographics

#### Analytics Service
```php
namespace App\Services;

use App\Models\PageView;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDemographics($days = 30): array
    {
        return [
            'countries' => PageView::where('created_at', '>=', now()->subDays($days))
                ->select('country', DB::raw('count(*) as total'))
                ->groupBy('country')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            
            'cities' => PageView::where('created_at', '>=', now()->subDays($days))
                ->select('city', 'country', DB::raw('count(*) as total'))
                ->groupBy('city', 'country')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            
            'daily_views' => PageView::where('created_at', '>=', now()->subDays($days))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }
}
```

---

## Phase 4: Image Management

### 4.1 Multiple Image Sizes

#### Update ImageOptimizationService
```php
namespace App\Services;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class ImageOptimizationService
{
    protected array $sizes = [
        'thumbnail' => [150, 150],
        'medium' => [400, 400],
        'large' => [800, 800],
        'original' => null,
    ];

    public function processImage($file, string $path): array
    {
        $paths = [];
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        foreach ($this->sizes as $sizeName => $dimensions) {
            $image = ImageManager::imagick()->read($file);
            
            if ($dimensions) {
                $image->cover($dimensions[0], $dimensions[1]);
            }
            
            $image->toWebp(quality: 80);
            
            $sizePath = "{$path}/{$sizeName}/{$filename}";
            Storage::put($sizePath, (string) $image->encode());
            
            $paths[$sizeName . '_path'] = $sizePath;
        }

        return $paths;
    }
}
```

### 4.2 CDN Integration

#### Configure Filesystems
```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    ],
    
    'cloudflare' => [
        'driver' => 's3',
        'key' => env('CLOUDFLARE_R2_ACCESS_KEY_ID'),
        'secret' => env('CLOUDFLARE_R2_SECRET_ACCESS_KEY'),
        'region' => env('CLOUDFLARE_R2_REGION', 'auto'),
        'bucket' => env('CLOUDFLARE_R2_BUCKET'),
        'url' => env('CLOUDFLARE_R2_URL'),
        'endpoint' => env('CLOUDFLARE_R2_ENDPOINT'),
    ],
],

'cloud' => env('FILESYSTEM_CLOUD', 's3'),
```

### 4.3 Image Gallery with Drag-and-Drop

#### Livewire Component: `ImageGallery.php`
```php
namespace App\Livewire;

use Livewire\Component;
use App\Models\ImageGallery;
use App\Services\ImageOptimizationService;

class ImageGallery extends Component
{
    public $gallerableType;
    public $gallerableId;
    public $images = [];
    public $altText = [];

    protected $listeners = [
        'imageUploaded' => 'handleImageUpload',
        'imageReordered' => 'handleReorder',
    ];

    public function mount($gallerableType, $gallerableId)
    {
        $this->gallerableType = $gallerableType;
        $this->gallerableId = $gallerableId;
        $this->loadImages();
    }

    public function loadImages()
    {
        $this->images = ImageGallery::where('gallerable_type', $this->gallerableType)
            ->where('gallerable_id', $this->gallerableId)
            ->orderBy('order')
            ->get();
    }

    public function handleReorder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            ImageGallery::where('id', $id)->update(['order' => $index]);
        }
        $this->loadImages();
    }

    public function updateAltText($imageId, $altText)
    {
        ImageGallery::where('id', $imageId)->update(['alt_text' => $altText]);
    }

    public function deleteImage($imageId)
    {
        $image = ImageGallery::find($imageId);
        // Delete files from storage
        Storage::delete([
            $image->image_path,
            $image->thumbnail_path,
            $image->medium_path,
            $image->large_path,
        ]);
        $image->delete();
        $this->loadImages();
    }

    public function render()
    {
        return view('livewire.image-gallery');
    }
}
```

#### Frontend JavaScript (Alpine.js)
```javascript
// Drag and drop functionality
x-data="{
    draggedItem: null,
    dragstart(e, id) {
        this.draggedItem = id;
        e.dataTransfer.effectAllowed = 'move';
    },
    dragover(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    },
    drop(e, targetId) {
        e.preventDefault();
        if (this.draggedItem !== targetId) {
            this.reorderItems(this.draggedItem, targetId);
        }
    },
    reorderItems(fromId, toId) {
        // Emit to Livewire
        $dispatch('image-reordered', { from: fromId, to: toId });
    }
}"
```

---

## Phase 5: SEO & Performance

### 5.1 XML Sitemap Generation

#### Command: `GenerateSitemap.php`
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Blog;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Static pages
        $sitemap->add(Url::create(route('home'))
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(1.0));

        $sitemap->add(Url::create(route('blog.index'))
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.8));

        $sitemap->add(Url::create(route('contact.index'))
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.5));

        // Projects
        Project::each(function ($project) use ($sitemap) {
            $sitemap->add(Url::create(route('projects.show', $project->slug))
                ->setLastModificationDate($project->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.7));
        });

        // Blogs
        Blog::published()->each(function ($blog) use ($sitemap) {
            $sitemap->add(Url::create(route('blog.show', $blog->slug))
                ->setLastModificationDate($blog->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6));
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
```

### 5.2 Robots.txt Management

#### Controller: `RobotsController.php`
```php
namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $robotsTxt = app()->environment('production')
            ? "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\n\nSitemap: " . url('sitemap.xml')
            : "User-agent: *\nDisallow: /";

        return response($robotsTxt)
            ->header('Content-Type', 'text/plain');
    }
}
```

#### Route
```php
Route::get('robots.txt', [RobotsController::class, 'index'])->name('robots');
```

### 5.3 Structured Data (JSON-LD)

#### Component: `StructuredData.php`
```php
namespace App\View\Components;

use Illuminate\View\Component;

class StructuredData extends Component
{
    public $type;
    public $data;

    public function __construct($type = 'Person', $data = [])
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function render()
    {
        return view('components.structured-data', [
            'jsonLd' => $this->generateJsonLd(),
        ]);
    }

    protected function generateJsonLd(): string
    {
        $data = match ($this->type) {
            'Person' => [
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => $this->data['name'] ?? config('app.name'),
                'url' => url('/'),
                'image' => $this->data['image'] ?? null,
                'jobTitle' => $this->data['role'] ?? null,
                'email' => $this->data['email'] ?? null,
            ],
            'Organization' => [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => url('/'),
                'logo' => $this->data['logo'] ?? null,
            ],
            'Article' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $this->data['title'],
                'datePublished' => $this->data['published_at'],
                'dateModified' => $this->data['updated_at'],
                'author' => [
                    '@type' => 'Person',
                    'name' => $this->data['author'],
                ],
            ],
            default => [],
        };

        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }
}
```

### 5.4 Canonical URLs & Meta Robots

#### Update SEO Meta Component
```php
// resources/views/components/seo-meta.blade.php
@php
    $canonical = $canonical ?? request()->url();
    $robotsIndex = $robotsIndex ?? 'index';
    $robotsFollow = $robotsFollow ?? 'follow';
@endphp

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
@if(!empty($keywords))
<meta name="keywords" content="{{ $keywords }}">
@endif

<!-- Canonical URL -->
<link rel="canonical" href="{{ $canonical }}">

<!-- Robots Meta -->
<meta name="robots" content="{{ $robotsIndex }}, {{ $robotsFollow }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
@if(!empty($image))
<meta property="og:image" content="{{ $image }}">
@endif

<!-- Twitter -->
<meta property="twitter:card" content="{{ $twitterCard ?? 'summary_large_image' }}">
<meta property="twitter:url" content="{{ $canonical }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
@if(!empty($image))
<meta property="twitter:image" content="{{ $image }}">
@endif
```

### 5.5 Cache Tags Implementation

#### Custom Cache Service
```php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public function rememberWithTags(array $tags, string $key, int $ttl, callable $callback)
    {
        if (config('cache.default') === 'redis') {
            // Use Redis cache tags
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        // Fallback for non-Redis caches
        $prefixedKey = implode(':', $tags) . ':' . $key;
        return Cache::remember($prefixedKey, $ttl, $callback);
    }

    public function flushTags(array $tags): void
    {
        if (config('cache.default') === 'redis') {
            Cache::tags($tags)->flush();
            return;
        }

        // For non-Redis, we need to track keys manually
        // This is a simplified approach
        foreach ($tags as $tag) {
            $keys = Cache::get("tag:{$tag}:keys", []);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            Cache::forget("tag:{$tag}:keys");
        }
    }
}
```

---

## Phase 6: Security & Authentication

### 6.1 Two-Factor Authentication

#### Install Package
```bash
composer require laravel/fortify
```

#### Configure Fortify
```php
// config/fortify.php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirmPassword' => true,
    ]),
],
```

#### Livewire Component: `TwoFactorSetup.php`
```php
namespace App\Livewire\Admin\Auth;

use Livewire\Component;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;

class TwoFactorSetup extends Component
{
    public $showingQr = false;
    public $code = '';
    public $recoveryCodes = [];

    public function enableTwoFactor()
    {
        $user = auth()->user();
        $user->two_factor_secret = encrypt(app('pragmarx.google2fa')->generateSecretKey());
        $user->two_factor_recovery_codes = encrypt(json_encode(
            collect(range(1, 8))->map(fn() => RecoveryCode::generate())->all()
        ));
        $user->save();

        $this->showingQr = true;
    }

    public function confirmTwoFactor()
    {
        $this->validate(['code' => 'required|string|size:6']);
        
        $user = auth()->user();
        $valid = app('pragmarx.google2fa')->verifyKey(
            decrypt($user->two_factor_secret),
            $this->code
        );

        if ($valid) {
            $user->two_factor_confirmed_at = now();
            $user->save();
            session()->flash('message', 'Two-factor authentication enabled.');
        } else {
            $this->addError('code', 'Invalid verification code.');
        }
    }

    public function render()
    {
        return view('livewire.admin.auth.two-factor-setup');
    }
}
```

### 6.2 Password Reset

#### Livewire Component: `ForgotPassword.php`
```php
namespace App\Livewire\Admin\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPassword extends Component
{
    public $email = '';
    public $emailSent = false;

    protected $rules = [
        'email' => 'required|email|exists:users,email',
    ];

    public function sendResetLink()
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->emailSent = true;
            session()->flash('status', __($status));
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.admin.auth.forgot-password');
    }
}
```

### 6.3 Session Management

#### Livewire Component: `SessionManager.php`
```php
namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionManager extends Component
{
    public $sessions = [];

    public function mount()
    {
        $this->loadSessions();
    }

    public function loadSessions()
    {
        $this->sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'id' => $session->id,
                    'ip' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity),
                    'is_current' => $session->id === session()->getId(),
                ];
            });
    }

    public function logoutOther($sessionId)
    {
        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();

        $this->loadSessions();
        session()->flash('message', 'Session logged out successfully.');
    }

    public function logoutAllOthers()
    {
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', session()->getId())
            ->delete();

        $this->loadSessions();
        session()->flash('message', 'All other sessions logged out.');
    }

    public function render()
    {
        return view('livewire.admin.session-manager');
    }
}
```

---

## Phase 7: Frontend & UX

### 7.1 Loading States for Livewire

#### Global Loading Indicator
```html
<!-- resources/views/layouts/app.blade.php -->
<div x-data x-show="isLoading" x-cloak
     class="fixed top-0 left-0 right-0 z-50 h-1 bg-gray-200 overflow-hidden">
    <div class="h-full bg-blue-500 animate-pulse" 
         x-bind:style="'width: ' + loadingProgress + '%'">
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('loading', {
        isLoading: false,
        loadingProgress: 0,
        
        start() {
            this.isLoading = true;
            this.loadingProgress = 0;
            this.animate();
        },
        
        animate() {
            if (this.isLoading && this.loadingProgress < 90) {
                this.loadingProgress += Math.random() * 10;
                setTimeout(() => this.animate(), 100);
            }
        },
        
        complete() {
            this.loadingProgress = 100;
            setTimeout(() => {
                this.isLoading = false;
                this.loadingProgress = 0;
            }, 200);
        }
    });
});

// Livewire events
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ type }) => {
        if (type === 'callMethod' || type === 'fireEvent') {
            Alpine.store('loading').start();
        }
    });
    
    Livewire.hook('response', () => {
        Alpine.store('loading').complete();
    });
});
</script>
```

### 7.2 Skeleton Loading

#### Component: `SkeletonLoader.php`
```php
namespace App\View\Components;

use Illuminate\View\Component;

class SkeletonLoader extends Component
{
    public $type;
    public $count;

    public function __construct($type = 'card', $count = 1)
    {
        $this->type = $type;
        $this->count = $count;
    }

    public function render()
    {
        return view('components.skeleton-loader');
    }
}
```

#### View
```html
<!-- resources/views/components/skeleton-loader.blade.php -->
@for ($i = 0; $i < $count; $i++)
    @if ($type === 'card')
        <div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-lg p-4">
            <div class="h-48 bg-gray-300 dark:bg-gray-600 rounded mb-4"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
        </div>
    @elseif ($type === 'text')
        <div class="animate-pulse space-y-2">
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-5/6"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-4/6"></div>
        </div>
    @endif
@endfor
```

### 7.3 Infinite Scroll

#### Livewire Component: `InfiniteScroll.php`
```php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

abstract class InfiniteScroll extends Component
{
    use WithPagination;

    public int $perPage = 10;
    public bool $hasMorePages = true;

    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->perPage += 10;
        }
    }

    public function render()
    {
        $items = $this->query()->paginate($this->perPage);
        $this->hasMorePages = $items->hasMorePages();

        return view('livewire.infinite-scroll', [
            'items' => $items,
        ]);
    }

    abstract protected function query();
}
```

#### Blog Implementation
```php
namespace App\Livewire;

class BlogInfiniteScroll extends InfiniteScroll
{
    protected function query()
    {
        return Blog::published()->recent();
    }
}
```

#### View with Intersection Observer
```html
<div x-data="{
    observer: null,
    init() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    $wire.loadMore();
                }
            });
        }, { rootMargin: '100px' });
        
        this.observer.observe(this.$refs.sentinel);
    }
}">
    @foreach ($items as $item)
        <div class="blog-item">
            <!-- Blog content -->
        </div>
    @endforeach
    
    <div x-ref="sentinel" class="h-10">
        @if ($hasMorePages)
            <x-skeleton-loader type="text" />
        @endif
    </div>
</div>
```

### 7.4 Dark Mode Persistence

#### Update ThemeToggle Component
```php
namespace App\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public string $theme = 'system';

    public function mount()
    {
        $this->theme = session('theme', 'system');
    }

    public function toggle()
    {
        $this->theme = match ($this->theme) {
            'light' => 'dark',
            'dark' => 'system',
            default => 'light',
        };

        session(['theme' => $this->theme]);
        $this->dispatch('theme-changed', theme: $this->theme);
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
```

#### JavaScript
```javascript
// Initialize theme from localStorage
if (localStorage.theme === 'dark' || 
    (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

// Listen for theme changes
document.addEventListener('theme-changed', (e) => {
    const theme = e.detail.theme;
    localStorage.theme = theme;
    
    if (theme === 'dark' || 
        (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});
```

### 7.5 Accessibility (ARIA, Keyboard Navigation)

#### Navigation Component with ARIA
```html
<nav role="navigation" aria-label="Main navigation">
    <ul role="menubar" class="flex space-x-4">
        @foreach ($menuItems as $item)
            <li role="none">
                <a role="menuitem"
                   href="{{ $item['url'] }}"
                   aria-current="{{ request()->url() === $item['url'] ? 'page' : 'false' }}"
                   tabindex="0"
                   class="focus:outline-none focus:ring-2 focus:ring-blue-500">
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
```

#### Skip Link
```html
<a href="#main-content" 
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded">
    Skip to main content
</a>
```

#### Focus Management
```javascript
// Trap focus in modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        // Close modal
        Alpine.store('modal').close();
    }
    
    if (e.key === 'Tab') {
        // Trap focus within modal
        const modal = document.querySelector('[x-show="modalOpen"]');
        if (modal && modal.contains(document.activeElement)) {
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
});
```

---

## Phase 8: Email & Communication

### 8.1 Contact Form Email Notifications

#### Mailable: `ContactFormSubmitted.php`
```php
namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact) {}

    public function build()
    {
        return $this->subject('New Contact Form Submission')
            ->view('emails.contact-submitted')
            ->with([
                'name' => $this->contact->name,
                'email' => $this->contact->email,
                'subject' => $this->contact->subject,
                'message' => $this->contact->message,
            ]);
    }
}
```

#### Job: `SendContactEmail.php`
```php
namespace App\Jobs;

use App\Models\Contact;
use App\Mail\ContactFormSubmitted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendContactEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public Contact $contact) {}

    public function handle()
    {
        Mail::to(config('mail.admin_email'))
            ->send(new ContactFormSubmitted($this->contact));
    }
}
```

### 8.2 Newsletter Subscription

#### Model: `NewsletterSubscriber.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email', 'name', 'subscribed_at', 
        'unsubscribed_at', 'unsubscribe_token', 'status'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public static function subscribe(string $email, ?string $name = null): self
    {
        return static::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'status' => 'active',
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'unsubscribe_token' => Str::random(64),
            ]
        );
    }

    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }
}
```

#### Livewire Component: `NewsletterSubscribe.php`
```php
namespace App\Livewire;

use Livewire\Component;
use App\Models\NewsletterSubscriber;

class NewsletterSubscribe extends Component
{
    public $email = '';
    public $name = '';
    public $subscribed = false;

    protected $rules = [
        'email' => 'required|email|unique:newsletter_subscribers,email',
        'name' => 'nullable|string|max:255',
    ];

    public function subscribe()
    {
        $this->validate();

        NewsletterSubscriber::subscribe($this->email, $this->name);

        $this->subscribed = true;
        $this->reset(['email', 'name']);
    }

    public function render()
    {
        return view('livewire.newsletter-subscribe');
    }
}
```

### 8.3 Email Templates

#### Newsletter Email Template
```html
<!-- resources/views/emails/newsletter.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $subject }}</h1>
        </div>
        <div class="content">
            {!! $content !!}
        </div>
        <div class="footer">
            <p>You received this email because you subscribed to our newsletter.</p>
            <p>
                <a href="{{ route('newsletter.unsubscribe', $token) }}">Unsubscribe</a>
            </p>
        </div>
    </div>
</body>
</html>
```

---

## Phase 9: Social & Integration

### 9.1 Social Media Sharing Buttons

#### Component: `ShareButtons.php`
```php
namespace App\View\Components;

use Illuminate\View\Component;

class ShareButtons extends Component
{
    public $url;
    public $title;
    public $description;

    public function __construct($url, $title, $description = '')
    {
        $this->url = urlencode($url);
        $this->title = urlencode($title);
        $this->description = urlencode($description);
    }

    public function render()
    {
        return view('components.share-buttons');
    }

    public function getShareLinks(): array
    {
        return [
            'twitter' => "https://twitter.com/intent/tweet?url={$this->url}&text={$this->title}",
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$this->url}",
            'linkedin' => "https://www.linkedin.com/shareArticle?mini=true&url={$this->url}&title={$this->title}",
            'whatsapp' => "https://wa.me/?text={$this->title}%20{$this->url}",
        ];
    }
}
```

### 9.2 Open Graph & Twitter Cards

#### Enhanced SEO Meta Component
```php
// Additional Open Graph fields
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="{{ app()->getLocale() }}">

@if($ogType === 'article')
    <meta property="article:published_time" content="{{ $publishedAt }}">
    <meta property="article:modified_time" content="{{ $updatedAt }}">
    <meta property="article:author" content="{{ $author }}">
    @foreach ($tags as $tag)
        <meta property="article:tag" content="{{ $tag }}">
    @endforeach
@endif

<!-- Twitter Cards -->
<meta name="twitter:card" content="{{ $twitterCard ?? 'summary_large_image' }}">
<meta name="twitter:site" content="@{{ config('services.twitter.site') }}">
<meta name="twitter:creator" content="@{{ $twitterCreator ?? config('services.twitter.site') }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
```

### 9.3 Social Login (Google, GitHub)

#### Install Socialite
```bash
composer require laravel/socialite
```

#### Configure Services
```php
// config/services.php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],

'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => env('GITHUB_REDIRECT_URI'),
],
```

#### Controller: `SocialLoginController.php`
```php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SocialAccount;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialLoginController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;
        } else {
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName(),
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('admin.dashboard'));
    }
}
```

#### Routes
```php
Route::get('/login/{provider}', [SocialLoginController::class, 'redirect'])
    ->name('social.login');
Route::get('/login/{provider}/callback', [SocialLoginController::class, 'callback'])
    ->name('social.callback');
```

---

## Implementation Timeline

### Phase 1: Database & Infrastructure
- [ ] Create all migrations
- [ ] Install and configure Redis
- [ ] Set up queue workers
- [ ] Create base jobs

### Phase 2: Blog & Content Features
- [ ] Implement comments system
- [ ] Add tech stack filtering
- [ ] Create admin comment management

### Phase 3: Search & Analytics
- [ ] Install and configure Scout/Meilisearch
- [ ] Implement search components
- [ ] Add page view tracking
- [ ] Create analytics dashboard

### Phase 4: Image Management
- [ ] Update image optimization service
- [ ] Configure CDN
- [ ] Implement gallery with drag-and-drop

### Phase 5: SEO & Performance
- [ ] Create sitemap generator
- [ ] Implement robots.txt management
- [ ] Add structured data
- [ ] Configure cache tags

### Phase 6: Security & Authentication
- [ ] Implement 2FA
- [ ] Add password reset
- [ ] Create session management

### Phase 7: Frontend & UX
- [ ] Add loading states
- [ ] Implement skeleton loading
- [ ] Add infinite scroll
- [ ] Persist dark mode
- [ ] Implement accessibility features

### Phase 8: Email & Communication
- [ ] Set up email notifications
- [ ] Implement newsletter
- [ ] Create email templates

### Phase 9: Social & Integration
- [ ] Add social sharing
- [ ] Enhance Open Graph
- [ ] Implement social login

---

## Dependencies to Install

```bash
# Phase 1
composer require predis/predis

# Phase 3
composer require laravel/scout
composer require meilisearch/meilisearch-php
composer require stevebauman/location

# Phase 5
composer require spatie/sitemap

# Phase 6
composer require laravel/fortify
composer require pragmarx/google2fa
composer require bacon/bacon-qr-code

# Phase 9
composer require laravel/socialite
```

---

## Environment Variables to Add

```env
# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Meilisearch
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=masterKey
SCOUT_DRIVER=meilisearch

# AWS S3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_URL=

# Cloudflare R2
CLOUDFLARE_R2_ACCESS_KEY_ID=
CLOUDFLARE_R2_SECRET_ACCESS_KEY=
CLOUDFLARE_R2_REGION=auto
CLOUDFLARE_R2_BUCKET=
CLOUDFLARE_R2_URL=
CLOUDFLARE_R2_ENDPOINT=

# Social Login
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI=

# Email
MAIL_ADMIN_EMAIL=admin@example.com
```

---

## Testing Strategy

Each phase should include:
1. **Unit Tests** - Test individual methods and classes
2. **Feature Tests** - Test HTTP endpoints and Livewire components
3. **E2E Tests** - Test user flows in browser

---

## Notes

- All features should maintain backward compatibility
- Each phase can be deployed independently
- Features should be feature-flagged for gradual rollout
- Performance should be monitored after each deployment