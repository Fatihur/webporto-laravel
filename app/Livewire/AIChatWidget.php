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
                'content' => 'Hi! 👋 Saya asisten AI Fatih (powered by OpenRouter). Ada yang bisa saya bantu tentang portfolio, project, atau blog?',
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
            'content' => 'Hi! 👋 Saya asisten AI Fatih (powered by OpenRouter). Ada yang bisa saya bantu tentang portfolio, project, atau blog?',
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
     * Format message by removing markdown formatting and converting buttons.
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

        // Remove markdown bold (**text**)
        $content = preg_replace('/\*\*(.*?)\*\*/', '$1', $content);

        // Remove markdown italic (*text*)
        $content = preg_replace('/\*(.*?)\*/', '$1', $content);

        // Remove markdown code blocks
        $content = preg_replace('/```[\s\S]*?```/', '', $content);

        // Remove inline code
        $content = preg_replace('/`(.*?)`/', '$1', $content);

        // Remove markdown links [text](url) -> keep just text
        $content = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $content);

        // Clean up extra newlines
        $content = preg_replace("/\n{3,}/", "\n\n", $content);

        return [
            'text' => trim($content),
            'buttons' => $buttons,
        ];
    }

    public function render()
    {
        return view('livewire.ai-chat-widget');
    }
}
