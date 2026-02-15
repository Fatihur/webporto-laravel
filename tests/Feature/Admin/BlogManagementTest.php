<?php

namespace Tests\Feature\Admin;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /** @test */
    public function authenticated_user_can_view_blogs_list(): void
    {
        Blog::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->get('/admin/blogs');

        $response->assertStatus(200);
        $response->assertSee('Blog');
    }

    /** @test */
    public function authenticated_user_can_create_blog_post(): void
    {
        $blogData = [
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'excerpt' => 'Test excerpt',
            'content' => '<p>Test content</p>',
            'category' => 'technology',
            'author' => 'Test Author',
            'read_time' => 5,
            'is_published' => true,
            'published_at' => '2024-01-15',
        ];

        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', $blogData);

        $response->assertRedirect('/admin/blogs');
        $this->assertDatabaseHas('blogs', [
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
        ]);
    }

    /** @test */
    public function blog_requires_title(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => '',
                'excerpt' => 'Excerpt',
                'category' => 'technology',
            ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function blog_requires_excerpt(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => 'Test Blog',
                'excerpt' => '',
                'category' => 'technology',
            ]);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function blog_requires_category(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => 'Test Blog',
                'excerpt' => 'Excerpt',
                'category' => '',
            ]);

        $response->assertSessionHasErrors('category');
    }

    /** @test */
    public function blog_slug_must_be_unique(): void
    {
        Blog::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => 'Test Blog',
                'slug' => 'existing-slug',
                'excerpt' => 'Excerpt',
                'category' => 'technology',
            ]);

        $response->assertSessionHasErrors('slug');
    }

    /** @test */
    public function blog_excerpt_cannot_exceed_500_characters(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => 'Test Blog',
                'excerpt' => str_repeat('a', 501),
                'category' => 'technology',
            ]);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function authenticated_user_can_update_blog(): void
    {
        $blog = Blog::factory()->create([
            'title' => 'Old Title',
            'excerpt' => 'Old excerpt',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/admin/blogs/{$blog->id}", [
                'title' => 'Updated Title',
                'slug' => $blog->slug,
                'excerpt' => 'Updated excerpt',
                'content' => $blog->content,
                'category' => $blog->category,
                'read_time' => 5,
            ]);

        $response->assertRedirect('/admin/blogs');
        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'Updated Title',
            'excerpt' => 'Updated excerpt',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_blog(): void
    {
        $blog = Blog::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete("/admin/blogs/{$blog->id}");

        $response->assertRedirect('/admin/blogs');
        $this->assertDatabaseMissing('blogs', [
            'id' => $blog->id,
        ]);
    }

    /** @test */
    public function blog_can_be_published(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => 'Published Post',
                'slug' => 'published-post',
                'excerpt' => 'Excerpt',
                'category' => 'technology',
                'read_time' => 5,
                'is_published' => true,
                'published_at' => '2024-01-15',
            ]);

        $response->assertRedirect('/admin/blogs');
        $this->assertDatabaseHas('blogs', [
            'title' => 'Published Post',
            'is_published' => true,
        ]);
    }

    /** @test */
    public function blog_can_be_draft(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => 'Draft Post',
                'slug' => 'draft-post',
                'excerpt' => 'Excerpt',
                'category' => 'technology',
                'read_time' => 5,
                'is_published' => false,
            ]);

        $response->assertRedirect('/admin/blogs');
        $this->assertDatabaseHas('blogs', [
            'title' => 'Draft Post',
            'is_published' => false,
        ]);
    }

    /** @test */
    public function blog_read_time_must_be_integer(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/blogs', [
                'title' => 'Test Blog',
                'slug' => 'test-blog',
                'excerpt' => 'Excerpt',
                'category' => 'technology',
                'read_time' => 'not-a-number',
            ]);

        $response->assertSessionHasErrors('read_time');
    }
}
