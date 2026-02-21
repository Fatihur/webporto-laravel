<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Projects\Form;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function authenticated_user_can_view_projects_list(): void
    {
        Project::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/admin/projects');

        $response->assertOk();
        $response->assertSee('Projects');
    }

    #[Test]
    public function projects_list_shows_pagination(): void
    {
        Project::factory()->count(15)->create();

        $this->actingAs($this->user)
            ->get('/admin/projects')
            ->assertOk();
    }

    #[Test]
    public function authenticated_user_can_create_project(): void
    {
        $this->actingAs($this->user);
        Storage::fake('public');

        Livewire::test(Form::class)
            ->set('title', 'Test Project')
            ->set('slug', 'test-project')
            ->set('description', 'Test project description')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'software-dev')
            ->set('project_date', '2024-01-15')
            ->set('tags', ['Laravel', 'Vue.js'])
            ->set('tech_stack', ['PHP', 'MySQL'])
            ->set('stats', [['label' => 'Client', 'value' => 'Test Corp']])
            ->set('is_featured', true)
            ->set('thumbnail', UploadedFile::fake()->image('thumbnail.jpg'))
            ->call('save')
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseHas('projects', [
            'title' => 'Test Project',
            'slug' => 'test-project',
            'category' => 'software-dev',
            'is_featured' => true,
        ]);
    }

    #[Test]
    public function project_requires_title(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', '')
            ->set('slug', 'test-project')
            ->set('description', 'Description')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'software-dev')
            ->set('project_date', '2024-01-15')
            ->call('save')
            ->assertHasErrors(['title']);
    }

    #[Test]
    public function project_requires_description(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', 'Test Project')
            ->set('slug', 'test-project')
            ->set('description', '')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'software-dev')
            ->set('project_date', '2024-01-15')
            ->call('save')
            ->assertHasErrors(['description']);
    }

    #[Test]
    public function project_requires_category(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', 'Test Project')
            ->set('slug', 'test-project')
            ->set('description', 'Description')
            ->set('content', '<p>Test content</p>')
            ->set('category', '')
            ->set('project_date', '2024-01-15')
            ->call('save')
            ->assertHasErrors(['category']);
    }

    #[Test]
    public function project_slug_must_be_unique(): void
    {
        Project::factory()->create(['slug' => 'existing-slug']);
        $this->actingAs($this->user);

        Livewire::test(Form::class)
            ->set('title', 'Test Project')
            ->set('slug', 'existing-slug')
            ->set('description', 'Description')
            ->set('content', '<p>Test content</p>')
            ->set('category', 'software-dev')
            ->set('project_date', '2024-01-15')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    #[Test]
    public function authenticated_user_can_update_project(): void
    {
        $project = Project::factory()->create([
            'title' => 'Old Title',
            'description' => 'Old description',
            'category' => 'software-dev',
            'project_date' => now(),
        ]);
        $this->actingAs($this->user);

        Livewire::test(Form::class, ['id' => $project->id])
            ->set('title', 'Updated Title')
            ->set('slug', $project->slug)
            ->set('description', 'Updated description')
            ->set('content', '<p>Updated content</p>')
            ->set('category', 'software-dev')
            ->set('project_date', '2024-01-15')
            ->call('save')
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);
    }

    #[Test]
    public function authenticated_user_can_delete_project(): void
    {
        $project = Project::factory()->create();
        $this->actingAs($this->user);

        Livewire::test(\App\Livewire\Admin\Projects\Index::class)
            ->call('delete', $project->id);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
