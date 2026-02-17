<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\ProjectFilter;
use App\Livewire\ProjectDetailPage;
use App\Livewire\BlogPage;
use App\Livewire\ContactPage;
use App\Livewire\BlogDetailPage;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Projects\Index as ProjectsIndex;
use App\Livewire\Admin\Projects\Form as ProjectsForm;
use App\Livewire\Admin\Blogs\Index as BlogsIndex;
use App\Livewire\Admin\Blogs\Form as BlogsForm;
use App\Livewire\Admin\Contacts\Index as ContactsIndex;
use App\Livewire\Admin\Experiences\Index as ExperiencesIndex;
use App\Livewire\Admin\Experiences\Form as ExperiencesForm;
use App\Livewire\Admin\Comments\Index as CommentsIndex;
use App\Livewire\SearchComponent;
use App\Livewire\Admin\Newsletter\Index as NewsletterIndex;
use App\Livewire\Admin\Newsletter\Send as NewsletterSend;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', HomePage::class)->name('home');

// SEO Routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Social Login Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login/{provider}', [SocialLoginController::class, 'redirect'])->name('social.login');
    Route::get('/login/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/social/{provider}/unlink', [SocialLoginController::class, 'unlink'])->name('social.unlink');
});

// Projects
Route::redirect('/projects', '/');
Route::get('/projects/{category}', ProjectFilter::class)->name('projects.category');
Route::get('/project/{slug}', ProjectDetailPage::class)->name('projects.show');

// Blog
Route::get('/blog', BlogPage::class)->name('blog.index');
Route::get('/blog/{slug}', BlogDetailPage::class)->name('blog.show');

// Contact
Route::get('/contact', ContactPage::class)->name('contact.index');

// Newsletter
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Admin Auth (Guest only)
Route::middleware(['guest'])->group(function () {
    Route::get('/admin/login', Login::class)->name('admin.login');
});

// Admin Logout
Route::post('/admin/logout', function () {
    Auth::logout();
    return redirect()->route('admin.login');
})->name('admin.logout')->middleware('auth');

// Admin Dashboard (Protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', Dashboard::class)->name('admin.dashboard');

    // Projects
    Route::get('/projects', ProjectsIndex::class)->name('admin.projects.index');
    Route::get('/projects/create', ProjectsForm::class)->name('admin.projects.create');
    Route::get('/projects/{id}/edit', ProjectsForm::class)->name('admin.projects.edit');

    // Blogs
    Route::get('/blogs', BlogsIndex::class)->name('admin.blogs.index');
    Route::get('/blogs/create', BlogsForm::class)->name('admin.blogs.create');
    Route::get('/blogs/{id}/edit', BlogsForm::class)->name('admin.blogs.edit');

    // Contacts
    Route::get('/contacts', ContactsIndex::class)->name('admin.contacts.index');

    // Experiences
    Route::get('/experiences', ExperiencesIndex::class)->name('admin.experiences.index');
    Route::get('/experiences/create', ExperiencesForm::class)->name('admin.experiences.create');
    Route::get('/experiences/{id}/edit', ExperiencesForm::class)->name('admin.experiences.edit');

    // Comments
    Route::get('/comments', CommentsIndex::class)->name('admin.comments.index');

    // Newsletter
    Route::get('/newsletter', NewsletterIndex::class)->name('admin.newsletter.index');
    Route::get('/newsletter/send', NewsletterSend::class)->name('admin.newsletter.send');

    // Search
    Route::get('/search', SearchComponent::class)->name('search');

    // Security
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/2fa', \App\Livewire\Admin\Security\TwoFactorAuth::class)->name('2fa');
        Route::get('/sessions', \App\Livewire\Admin\Security\SessionManager::class)->name('sessions');
    });

});
