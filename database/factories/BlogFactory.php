<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(5, true),
            'category' => fake()->randomElement(['design', 'technology', 'tutorial', 'insights']),
            'image' => null,
            'author' => fake()->name(),
            'read_time' => fake()->numberBetween(3, 20),
            'published_at' => fake()->optional(70)->dateTimeBetween('-1 year', 'now'),
            'is_published' => fake()->boolean(70),
            'meta_description' => fake()->sentence(),
            'meta_keywords' => implode(', ', fake()->words(5)),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(1, 365)),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
