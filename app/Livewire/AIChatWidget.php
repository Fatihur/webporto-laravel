<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Ai\Agents\PortfolioAssistant;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Throwable;

class AIChatWidget extends Component
{
    public bool $isOpen = false;

    public string $message = '';

    public array $chatHistory = [];

    public bool $isLoading = false;

    public ?string $conversationId = null;

    public function mount(): void
    {
        $this->conversationId = Session::get('ai_conversation_id');
        $this->chatHistory = Session::get('ai_chat_history', []);

        // Add welcome message if no history
        if (empty($this->chatHistory)) {
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => 'Halo! 👋 Aku Fay, asisten AI-nya Fatih. Mau tau tentang portfolio, project, atau blog? Chat aja!',
                'timestamp' => now()->toIso8601String(),
            ];
            $this->saveChatHistory();
        }
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function sendMessage(): void
    {
        // Method ini dipanggil dari Alpine.js dengan parameter
    }

    /**
     * Method untuk menerima pesan dari Alpine.js dan proses AI.
     */
    public function processMessage(string $message): void
    {
        $userMessage = trim($message);

        if (empty($userMessage)) {
            return;
        }

        // Add user message to history (tambahkan lagi untuk sinkronisasi)
        $this->chatHistory[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->isLoading = true;
        $this->saveChatHistory();

        // Dispatch event untuk update UI
        $this->dispatch('chat-updated');

        try {
            $agent = new PortfolioAssistant;

            // Continue existing conversation if available
            if ($this->conversationId) {
                $agent = $agent->continue($this->conversationId);
            }

            $response = $agent->prompt($userMessage);

            // Save conversation ID for continuity
            if ($response->conversationId) {
                $this->conversationId = $response->conversationId;
                Session::put('ai_conversation_id', $this->conversationId);
            }

            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => (string) $response,
                'timestamp' => now()->toIso8601String(),
            ];
        } catch (Throwable $e) {
            // Jika API error, gunakan response lokal untuk demo
            $demoResponse = $this->generateDemoResponse($userMessage);

            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => $demoResponse,
                'timestamp' => now()->toIso8601String(),
            ];
        }

        $this->isLoading = false;
        $this->saveChatHistory();

        // Dispatch event untuk scroll ke bawah setelah response
        $this->dispatch('chat-updated');
    }

    /**
     * Generate local fallback response when API is temporarily unavailable.
     */
    private function generateDemoResponse(string $message): string
    {
        $lowerMessage = strtolower($message);

        if (str_contains($lowerMessage, 'halo') || str_contains($lowerMessage, 'hi') || str_contains($lowerMessage, 'hello')) {
            return "Halo! 👋 Saya adalah AI Assistant Fatih. Saat ini layanan AI sedang sementara tidak tersedia, tapi saya masih bisa membantu dengan informasi dasar.\n\nAnda bisa bertanya tentang:\n• Project portfolio Fatih\n• Blog dan artikel\n• Pengalaman kerja\n• Cara menghubungi";
        }

        if (str_contains($lowerMessage, 'project') || str_contains($lowerMessage, 'portfolio')) {
            $projects = \App\Models\Project::query()
                ->recent()
                ->limit(3)
                ->get();

            if ($projects->isEmpty()) {
                return 'Saat ini belum ada project yang ditampilkan dalam portfolio.';
            }

            $response = "Berikut beberapa project terbaru Fatih:\n\n";
            foreach ($projects as $project) {
                $response .= "• **{$project->title}** ({$project->category})\n";
                $response .= '  '.strip_tags($project->description)."\n\n";
            }
            $response .= 'Lihat detail selengkapnya di halaman Projects.';

            return $response;
        }

        if (str_contains($lowerMessage, 'blog') || str_contains($lowerMessage, 'artikel')) {
            $blogs = \App\Models\Blog::query()
                ->published()
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get();

            if ($blogs->isEmpty()) {
                return 'Saat ini belum ada artikel blog yang dipublikasikan.';
            }

            $response = "Berikut artikel blog terbaru:\n\n";
            foreach ($blogs as $blog) {
                $response .= "• **{$blog->title}**\n";
                $response .= '  '.strip_tags($blog->excerpt)."\n\n";
            }

            return $response;
        }

        if (str_contains($lowerMessage, 'pengalaman') || str_contains($lowerMessage, 'kerja') || str_contains($lowerMessage, 'experience')) {
            $experiences = \App\Models\Experience::query()
                ->ordered()
                ->get();

            if ($experiences->isEmpty()) {
                return 'Belum ada data pengalaman kerja yang tersedia.';
            }

            $response = "Pengalaman kerja Fatih:\n\n";
            foreach ($experiences as $exp) {
                $response .= "• **{$exp->role}** di {$exp->company}\n";
                $response .= "  {$exp->dateRange()}\n\n";
            }

            return $response;
        }

        if (str_contains($lowerMessage, 'kontak') || str_contains($lowerMessage, 'hubungi')) {
            return "Anda bisa menghubungi Fatih melalui:\n\n• Halaman Contact: /contact\n• Email: fatihur17@gmail.com\n\nAtau kunjungi halaman Contact untuk mengirim pesan langsung.";
        }

        // Default response
        return "Maaf, layanan AI sedang sementara tidak tersedia. Silakan coba lagi nanti atau hubungi Fatih langsung melalui halaman Contact.\n\nAnda bisa bertanya tentang:\n• Project portfolio\n• Blog/artikel\n• Pengalaman kerja\n• Cara menghubungi Fatih";
    }

    public function clearChat(): void
    {
        $this->chatHistory = [];
        $this->conversationId = null;
        Session::forget('ai_conversation_id');
        Session::forget('ai_chat_history');

        // Add welcome message
        $this->chatHistory[] = [
            'role' => 'assistant',
            'content' => 'Halo! 👋 Aku Fay, asisten AI-nya Fatih. Mau tau tentang portfolio, project, atau blog? Chat aja!',
            'timestamp' => now()->toIso8601String(),
        ];
        $this->saveChatHistory();
    }

    private function saveChatHistory(): void
    {
        // Keep only last 20 messages to prevent session bloat
        $this->chatHistory = array_slice($this->chatHistory, -20);
        Session::put('ai_chat_history', $this->chatHistory);
    }

    /**
     * Format message by converting markdown to HTML and extracting buttons/suggestions.
     */
    public function formatMessage(string $content): array
    {
        // Parse buttons first [BUTTON:Label|/path]
        $buttons = [];
        $content = preg_replace_callback(
            '/\[BUTTON:(.*?)\|(.*?)\]/',
            function ($matches) use (&$buttons) {
                $buttons[] = [
                    'label' => trim($matches[1]),
                    'url' => trim($matches[2]),
                ];

                return ''; // Remove from content
            },
            $content
        );

        // Parse suggestions [SUGGEST:Label|Question]
        $suggestions = [];
        $content = preg_replace_callback(
            '/\[SUGGEST:(.*?)\|(.*?)\]/',
            function ($matches) use (&$suggestions) {
                $suggestions[] = [
                    'label' => trim($matches[1]),
                    'question' => trim($matches[2]),
                ];

                return ''; // Remove from content
            },
            $content
        );

        // Convert markdown bold (**text**) to strong (darker color for visibility)
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-semibold text-zinc-900 dark:text-white">$1</strong>', $content);

        // Convert markdown italic (*text*) to em
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);

        // Remove markdown code blocks but keep content
        $content = preg_replace_callback('/```[\s\S]*?```/', function ($matches) {
            $code = trim(substr($matches[0], 3, -3));

            return "<code class=\"bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-xs font-mono\">{$code}</code>";
        }, $content);

        // Convert inline code
        $content = preg_replace('/`(.*?)`/', '<code class="bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-xs font-mono">$1</code>', $content);

        // Convert markdown links [text](url) to anchor tags
        $content = preg_replace_callback('/\[(.*?)\]\(https?:\/\/.*?\)/', function ($matches) {
            $text = $matches[1];
            $url = preg_replace('/\[(.*?)\]\((.*?)\)/', '$2', $matches[0]);

            return "<a href=\"{$url}\" target=\"_blank\" rel=\"noopener\" class=\"text-mint hover:underline\">{$text}</a>";
        }, $content);

        // Convert newlines to <br> tags
        $content = preg_replace('/\n/', '<br>', $content);

        // Clean up extra breaks
        $content = preg_replace('/(<br>\s*){3,}/', '<br><br>', $content);

        return [
            'text' => trim($content),
            'buttons' => $buttons,
            'suggestions' => $suggestions,
        ];
    }

    public function render()
    {
        return view('livewire.ai-chat-widget');
    }
}
