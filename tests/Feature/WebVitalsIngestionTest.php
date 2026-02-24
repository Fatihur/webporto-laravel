<?php

namespace Tests\Feature;

use App\Models\WebVitalMetric;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebVitalsIngestionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_web_vitals_payload_and_resolves_budget_rating(): void
    {
        $response = $this->postJson(route('web-vitals.store'), [
            'path' => '/blog/test-article',
            'metric' => 'LCP',
            'value' => 2300,
            'connection_type' => '4g',
        ]);

        $response->assertStatus(202)
            ->assertJson(['recorded' => true]);

        $this->assertDatabaseHas('web_vital_metrics', [
            'path' => '/blog/test-article',
            'metric' => 'LCP',
            'rating' => 'good',
            'page_group' => 'blog_detail',
            'connection_type' => '4g',
        ]);
    }

    #[Test]
    public function it_rejects_invalid_metric_payload(): void
    {
        $response = $this->postJson(route('web-vitals.store'), [
            'path' => '/',
            'metric' => 'TTFB',
            'value' => 120,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['metric']);
    }

    #[Test]
    public function it_hashes_user_agent_for_privacy(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'PerformanceBot/1.0',
        ])->postJson(route('web-vitals.store'), [
            'path' => '/contact',
            'metric' => 'INP',
            'value' => 350,
        ]);

        $response->assertStatus(202);

        $this->assertNotNull(WebVitalMetric::query()->first()?->user_agent_hash);
    }
}
