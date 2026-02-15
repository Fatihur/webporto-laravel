<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['graphic-design', 'software-dev', 'data-analysis', 'networking']),
            'thumbnail' => null,
            'gallery' => [],
            'project_date' => fake()->date(),
            'tags' => fake()->words(3),
            'tech_stack' => fake()->words(5),
            'stats' => [],
            'is_featured' => fake()->boolean(20),
            'meta_description' => fake()->sentence(),
            'meta_keywords' => implode(', ', fake()->words(5)),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function softwareDev(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'software-dev',
        ]);
    }
}
