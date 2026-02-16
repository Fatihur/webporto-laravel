<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected string $provider;

    public function __construct()
    {
        // Check if OpenRouter is configured, otherwise fallback to Gemini
        $this->provider = !empty(config('services.openrouter.api_key')) ? 'openrouter' : 'gemini';

        if ($this->provider === 'openrouter') {
            $this->apiKey = config('services.openrouter.api_key');
            $this->model = config('services.openrouter.model', 'openrouter/free');
            $this->baseUrl = config('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
        } else {
            $this->apiKey = config('services.gemini.api_key');
            $this->model = config('services.gemini.model', 'gemini-2.0-flash');
            $this->baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        }
    }

    /**
     * Check if the service is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get current provider name
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Generate chat response
     *
     * @param array $messages Array of messages with 'role' and 'content' keys
     * @param string|null $systemPrompt System instructions
     * @return string|null
     */
    public function chat(array $messages, ?string $systemPrompt = null): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('AI API key not configured');
            return "I apologize, but the AI service is not properly configured. Please contact the administrator.";
        }

        try {
            if ($this->provider === 'openrouter') {
                return $this->chatWithOpenRouter($messages, $systemPrompt);
            } else {
                return $this->chatWithGemini($messages, $systemPrompt);
            }
        } catch (\Exception $e) {
            Log::error('AI service error: ' . $e->getMessage());

            $message = $e->getMessage();

            if (str_contains($message, 'quota') || str_contains($message, '429') || str_contains($message, 'exceeded')) {
                return "I apologize, but the AI service has reached its limit. Please try again later, or contact Fatih directly through the contact form.";
            }

            if (str_contains($message, 'invalid') || str_contains($message, '401') || str_contains($message, '403')) {
                return "I'm having trouble authenticating. Please contact the administrator.";
            }

            return "I'm having trouble connecting right now. Please try again later, or reach out through the contact form if you need immediate assistance.";
        }
    }

    /**
     * Chat using OpenRouter API (OpenAI compatible)
     */
    protected function chatWithOpenRouter(array $messages, ?string $systemPrompt): ?string
    {
        $endpoint = "{$this->baseUrl}/chat/completions";

        // Format messages for OpenAI-compatible API
        $formattedMessages = [];

        if ($systemPrompt) {
            $formattedMessages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];
        }

        foreach ($messages as $message) {
            $formattedMessages[] = [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }

        // Debug: log system prompt
        Log::debug('OpenRouter API Request', [
            'model' => $this->model,
            'system_prompt_length' => strlen($systemPrompt ?? ''),
            'messages_count' => count($formattedMessages),
        ]);

        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('services.openrouter.site_url', config('app.url')),
                'X-Title' => config('services.openrouter.site_name', config('app.name')),
            ])
            ->post($endpoint, [
                'model' => $this->model,
                'messages' => $formattedMessages,
                'temperature' => 0.7,
                'max_tokens' => 2048,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        }

        $errorBody = $response->json() ?? [];
        $errorMessage = $errorBody['error']['message'] ?? $response->body();

        Log::error('OpenRouter API error', [
            'status' => $response->status(),
            'error' => $errorMessage,
        ]);

        throw new \Exception("OpenRouter error: {$errorMessage}");
    }

    /**
     * Chat using Gemini API
     */
    protected function chatWithGemini(array $messages, ?string $systemPrompt): ?string
    {
        $endpoint = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $contents = $this->formatMessagesForGemini($messages);

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
        ];

        if ($systemPrompt && $this->supportsSystemInstruction()) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ];
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($endpoint, $payload);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        $errorBody = $response->json() ?? [];
        $errorMessage = $errorBody['error']['message'] ?? $response->body();

        Log::error('Gemini API error', [
            'status' => $response->status(),
            'error' => $errorMessage,
        ]);

        throw new \Exception("Gemini error: {$errorMessage}");
    }

    /**
     * Generate embeddings for text
     *
     * @param string $text
     * @return array|null
     */
    public function embed(string $text): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('AI API key not configured');
            return null;
        }

        // OpenRouter doesn't support embeddings directly, use Gemini for embeddings
        if ($this->provider === 'openrouter') {
            return $this->embedWithGemini($text);
        }

        return $this->embedWithGemini($text);
    }

    /**
     * Generate embeddings using Gemini
     */
    protected function embedWithGemini(string $text): ?array
    {
        $geminiApiKey = config('services.gemini.api_key');

        if (!$geminiApiKey) {
            return null;
        }

        try {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent?key={$geminiApiKey}";

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'model' => 'models/embedding-001',
                    'content' => [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['embedding']['values'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Gemini embed error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format messages for Gemini API
     */
    protected function formatMessagesForGemini(array $messages): array
    {
        $contents = [];

        foreach ($messages as $message) {
            $role = $message['role'] === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $message['content']]
                ]
            ];
        }

        return $contents;
    }

    /**
     * Check if the model supports system instruction
     */
    protected function supportsSystemInstruction(): bool
    {
        return str_contains($this->model, 'gemini-1.5') || str_contains($this->model, 'gemini-2.0');
    }

    /**
     * Build system prompt with portfolio context
     *
     * @param array $context Additional context data
     * @return string
     */
    public function buildSystemPrompt(array $context = []): string
    {
        $basePrompt = <<<PROMPT
You are an AI assistant for Fatihurroyyan's portfolio website. You help visitors learn about Fatih's background, skills, projects, and experience.

ABOUT FATIH:
- Name: Fatihurroyyan (also known as Fatih)
- Education: Informatics graduate from Universitas Teknologi Sumbawa
- Passion: Tech enthusiast sharing software development projects
- Focus: Web applications and modern technology explorations

YOUR ROLE:
1. Answer questions about Fatih's portfolio, projects, skills, and experience
2. Help visitors navigate the website
3. Recommend relevant projects or blog posts based on interests
4. Be friendly, professional, and informative
5. If you don't know something specific, direct them to the contact page

WEBSITE STRUCTURE:
- Home: Landing page with overview
- Projects: Showcase of software development projects
- Blog: Tech articles and tutorials
- Contact: Form to reach out to Fatih

IMPORTANT INSTRUCTIONS:
- Keep responses concise but informative
- Use markdown formatting when helpful
- If asked about something not in your knowledge, suggest checking the relevant page or using the contact form
- Be helpful and encouraging to visitors
- CRITICAL: If the AVAILABLE PROJECTS, RECENT BLOG POSTS, or EXPERIENCE sections below are empty or not provided, you MUST say "I don't have detailed information about that in my current database. Please check the [Projects/Blog/Experience] page directly or use the contact form to ask Fatih."
- NEVER make up or hallucinate project names, technologies, or details that are not explicitly listed below.
- Only mention projects, blogs, or experiences that are listed in the context below.
PROMPT;

        if (!empty($context['projects'])) {
            $basePrompt .= "\n\nAVAILABLE PROJECTS:\n" . $context['projects'];
        }

        if (!empty($context['blogs'])) {
            $basePrompt .= "\n\nRECENT BLOG POSTS:\n" . $context['blogs'];
        }

        if (!empty($context['experiences'])) {
            $basePrompt .= "\n\nEXPERIENCE:\n" . $context['experiences'];
        }

        // Add explicit note about data availability
        $availableData = [];
        if (!empty($context['projects'])) $availableData[] = 'projects';
        if (!empty($context['blogs'])) $availableData[] = 'blogs';
        if (!empty($context['experiences'])) $availableData[] = 'experiences';

        if (empty($availableData)) {
            $basePrompt .= "\n\nDATA AVAILABILITY: No specific projects, blogs, or experiences are currently loaded in my database. If asked about these, you MUST direct the visitor to check the relevant pages on the website or use the contact form.";
        } else {
            $basePrompt .= "\n\nDATA AVAILABILITY: I have information about: " . implode(', ', $availableData) . ". Only mention these specific items.";
        }

        return $basePrompt;
    }

    /**
     * Get list of recommended free models from OpenRouter
     */
    public static function getFreeModels(): array
    {
        return [
            'z-ai/glm-4.5-air:free' => 'Z.AI GLM 4.5 Air (Free) ⭐',
            'deepseek/deepseek-r1-0528:free' => 'DeepSeek R1 0528 (Free) ⭐',
            'openai/gpt-oss-120b:free' => 'OpenAI GPT-OSS 120B (Free) ⭐',
            'qwen/qwen3-coder:free' => 'Qwen 3 Coder (Free) ⭐',
            'openrouter/free' => 'OpenRouter Free (Auto)',
            'stepfun/step-3.5-flash:free' => 'Step Fun 3.5 Flash (Free)',
            'nvidia/nemotron-3-nano-30b-a3b:free' => 'NVIDIA Nemotron 3 Nano 30B (Free)',
            'liquid/lfm-2.5-1.2b-instruct:free' => 'Liquid LFM 2.5 1.2B Instruct (Free)',
            'upstage/solar-pro-3:free' => 'Upstage Solar Pro 3 (Free)',
            'arcee-ai/trinity-mini:free' => 'Arcee AI Trinity Mini (Free)',
            'qwen/qwen3-next-80b-a3b-instruct:free' => 'Qwen 3 Next 80B A3B Instruct (Free)',
        ];
    }
}
