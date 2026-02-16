<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMessageFactory extends Factory
{
    protected $model = ChatMessage::class;

    public function definition(): array
    {
        return [
            'chat_session_id' => ChatSession::factory(),
            'role' => $this->faker->randomElement(['user', 'assistant']),
            'content' => $this->faker->paragraphs(2, true),
            'metadata' => null,
            'sent_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'created_at' => fn(array $attributes) => $attributes['sent_at'],
            'updated_at' => fn(array $attributes) => $attributes['sent_at'],
        ];
    }

    public function user(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'user',
            'content' => $this->faker->randomElement([
                'Tell me about your projects',
                'What technologies do you use?',
                'How can I contact you?',
                'Show me your latest blog posts',
                'What is your experience?',
                'Can you tell me more about ' . $this->faker->word() . '?',
            ]),
        ]);
    }

    public function assistant(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'assistant',
            'content' => $this->faker->randomElement([
                "I'd be happy to help you with that! Fatih has worked on various projects including web applications, mobile apps, and data analysis tools.\n\nWould you like to know about a specific project?",
                "Fatih is proficient in several technologies including Laravel, PHP, JavaScript, Python, and React. He also has experience with data analysis using Python and R.\n\nIs there a specific technology you're interested in?",
                "You can contact Fatih through the contact form on this website, or reach out via email at contact@fatih.com.\n\nHe typically responds within 24-48 hours.",
                "Here are some of Fatih's recent blog posts:\n\n• Getting Started with Laravel Livewire\n• Optimizing Database Queries\n• Building Scalable APIs\n\nWould you like me to summarize any of these?",
            ]),
        ]);
    }

    public function withMetadata(): static
    {
        return $this->state(fn(array $attributes) => [
            'metadata' => [
                'token_count' => $this->faker->numberBetween(50, 500),
                'latency_ms' => $this->faker->numberBetween(200, 3000),
                'model' => 'gemini-2.0-flash',
            ],
        ]);
    }
}
