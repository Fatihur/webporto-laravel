<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiBlogAutomation>
 */
class AiBlogAutomationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'topic_prompt' => $this->faker->sentence(),
            'content_prompt' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['design', 'technology', 'tutorial', 'insights']),
            'image_url' => null,
            'frequency' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'custom']),
            'scheduled_at' => '09:00:00',
            'is_active' => true,
            'max_articles_per_day' => 1,
            'auto_publish' => true,
            'last_run_at' => null,
            'next_run_at' => null,
        ];
    }

    /**
     * Indicate that the automation is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the automation is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
