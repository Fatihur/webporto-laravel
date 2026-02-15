<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-10 years', '-1 year');
        $isCurrent = fake()->boolean(30);

        return [
            'company' => fake()->company(),
            'role' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'end_date' => $isCurrent ? null : fake()->dateTimeBetween($startDate, 'now'),
            'is_current' => $isCurrent,
            'order' => fake()->numberBetween(0, 100),
        ];
    }

    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => true,
            'end_date' => null,
        ]);
    }

    public function past(): static
    {
        $startDate = fake()->dateTimeBetween('-10 years', '-2 years');

        return $this->state(fn (array $attributes) => [
            'is_current' => false,
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '-1 year'),
        ]);
    }
}
