<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Blogs\Form;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function authenticated_user_can_view_blogs_list(): void
    {
        Blog::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/admin/blogs');

        $response->assertOk();
        $response->assertSee('Blog Posts');
    }

    #[Test]
    public function authenticated_user_can_create_blog_post(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', 'Test Blog Post')
            ->set('slug', 'test-blog-post')
            ->set('excerpt', 'This is a short excerpt for testing purposes.')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'technology')
            ->set('author', 'Test Author')
            ->set('read_time', 5)
            ->set('is_published', true)
            ->set('published_at', '2024-01-15')
            ->call('save')
            ->assertRedirect(route('admin.blogs.index'));

        $this->assertDatabaseHas('blogs', [
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'category' => 'technology',
            'is_published' => true,
        ]);
    }

    #[Test]
    public function blog_requires_title(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', '')
            ->set('slug', 'test-blog')
            ->set('excerpt', 'This is a short excerpt for testing purposes.')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'technology')
            ->set('read_time', 5)
            ->call('save')
            ->assertHasErrors(['title']);
    }

    #[Test]
    public function blog_requires_excerpt(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', 'Test Blog')
            ->set('slug', 'test-blog')
            ->set('excerpt', '')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'technology')
            ->set('read_time', 5)
            ->call('save')
            ->assertHasErrors(['excerpt']);
    }

    #[Test]
    public function blog_requires_category(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', 'Test Blog')
            ->set('slug', 'test-blog')
            ->set('excerpt', 'This is a short excerpt for testing purposes.')
            ->set('content', '<p>Test content</p>')
            ->set('category', '')
            ->set('read_time', 5)
            ->call('save')
            ->assertHasErrors(['category']);
    }

    #[Test]
    public function blog_slug_must_be_unique(): void
    {
        Blog::factory()->create(['slug' => 'existing-slug']);
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', 'Test Blog')
            ->set('slug', 'existing-slug')
            ->set('excerpt', 'This is a short excerpt for testing purposes.')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'technology')
            ->set('read_time', 5)
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    #[Test]
    public function authenticated_user_can_update_blog(): void
    {
        $blog = Blog::factory()->create([
            'title' => 'Old Title',
            'excerpt' => 'Old excerpt with enough characters',
            'category' => 'technology',
        ]);
        $this->actingAs($this->user);

        Livewire::test(Form::class, ['id' => $blog->id])
            ->set('title', 'Updated Title')
            ->set('slug', $blog->slug)
            ->set('excerpt', 'Updated excerpt with enough characters')
            ->set('content', '<p>Updated content</p>')
            ->set('category', 'technology')
            ->set('read_time', 7)
            ->call('save')
            ->assertRedirect(route('admin.blogs.index'));

        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'Updated Title',
            'read_time' => 7,
        ]);
    }

    #[Test]
    public function authenticated_user_can_delete_blog(): void
    {
        $blog = Blog::factory()->create();
        $this->actingAs($this->user);

        Livewire::test(\App\Livewire\Admin\Blogs\Index::class)
            ->call('delete', $blog->id);

        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }
}
