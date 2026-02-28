<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LightboxNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_detail_page_contains_gallery_lightbox_structure(): void
    {
        $project = Project::factory()->create([
            'gallery' => ['projects/image1.jpg', 'projects/image2.jpg', 'projects/image3.jpg'],
        ]);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertStatus(200);
        $response->assertSee('galleryUrls', false);
        $response->assertSee('open-lightbox', false);
        $response->assertSee('index:', false);
    }

    public function test_lightbox_global_structure_exists(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Check lightbox navigation elements exist
        $response->assertSee('prev()', false);
        $response->assertSee('next()', false);
        $response->assertSee('ArrowRight', false);
        $response->assertSee('ArrowLeft', false);
        $response->assertSee('handleSwipe', false);
        $response->assertSee('touchStartX', false);
        $response->assertSee('images.length', false);
        $response->assertSee('currentIndex', false);
    }

    public function test_project_without_gallery_works(): void
    {
        $project = Project::factory()->create([
            'gallery' => [],
        ]);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertStatus(200);
        $response->assertDontSee('Project Gallery');
    }

    public function test_project_gallery_renders_correct_number_of_images(): void
    {
        $gallery = ['projects/1.jpg', 'projects/2.jpg', 'projects/3.jpg', 'projects/4.jpg'];
        $project = Project::factory()->create([
            'gallery' => $gallery,
        ]);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertStatus(200);
        // Should show gallery section
        $response->assertSee('Project Gallery');

        // Check that all images are in the gallery data
        foreach ($gallery as $idx => $image) {
            $num = $idx + 1;
            $response->assertSee("Gallery image {$num}");
        }
    }

    public function test_project_detail_page_shows_case_study_and_stats_sections(): void
    {
        $project = Project::factory()->create([
            'stats' => [
                ['label' => 'Users', 'value' => '+120%'],
            ],
            'case_study_problem' => 'Traffic quality was low.',
            'case_study_process' => 'We improved onboarding and SEO.',
            'case_study_result' => 'Retention and conversion improved.',
            'case_study_metrics' => [
                ['label' => 'Conversion', 'value' => '+35%'],
            ],
        ]);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertStatus(200);
        $response->assertSee('Project Stats');
        $response->assertSee('Interactive Case Study');
        $response->assertSee('Case Study Metrics');
        $response->assertSee('Conversion');
        $response->assertSee('+35%');
    }
}
