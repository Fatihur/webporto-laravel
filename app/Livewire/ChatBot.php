<?php

namespace App\Livewire;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Services\Ai\GeminiService;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Experience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ChatBot extends Component
{
    public bool $isOpen = false;
    public string $message = '';
    public array $messages = [];
    public ?int $sessionId = null;
    public string $sessionToken = '';
    public bool $isTyping = false;
    public int $unreadCount = 0;
    public ?string $error = null;

    // Typing animation properties
    public ?string $typingContent = null;
    public ?int $typingMessageId = null;
    public int $typingSpeed = 15; // milliseconds per character

    protected $listeners = [
        'chatOpened' => 'markAsRead',
        'typingComplete' => 'handleTypingComplete',
    ];

    protected $rules = [
        'message' => 'required|string|max:2000',
    ];

    public function mount(): void
    {
        $this->sessionToken = session('chat_session_token', Str::uuid()->toString());
        session(['chat_session_token' => $this->sessionToken]);

        $this->loadSession();
        $this->loadMessages();
    }

    /**
     * Load or create chat session
     */
    protected function loadSession(): void
    {
        $session = ChatSession::where('session_id', $this->sessionToken)->first();

        if (!$session) {
            $session = ChatSession::create([
                'user_id' => Auth::id(),
                'session_id' => $this->sessionToken,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'last_activity_at' => now(),
                'is_active' => true,
            ]);
        }

        $this->sessionId = $session->id;
        $session->touchLastActivity();

        // Count unread messages (assistant messages not seen yet)
        $this->unreadCount = $session->messages()
            ->where('role', 'assistant')
            ->where('created_at', '>', $session->last_activity_at ?? now()->subDay())
            ->count();
    }

    /**
     * Load messages for display
     */
    protected function loadMessages(): void
    {
        if (!$this->sessionId) {
            return;
        }

        $messages = ChatMessage::where('chat_session_id', $this->sessionId)
            ->orderBy('sent_at')
            ->get()
            ->map(fn($msg) => [
                'id' => $msg->id,
                'role' => $msg->role,
                'content' => $msg->content,
                'sent_at' => $msg->sent_at->diffForHumans(),
            ])
            ->toArray();

        // Add welcome message if no messages yet
        if (empty($messages)) {
            $welcomeMessage = $this->getWelcomeMessage();
            $messages[] = [
                'id' => 'welcome',
                'role' => 'assistant',
                'content' => $welcomeMessage,
                'sent_at' => now()->diffForHumans(),
            ];
        }

        $this->messages = $messages;
    }

    /**
     * Get welcome message
     */
    protected function getWelcomeMessage(): string
    {
        $hour = now()->hour;
        $greeting = match(true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        return "👋 {$greeting}! I'm Fatih's AI assistant.\n\nI can help you with:\n• Learning about Fatih's projects and skills\n• Finding relevant blog posts\n• Understanding his experience\n• Navigating this website\n\nWhat would you like to know?";
    }

    /**
     * Toggle chat widget
     */
    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;

        if ($this->isOpen) {
            $this->unreadCount = 0;
            $this->dispatch('chatOpened');
        } else {
            $this->dispatch('chatClosed');
        }

        $this->dispatch('chatToggled', [
            'isOpen' => $this->isOpen,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(): void
    {
        if ($this->sessionId) {
            ChatSession::where('id', $this->sessionId)->update([
                'last_activity_at' => now(),
            ]);
        }
    }

    /**
     * Send message
     */
    public function sendMessage(GeminiService $gemini): void
    {
        $this->validate();
        $this->error = null;

        try {
            // Save user message
            $userMessage = ChatMessage::create([
                'chat_session_id' => $this->sessionId,
                'role' => 'user',
                'content' => $this->message,
                'sent_at' => now(),
            ]);

            // Add to local messages
            $userContent = $this->message;
            $this->messages[] = [
                'id' => $userMessage->id,
                'role' => 'user',
                'content' => $userContent,
                'sent_at' => 'just now',
            ];

            // Dispatch message sent event
            $this->dispatch('messageSent', [
                'messageId' => $userMessage->id,
                'content' => $userContent,
                'timestamp' => now()->toIso8601String(),
            ]);

            $this->message = '';
            $this->isTyping = true;

            // Dispatch typing started event
            $this->dispatch('typingStarted', [
                'timestamp' => now()->toIso8601String(),
            ]);

            // Get AI response
            $response = $this->getAiResponse($gemini, $userContent);

            // Save AI response
            $assistantMessage = ChatMessage::create([
                'chat_session_id' => $this->sessionId,
                'role' => 'assistant',
                'content' => $response,
                'sent_at' => now(),
            ]);

            $this->isTyping = false;

            // Add message with empty content for typing animation
            $messageId = $assistantMessage->id;
            $this->typingMessageId = $messageId;
            $this->typingContent = $response;

            $this->messages[] = [
                'id' => $messageId,
                'role' => 'assistant',
                'content' => '', // Start empty for typing effect
                'fullContent' => $response,
                'sent_at' => 'just now',
                'isTyping' => true,
            ];

            // Dispatch typing progress event with content
            $this->dispatch('typingProgress', [
                'messageId' => $messageId,
                'content' => $response,
                'speed' => $this->typingSpeed,
            ]);

            // Update session activity
            ChatSession::where('id', $this->sessionId)->update([
                'last_activity_at' => now(),
            ]);

        } catch (\Exception $e) {
            $this->error = 'Sorry, I encountered an error. Please try again.';
            $this->dispatch('errorOccurred', [
                'message' => $this->error,
                'timestamp' => now()->toIso8601String(),
            ]);
            \Illuminate\Support\Facades\Log::error('ChatBot error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->isTyping = false;
        }
    }

    /**
     * Handle typing animation complete
     */
    public function handleTypingComplete(int $messageId): void
    {
        // Find and update the message
        foreach ($this->messages as $key => $msg) {
            if ($msg['id'] === $messageId) {
                $this->messages[$key]['isTyping'] = false;
                $this->messages[$key]['content'] = $msg['fullContent'] ?? $this->typingContent;
                break;
            }
        }

        $this->typingMessageId = null;
        $this->typingContent = null;

        // Dispatch message received event
        $this->dispatch('messageReceived', [
            'messageId' => $messageId,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Skip typing animation and show full message
     */
    public function skipTyping(): void
    {
        if ($this->typingMessageId && $this->typingContent) {
            $this->dispatch('typingSkipped', [
                'messageId' => $this->typingMessageId,
                'content' => $this->typingContent,
            ]);

            $this->handleTypingComplete($this->typingMessageId);
        }
    }

    /**
     * Get AI response from Gemini
     */
    protected function getAiResponse(GeminiService $gemini, string $userMessage): string
    {
        // Build context from database
        $context = $this->buildContext();

        // Get recent conversation history (last 10 messages for context)
        $recentMessages = ChatMessage::where('chat_session_id', $this->sessionId)
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->getFormattedContent(),
            ])
            ->toArray();

        // Add current message
        $recentMessages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        // Build system prompt
        $systemPrompt = $gemini->buildSystemPrompt($context);

        // Get response
        $response = $gemini->chat($recentMessages, $systemPrompt);

        if (!$response) {
            return "I apologize, but I'm having trouble connecting right now. Please try again in a moment, or feel free to reach out through the contact form if you need immediate assistance.";
        }

        return $response;
    }

    /**
     * Build context from database for AI
     */
    protected function buildContext(): array
    {
        $context = [];

        // Get featured projects first, if none, get recent projects
        $projects = Project::where('is_featured', true)
            ->orderBy('project_date', 'desc')
            ->limit(5)
            ->get();

        // If no featured projects, get recent projects
        if ($projects->isEmpty()) {
            $projects = Project::orderBy('project_date', 'desc')
                ->limit(5)
                ->get();
        }

        if ($projects->isNotEmpty()) {
            $context['projects'] = $projects->map(function ($p) {
                $techStack = is_array($p->tech_stack) ? implode(', ', $p->tech_stack) : $p->tech_stack;
                $featured = $p->is_featured ? ' ⭐' : '';
                return "- {$p->title}{$featured}: {$p->description} (Tech: {$techStack}, Category: {$p->category})";
            })->join("\n");
        }

        // Get recent blog posts
        $blogs = Blog::where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        if ($blogs->isNotEmpty()) {
            $context['blogs'] = $blogs->map(function ($b) {
                $excerpt = strip_tags($b->excerpt);
                $excerpt = strlen($excerpt) > 150 ? substr($excerpt, 0, 150) . '...' : $excerpt;
                return "- {$b->title}: {$excerpt} (Category: {$b->category})";
            })->join("\n");
        }

        // Get experiences
        $experiences = Experience::orderBy('start_date', 'desc')
            ->limit(5)
            ->get();

        if ($experiences->isNotEmpty()) {
            $context['experiences'] = $experiences->map(function ($e) {
                $endDate = $e->end_date ? $e->end_date->format('Y') : 'Present';
                return "- {$e->title} at {$e->company} ({$e->start_date->format('Y')} - {$endDate}): {$e->description}";
            })->join("\n");
        }

        return $context;
    }

    /**
     * Clear chat history
     */
    public function clearChat(): void
    {
        if ($this->sessionId) {
            ChatMessage::where('chat_session_id', $this->sessionId)->delete();
        }

        $this->messages = [
            [
                'id' => 'welcome',
                'role' => 'assistant',
                'content' => $this->getWelcomeMessage(),
                'sent_at' => 'just now',
            ],
        ];

        $this->dispatch('chatCleared', [
            'sessionId' => $this->sessionId,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Quick suggestion clicked
     */
    public function sendSuggestion(string $suggestion): void
    {
        $this->message = $suggestion;
        $this->sendMessage(app(GeminiService::class));
    }

    /**
     * Format markdown text to HTML
     */
    public function formatMarkdown(string $text): string
    {
        // Convert URLs to links
        $text = preg_replace(
            '/(https?:\/\/[^\s<]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">$1</a>',
            $text
        );

        // Convert **bold** to <strong>
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

        // Convert *italic* to <em>
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);

        // Convert `code` to <code>
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);

        // Convert bullet points
        $lines = explode("\n", $text);
        $inList = false;
        $result = [];

        foreach ($lines as $line) {
            if (preg_match('/^[•\-\*]\s+(.+)$/', $line, $matches)) {
                if (!$inList) {
                    $result[] = '<ul class="list-disc pl-5 space-y-1">';
                    $inList = true;
                }
                $result[] = '<li>' . $matches[1] . '</li>';
            } else {
                if ($inList) {
                    $result[] = '</ul>';
                    $inList = false;
                }
                $result[] = $line;
            }
        }

        if ($inList) {
            $result[] = '</ul>';
        }

        $text = implode("\n", $result);

        // Convert newlines to <br> (but not inside lists)
        $text = preg_replace('/([^<])\n([^<])/', '$1<br>$2', $text);

        return $text;
    }

    public function render()
    {
        return view('livewire.chat-bot');
    }
}
