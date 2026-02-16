<?php

namespace Database\Factories;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChatSessionFactory extends Factory
{
    protected $model = ChatSession::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'session_id' => Str::uuid(),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'last_activity_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'is_active' => $this->faker->boolean(80),
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'updated_at' => fn(array $attributes) => $attributes['created_at'],
        ];
    }

    public function withUser(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
            'last_activity_at' => $this->faker->dateTimeBetween('-90 days', '-31 days'),
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn(array $attributes) => [
            'last_activity_at' => now(),
            'created_at' => now(),
        ]);
    }
}
