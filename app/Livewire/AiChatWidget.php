<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Ai\Agents\PortfolioAssistant;
use App\Models\Contact;
use App\Services\AiChatRetrievalService;
use App\Services\UserContextService;
use App\Support\AiChat\GameEngine;
use App\Support\AiChat\MessageFormatter;
use Illuminate\Support\Facades\Session;
use Laravel\Ai\Exceptions\RateLimitedException;
use Livewire\Component;
use Throwable;

class AIChatWidget extends Component
{
    public bool $isOpen = false;

    public string $message = '';

    public array $chatHistory = [];

    public bool $isLoading = false;

    public ?string $conversationId = null;

    public array $userContexts = [];

    // Game State
    public ?array $activeGame = null;

    public int $gameScore = 0;

    public int $gameStreak = 0;

    protected UserContextService $contextService;

    protected MessageFormatter $messageFormatter;

    protected GameEngine $gameEngine;

    protected AiChatRetrievalService $retrievalService;

    public bool $leadMode = false;

    /**
     * @var array{name: string, email: string, project_type: string, budget: string, timeline: string, message: string}
     */
    public array $leadDraft = [
        'name' => '',
        'email' => '',
        'project_type' => '',
        'budget' => '',
        'timeline' => '',
        'message' => '',
    ];

    public function boot(): void
    {
        $this->contextService = new UserContextService;
        $this->messageFormatter = new MessageFormatter;
        $this->gameEngine = new GameEngine;
        $this->retrievalService = app(AiChatRetrievalService::class);
    }

    public function mount(): void
    {
        $this->conversationId = Session::get('ai_conversation_id');
        $this->chatHistory = Session::get('ai_chat_history', []);
        $this->loadUserContexts();

        // Load game state
        $this->activeGame = Session::get('ai_active_game');
        $this->gameScore = Session::get('ai_game_score', 0);
        $this->gameStreak = Session::get('ai_game_streak', 0);
        $this->leadMode = Session::get('ai_lead_mode', false);
        $this->leadDraft = Session::get('ai_lead_draft', $this->leadDraft);

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

    /**
     * Load user contexts for display in UI
     */
    private function loadUserContexts(): void
    {
        $this->userContexts = $this->contextService->getAll();
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

        if ($this->leadMode) {
            $this->handleLeadFlow($userMessage);

            return;
        }

        // Handle stop game command FIRST (before treating as game answer)
        $stopCommands = ['stop', 'stop game', 'end', 'end game', 'berhenti', 'keluar', 'quit', 'exit'];
        if ($this->activeGame && in_array(strtolower($userMessage), $stopCommands)) {
            // Add user message to history
            $this->chatHistory[] = [
                'role' => 'user',
                'content' => $userMessage,
                'timestamp' => now()->toIso8601String(),
            ];
            $this->saveChatHistory();
            $this->endGame();

            return;
        }

        // Check if we're in active game mode (and not a game command)
        if ($this->activeGame && ! str_starts_with(strtolower($userMessage), 'game:')) {
            // Handle as game answer
            $this->handleGameAnswer($userMessage);

            return;
        }

        if ($this->shouldStartLeadFlow($userMessage)) {
            $this->startLeadFlow($userMessage);

            return;
        }

        // Extract context from user message (ISOLATED per session)
        $this->contextService->extractFromMessage($userMessage);
        $this->loadUserContexts(); // Refresh UI

        // Add user message to history
        $this->chatHistory[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->isLoading = true;
        $this->saveChatHistory();

        // Dispatch event untuk update UI
        $this->dispatch('chat-updated');

        $retrieval = $this->retrievalService->retrieve($userMessage, 3);

        try {
            $agent = new PortfolioAssistant;
            // Convert session ID to integer for database compatibility
            $sessionId = Session::getId();
            $userId = abs(crc32($sessionId)) ?: 1; // Convert string to positive integer
            $conversationUser = (object) ['id' => $userId];

            // Continue existing conversation if available
            if ($this->conversationId) {
                $agent = $agent->continue($this->conversationId, $conversationUser);
            } else {
                $agent = $agent->forUser($conversationUser);
            }

            // Build prompt with context (ISOLATED)
            $promptWithContext = $this->buildPromptWithContext($userMessage, $retrieval);
            $response = $agent->prompt($promptWithContext);

            // Save conversation ID for continuity
            if ($response->conversationId) {
                $this->conversationId = $response->conversationId;
                Session::put('ai_conversation_id', $this->conversationId);
            }

            $assistantResponse = (string) $response;
            $citationBlock = $this->retrievalService->formatCitationBlock($retrieval);
            if ($citationBlock !== '') {
                $assistantResponse .= "\n\n".$citationBlock;
            }

            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => $assistantResponse,
                'timestamp' => now()->toIso8601String(),
            ];
        } catch (RateLimitedException $e) {
            logger()->warning('AI provider rate limited, using local fallback.', [
                'exception' => $e,
                'user_message' => $userMessage,
            ]);

            $fallbackResponse = "⚠️ Groq lagi kena rate limit. Aku lanjut bantu pakai mode fallback lokal dulu ya.\n\n".$this->generateDemoResponse($userMessage);
            $citationBlock = $this->retrievalService->formatCitationBlock($retrieval);
            if ($citationBlock !== '') {
                $fallbackResponse .= "\n\n".$citationBlock;
            }

            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => $fallbackResponse,
                'timestamp' => now()->toIso8601String(),
            ];
        } catch (Throwable $e) {
            // Log error untuk debugging
            logger()->error('AI Chat Error: '.$e->getMessage(), [
                'exception' => $e,
                'user_message' => $userMessage,
            ]);

            // Jika API error, gunakan response lokal untuk demo
            $demoResponse = $this->generateDemoResponse($userMessage);
            $citationBlock = $this->retrievalService->formatCitationBlock($retrieval);
            if ($citationBlock !== '') {
                $demoResponse .= "\n\n".$citationBlock;
            }

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
     * Build prompt with user context (ISOLATED per session)
     */
    private function buildPromptWithContext(string $message, array $retrieval = []): string
    {
        $parts = [];

        $context = $this->contextService->getForAiPrompt();
        if (! empty($context)) {
            $parts[] = $context;
        }

        $retrievalContext = $this->retrievalService->buildPromptContext($retrieval);
        if ($retrievalContext !== '') {
            $parts[] = $retrievalContext;
        }

        if (empty($parts)) {
            return $message;
        }

        return implode("\n\n", $parts)."\n\nPertanyaan user: ".$message;
    }

    private function shouldStartLeadFlow(string $message): bool
    {
        $normalized = strtolower($message);
        $keywords = [
            'hire',
            'hiring',
            'kerja sama',
            'kolaborasi',
            'project',
            'proyek',
            'buat website',
            'jasa',
            'budget',
            'pricing',
            'quotation',
            'quote',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function startLeadFlow(string $initialMessage): void
    {
        $this->leadMode = true;
        $this->leadDraft = [
            'name' => $this->userContexts['name'] ?? '',
            'email' => '',
            'project_type' => $this->userContexts['project_type'] ?? '',
            'budget' => $this->userContexts['budget'] ?? '',
            'timeline' => '',
            'message' => $initialMessage,
        ];

        $this->chatHistory[] = [
            'role' => 'user',
            'content' => $initialMessage,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->chatHistory[] = [
            'role' => 'assistant',
            'content' => "Mantap, aku bantu kumpulin kebutuhan project kamu ya. 🚀\n\nSebelum lanjut, boleh kasih **nama kamu** dulu?\n\nKetik juga **batal** kalau gak jadi.",
            'timestamp' => now()->toIso8601String(),
        ];

        $this->saveChatHistory();
        $this->dispatch('chat-updated');
    }

    private function handleLeadFlow(string $message): void
    {
        $input = trim($message);
        $lower = strtolower($input);

        $this->chatHistory[] = [
            'role' => 'user',
            'content' => $input,
            'timestamp' => now()->toIso8601String(),
        ];

        if (in_array($lower, ['batal', 'cancel', 'stop'], true)) {
            $this->leadMode = false;
            $this->leadDraft = [
                'name' => '',
                'email' => '',
                'project_type' => '',
                'budget' => '',
                'timeline' => '',
                'message' => '',
            ];

            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => 'Siap, flow lead aku batalkan ya. Kalau mau lanjut lagi tinggal bilang aja. 🙂',
                'timestamp' => now()->toIso8601String(),
            ];

            $this->saveChatHistory();
            $this->dispatch('chat-updated');

            return;
        }

        if ($this->leadDraft['name'] === '') {
            $this->leadDraft['name'] = $input;
            $this->contextService->save('name', $input);
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => 'Thanks, '.$input.'! Sekarang boleh kasih **email aktif** kamu?',
                'timestamp' => now()->toIso8601String(),
            ];
            $this->saveChatHistory();
            $this->dispatch('chat-updated');

            return;
        }

        if ($this->leadDraft['email'] === '') {
            if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $this->chatHistory[] = [
                    'role' => 'assistant',
                    'content' => 'Sepertinya format email belum valid. Coba kirim email yang benar ya.',
                    'timestamp' => now()->toIso8601String(),
                ];
                $this->saveChatHistory();
                $this->dispatch('chat-updated');

                return;
            }

            $this->leadDraft['email'] = $input;
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => 'Noted. Jenis project kamu apa? (contoh: software_dev, graphic_design, data_analysis, networking)',
                'timestamp' => now()->toIso8601String(),
            ];
            $this->saveChatHistory();
            $this->dispatch('chat-updated');

            return;
        }

        if ($this->leadDraft['project_type'] === '') {
            $this->leadDraft['project_type'] = $input;
            $this->contextService->save('project_type', $input);
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => 'Sip. Kalau boleh tahu, kisaran **budget** kamu berapa?',
                'timestamp' => now()->toIso8601String(),
            ];
            $this->saveChatHistory();
            $this->dispatch('chat-updated');

            return;
        }

        if ($this->leadDraft['budget'] === '') {
            $this->leadDraft['budget'] = $input;
            $this->contextService->save('budget', $input);
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => 'Oke. Target **timeline** pengerjaan yang kamu harapkan berapa lama?',
                'timestamp' => now()->toIso8601String(),
            ];
            $this->saveChatHistory();
            $this->dispatch('chat-updated');

            return;
        }

        if ($this->leadDraft['timeline'] === '') {
            $this->leadDraft['timeline'] = $input;
            $this->persistLeadToContacts();

            $this->leadMode = false;
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => "Berhasil! ✅ Brief kamu sudah aku teruskan ke tim Fatih.\n\nKamu juga bisa lanjut via halaman contact: [BUTTON:Buka Contact|/contact]",
                'timestamp' => now()->toIso8601String(),
            ];
            $this->saveChatHistory();
            $this->dispatch('chat-updated');
        }
    }

    private function persistLeadToContacts(): void
    {
        Contact::create([
            'name' => $this->leadDraft['name'],
            'email' => $this->leadDraft['email'],
            'subject' => 'AI Lead: '.($this->leadDraft['project_type'] ?: 'general'),
            'message' => trim(implode("\n", [
                'Sumber: AI Chat Widget',
                'Project Type: '.$this->leadDraft['project_type'],
                'Budget: '.$this->leadDraft['budget'],
                'Timeline: '.$this->leadDraft['timeline'],
                '',
                'Pesan awal user:',
                $this->leadDraft['message'],
            ])),
            'is_read' => false,
        ]);
    }

    /**
     * Generate local fallback response when API is temporarily unavailable.
     */
    private function generateDemoResponse(string $message): string
    {
        $lowerMessage = strtolower($message);

        // Check for game commands
        if (str_contains($lowerMessage, 'main game') || str_contains($lowerMessage, 'mulai game')) {
            return "🎮 **Pilih Game:**\n\n[BUTTON:Math Quiz|game:math]\n[BUTTON:Teka-Teki|game:puzzle]\n[BUTTON:Tech Quiz|game:quiz]\n\nAtau ketik:\n• \"math\" - Quiz matematika\n• \"puzzle\" - Teka-teki logika\n• \"quiz\" - Quiz teknologi";
        }

        if (str_contains($lowerMessage, 'math') || str_contains($lowerMessage, 'matematika')) {
            $this->startGame('math');

            return "🧮 **Math Quiz dimulai!**\n\nScore: {$this->gameScore}\nStreak: {$this->gameStreak}\n\n".$this->activeGame['question']."\n\n[INPUT:number|game_answer]\n\n[BUTTON:Stop Game|game:end]";
        }

        if (str_contains($lowerMessage, 'puzzle') || str_contains($lowerMessage, 'teka-teki')) {
            $this->startGame('puzzle');
            $optionsText = implode("\n", array_map(fn ($i, $opt) => ($i + 1).'. '.$opt, array_keys($this->activeGame['options']), $this->activeGame['options']));

            return "🧩 **Teka-Teki dimulai!**\n\nScore: {$this->gameScore}\n\n".$this->activeGame['question']."\n\n".$optionsText."\n\n[SELECT:game_answer|".implode(',', $this->activeGame['options'])."]\n\n[BUTTON:Stop Game|game:end]";
        }

        if (str_contains($lowerMessage, 'quiz') && ! str_contains($lowerMessage, 'math')) {
            $this->startGame('quiz');
            $optionsText = implode("\n", array_map(fn ($i, $opt) => ($i + 1).'. '.$opt, array_keys($this->activeGame['options']), $this->activeGame['options']));

            return "📚 **Tech Quiz dimulai!**\n\nScore: {$this->gameScore}\n\n".$this->activeGame['question']."\n\n".$optionsText."\n\n[SELECT:game_answer|".implode(',', $this->activeGame['options'])."]\n\n[BUTTON:Stop Game|game:end]";
        }

        if (str_contains($lowerMessage, 'stop game') || str_contains($lowerMessage, 'end game')) {
            $this->endGame();

            return '';
        }

        if (str_contains($lowerMessage, 'halo') || str_contains($lowerMessage, 'hi') || str_contains($lowerMessage, 'hello')) {
            return "Halo! 👋 Aku Fay, asisten AI-nya Fatih. Saat ini layanan AI lagi gangguan nih, tapi tenang, aku masih bisa bantu dengan info dasar 😄\n\nMau tau tentang:\n• Project portfolio Fatih\n• Blog dan artikel\n• Pengalaman kerja\n• Cara hubungi Fatih\n\nAtau mau main game? Ketik \"main game\"! 🎮";
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

    /**
     * Clear chat history AND user context (full reset)
     */
    public function clearChat(): void
    {
        $this->chatHistory = [];
        $this->conversationId = null;
        $this->userContexts = [];
        $this->activeGame = null;
        $this->gameScore = 0;
        $this->gameStreak = 0;

        Session::forget('ai_conversation_id');
        Session::forget('ai_chat_history');
        Session::forget('ai_active_game');
        Session::forget('ai_game_score');
        Session::forget('ai_game_streak');

        // Clear isolated context for this session only
        $this->contextService->clearAll();

        // Add welcome message
        $this->chatHistory[] = [
            'role' => 'assistant',
            'content' => 'Halo! 👋 Aku Fay, asisten AI-nya Fatih. Mau tau tentang portfolio, project, atau blog? Chat aja!',
            'timestamp' => now()->toIso8601String(),
        ];
        $this->saveChatHistory();
    }

    /**
     * Clear only user context (keep chat history)
     */
    public function clearContext(): void
    {
        $this->contextService->clearAll();
        $this->userContexts = [];

        $this->chatHistory[] = [
            'role' => 'assistant',
            'content' => 'Oke! Aku sudah lupa semua informasi tentang kamu. Kita mulai dari awal ya! 👋',
            'timestamp' => now()->toIso8601String(),
        ];
        $this->saveChatHistory();
    }

    /**
     * Forget specific context type
     */
    public function forgetContext(string $type): void
    {
        $this->contextService->forget($type);
        $this->loadUserContexts();
    }

    private function saveChatHistory(): void
    {
        // Keep only last 20 messages to prevent session bloat
        $this->chatHistory = array_slice($this->chatHistory, -20);
        Session::put('ai_chat_history', $this->chatHistory);

        // Save game state
        Session::put('ai_active_game', $this->activeGame);
        Session::put('ai_game_score', $this->gameScore);
        Session::put('ai_game_streak', $this->gameStreak);
    }

    // ==========================================
    // GAME METHODS
    // ==========================================

    /**
     * Start a new game session
     */
    public function startGame(string $gameType): void
    {
        $this->activeGame = [
            'type' => $gameType,
            'question' => null,
            'answer' => null,
            'options' => null,
            'startTime' => now()->timestamp,
        ];

        $welcomeMessages = [
            'math' => '🧮 **Math Quiz dimulai!**\n\nJawab soal matematika secepat mungkin!\n+10 poin untuk setiap jawaban benar\n🔥 Streak bonus untuk jawaban berturut-turut!',
            'puzzle' => '🧩 **Teka-Teki dimulai!**\n\nTebak teka-teki logika ini!\n+10 poin untuk setiap jawaban benar',
            'quiz' => '📚 **Tech Quiz dimulai!**\n\nUji pengetahuan teknologimu!\n+10 poin untuk setiap jawaban benar',
        ];

        // Send game welcome message
        $this->chatHistory[] = [
            'role' => 'assistant',
            'content' => $welcomeMessages[$gameType] ?? '🎮 Game dimulai!',
            'timestamp' => now()->toIso8601String(),
        ];

        switch ($gameType) {
            case 'math':
                $this->generateMathQuestion();
                $this->chatHistory[] = [
                    'role' => 'assistant',
                    'content' => "Score: {$this->gameScore}\nStreak: {$this->gameStreak}\n\n".$this->activeGame['question']."\n\n[INPUT:number|game_answer]\n\n[BUTTON:Stop Game|game:end]",
                    'timestamp' => now()->toIso8601String(),
                ];
                break;
            case 'puzzle':
                $this->generatePuzzleQuestion();
                $optionsText = implode("\n", array_map(fn ($i, $opt) => ($i + 1).'. '.$opt, array_keys($this->activeGame['options']), $this->activeGame['options']));
                $this->chatHistory[] = [
                    'role' => 'assistant',
                    'content' => "Score: {$this->gameScore}\n\n".$this->activeGame['question']."\n\n".$optionsText."\n\n[SELECT:game_answer|".implode(',', $this->activeGame['options'])."]\n\n[BUTTON:Stop Game|game:end]",
                    'timestamp' => now()->toIso8601String(),
                ];
                break;
            case 'quiz':
                $this->generateQuizQuestion();
                $optionsText = implode("\n", array_map(fn ($i, $opt) => ($i + 1).'. '.$opt, array_keys($this->activeGame['options']), $this->activeGame['options']));
                $this->chatHistory[] = [
                    'role' => 'assistant',
                    'content' => "Score: {$this->gameScore}\n\n".$this->activeGame['question']."\n\n".$optionsText."\n\n[SELECT:game_answer|".implode(',', $this->activeGame['options'])."]\n\n[BUTTON:Stop Game|game:end]",
                    'timestamp' => now()->toIso8601String(),
                ];
                break;
        }

        $this->saveChatHistory();
        $this->dispatch('chat-updated');
    }

    /**
     * Submit answer for active game
     */
    public function submitGameAnswer($answer): void
    {
        if (! $this->activeGame) {
            return;
        }

        $isCorrect = $answer == $this->activeGame['answer'];

        if ($isCorrect) {
            $this->gameScore += 10;
            $this->gameStreak++;
            $bonus = $this->gameStreak >= 3 ? ' 🔥 Streak x'.$this->gameStreak.'!' : '';
            $response = '🎉 Betul! +10 poin'.$bonus;
        } else {
            $this->gameStreak = 0;
            $correctAnswer = $this->activeGame['answer'];
            $response = '❌ Salah! Jawaban yang benar: '.$correctAnswer;
        }

        $this->chatHistory[] = [
            'role' => 'assistant',
            'content' => $response,
            'timestamp' => now()->toIso8601String(),
        ];

        // Generate next question
        switch ($this->activeGame['type']) {
            case 'math':
                $this->generateMathQuestion();
                $mathQuestion = "🧮 **Math Quiz**\n\nScore: {$this->gameScore}\nStreak: {$this->gameStreak}\n\n".$this->activeGame['question'];
                $this->chatHistory[] = [
                    'role' => 'assistant',
                    'content' => $mathQuestion."\n\n[INPUT:number|game_answer]\n\n[BUTTON:Stop Game|game:end]",
                    'timestamp' => now()->toIso8601String(),
                ];
                break;
            case 'puzzle':
                $this->generatePuzzleQuestion();
                $optionsText = implode("\n", array_map(fn ($i, $opt) => ($i + 1).'. '.$opt, array_keys($this->activeGame['options']), $this->activeGame['options']));
                $puzzleQuestion = "🧩 **Teka-Teki**\n\nScore: {$this->gameScore}\n\n".$this->activeGame['question'];
                $this->chatHistory[] = [
                    'role' => 'assistant',
                    'content' => $puzzleQuestion."\n\n".$optionsText."\n\n[SELECT:game_answer|".implode(',', $this->activeGame['options'])."]\n\n[BUTTON:Stop Game|game:end]",
                    'timestamp' => now()->toIso8601String(),
                ];
                break;
            case 'quiz':
                $this->generateQuizQuestion();
                $optionsText = implode("\n", array_map(fn ($i, $opt) => ($i + 1).'. '.$opt, array_keys($this->activeGame['options']), $this->activeGame['options']));
                $quizQuestion = "📚 **Tech Quiz**\n\nScore: {$this->gameScore}\n\n".$this->activeGame['question'];
                $this->chatHistory[] = [
                    'role' => 'assistant',
                    'content' => $quizQuestion."\n\n".$optionsText."\n\n[SELECT:game_answer|".implode(',', $this->activeGame['options'])."]\n\n[BUTTON:Stop Game|game:end]",
                    'timestamp' => now()->toIso8601String(),
                ];
                break;
        }

        $this->saveChatHistory();
        $this->dispatch('chat-updated');
    }

    /**
     * End current game session
     */
    public function endGame(): void
    {
        if ($this->activeGame) {
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => '🏁 Game selesai!\nFinal Score: '.$this->gameScore.'\nThanks for playing! 🎮',
                'timestamp' => now()->toIso8601String(),
            ];
        }

        $this->activeGame = null;
        $this->gameScore = 0;
        $this->gameStreak = 0;
        $this->saveChatHistory();
        $this->dispatch('chat-updated');
    }

    /**
     * Handle text message as potential game answer
     */
    private function handleGameAnswer(string $message): void
    {
        // Try to parse as number for math game
        if ($this->activeGame['type'] === 'math') {
            $answer = (int) filter_var($message, FILTER_SANITIZE_NUMBER_INT);
            if ($answer !== 0 || $message === '0') {
                $this->submitGameAnswer($answer);

                return;
            }
        }

        // For select-based games, check if message is option number
        if (in_array($this->activeGame['type'], ['puzzle', 'quiz'])) {
            $answer = (int) $message - 1; // Convert 1-based to 0-based
            if ($answer >= 0 && $answer < count($this->activeGame['options'])) {
                $this->submitGameAnswer($answer);

                return;
            }
        }

        // If not recognized as answer, show hint
        $this->chatHistory[] = [
            'role' => 'user',
            'content' => $message,
            'timestamp' => now()->toIso8601String(),
        ];
        $this->chatHistory[] = [
            'role' => 'assistant',
            'content' => "❓ Aku lagi nunggu jawaban game nih! Gunakan input yang tersedia atau ketik angka jawabanmu.\n\nKetik **stop** untuk berhenti main.",
            'timestamp' => now()->toIso8601String(),
        ];
        $this->saveChatHistory();
        $this->dispatch('chat-updated');
    }

    /**
     * Generate math question
     */
    private function generateMathQuestion(): void
    {
        $question = $this->gameEngine->mathQuestion($this->gameStreak);
        $this->activeGame['question'] = $question['question'];
        $this->activeGame['answer'] = $question['answer'];
        $this->activeGame['input_type'] = $question['input_type'];
        $this->activeGame['options'] = $question['options'];
    }

    /**
     * Generate puzzle/teka-teki question
     */
    private function generatePuzzleQuestion(): void
    {
        $puzzle = $this->gameEngine->puzzleQuestion();
        $this->activeGame['question'] = $puzzle['question'];
        $this->activeGame['answer'] = $puzzle['answer'];
        $this->activeGame['input_type'] = $puzzle['input_type'];
        $this->activeGame['options'] = $puzzle['options'];
    }

    /**
     * Generate multiple choice quiz
     */
    private function generateQuizQuestion(): void
    {
        $quiz = $this->gameEngine->quizQuestion();
        $this->activeGame['question'] = $quiz['question'];
        $this->activeGame['answer'] = $quiz['answer'];
        $this->activeGame['input_type'] = $quiz['input_type'];
        $this->activeGame['options'] = $quiz['options'];
    }

    /**
     * Format message by converting markdown to HTML and extracting buttons/suggestions.
     */
    public function formatMessage(string $content): array
    {
        return $this->messageFormatter->format($content);
    }

    public function render()
    {
        return view('livewire.ai-chat-widget', [
            'activeGame' => $this->activeGame,
        ]);
    }
}
