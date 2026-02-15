<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_projects_list(): void
    {
        Project::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->get('/admin/projects');

        $response->assertStatus(200);
        $response->assertSee('Projects');
    }

    /** @test */
    public function projects_list_shows_pagination(): void
    {
        Project::factory()->count(15)->create();

        $response = $this->actingAs($this->user)
            ->get('/admin/projects');

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_create_project(): void
    {
        Storage::fake('public');

        $projectData = [
            'title' => 'Test Project',
            'slug' => 'test-project',
            'description' => 'Test project description',
            'content' => '<p>Test content</p>',
            'category' => 'software-dev',
            'project_date' => '2024-01-15',
            'tags' => ['Laravel', 'Vue.js'],
            'tech_stack' => ['PHP', 'MySQL'],
            'stats' => [['label' => 'Client', 'value' => 'Test Corp']],
            'is_featured' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post('/admin/projects', $projectData);

        $response->assertRedirect('/admin/projects');
        $this->assertDatabaseHas('projects', [
            'title' => 'Test Project',
            'slug' => 'test-project',
        ]);
    }

    /** @test */
    public function project_requires_title(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => '',
                'description' => 'Description',
                'category' => 'software-dev',
            ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function project_requires_description(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Test Project',
                'description' => '',
                'category' => 'software-dev',
            ]);

        $response->assertSessionHasErrors('description');
    }

    /** @test */
    public function project_requires_category(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Test Project',
                'description' => 'Description',
                'category' => '',
            ]);

        $response->assertSessionHasErrors('category');
    }

    /** @test */
    public function project_slug_must_be_unique(): void
    {
        Project::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Test Project',
                'slug' => 'existing-slug',
                'description' => 'Description',
                'category' => 'software-dev',
                'project_date' => '2024-01-15',
            ]);

        $response->assertSessionHasErrors('slug');
    }

    /** @test */
    public function authenticated_user_can_update_project(): void
    {
        $project = Project::factory()->create([
            'title' => 'Old Title',
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/admin/projects/{$project->id}", [
                'title' => 'Updated Title',
                'slug' => $project->slug,
                'description' => 'Updated description',
                'content' => $project->content,
                'category' => $project->category,
                'project_date' => $project->project_date->format('Y-m-d'),
            ]);

        $response->assertRedirect('/admin/projects');
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete("/admin/projects/{$project->id}");

        $response->assertRedirect('/admin/projects');
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    /** @test */
    public function project_can_be_featured(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Featured Project',
                'slug' => 'featured-project',
                'description' => 'Description',
                'category' => 'software-dev',
                'project_date' => '2024-01-15',
                'is_featured' => true,
            ]);

        $response->assertRedirect('/admin/projects');
        $this->assertDatabaseHas('projects', [
            'title' => 'Featured Project',
            'is_featured' => true,
        ]);
    }

    /** @test */
    public function project_can_have_tags(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Tagged Project',
                'slug' => 'tagged-project',
                'description' => 'Description',
                'category' => 'software-dev',
                'project_date' => '2024-01-15',
                'tags' => ['PHP', 'Laravel', 'Vue.js'],
            ]);

        $response->assertRedirect('/admin/projects');
        $project = Project::where('slug', 'tagged-project')->first();
        $this->assertCount(3, $project->tags);
    }

    /** @test */
    public function project_can_have_tech_stack(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Tech Project',
                'slug' => 'tech-project',
                'description' => 'Description',
                'category' => 'software-dev',
                'project_date' => '2024-01-15',
                'tech_stack' => ['PHP', 'MySQL', 'Redis'],
            ]);

        $response->assertRedirect('/admin/projects');
        $project = Project::where('slug', 'tech-project')->first();
        $this->assertCount(3, $project->tech_stack);
    }

    /** @test */
    public function project_can_have_stats(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/projects', [
                'title' => 'Stats Project',
                'slug' => 'stats-project',
                'description' => 'Description',
                'category' => 'software-dev',
                'project_date' => '2024-01-15',
                'stats' => [
                    ['label' => 'Client', 'value' => 'Acme Corp'],
                    ['label' => 'Duration', 'value' => '3 months'],
                ],
            ]);

        $response->assertRedirect('/admin/projects');
        $project = Project::where('slug', 'stats-project')->first();
        $this->assertCount(2, $project->stats);
    }
}
