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
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', HomePage::class)->name('home');

// Projects
Route::redirect('/projects', '/');
Route::get('/projects/{category}', ProjectFilter::class)->name('projects.category');
Route::get('/project/{slug}', ProjectDetailPage::class)->name('projects.show');

// Blog
Route::get('/blog', BlogPage::class)->name('blog.index');
Route::get('/blog/{slug}', BlogDetailPage::class)->name('blog.show');

// Contact
Route::get('/contact', ContactPage::class)->name('contact.index');

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
});
