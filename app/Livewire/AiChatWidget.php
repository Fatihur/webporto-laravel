<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Ai\Agents\PortfolioAssistant;
use App\Services\UserContextService;
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

    public array $userContexts = [];

    // Game State
    public ?array $activeGame = null;

    public int $gameScore = 0;

    public int $gameStreak = 0;

    protected UserContextService $contextService;

    public function boot(): void
    {
        $this->contextService = new UserContextService;
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
        if ($this->activeGame && !str_starts_with(strtolower($userMessage), 'game:')) {
            // Handle as game answer
            $this->handleGameAnswer($userMessage);
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

        try {
            $agent = new PortfolioAssistant;

            // Continue existing conversation if available
            if ($this->conversationId) {
                $agent = $agent->continue($this->conversationId);
            }

            // Build prompt with context (ISOLATED)
            $promptWithContext = $this->buildPromptWithContext($userMessage);
            $response = $agent->prompt($promptWithContext);

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
     * Build prompt with user context (ISOLATED per session)
     */
    private function buildPromptWithContext(string $message): string
    {
        $context = $this->contextService->getForAiPrompt();

        if (empty($context)) {
            return $message;
        }

        return $context."\n\nPertanyaan user: ".$message;
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
        $operations = ['+', '-', '*'];
        $operation = $operations[array_rand($operations)];

        // Difficulty increases with streak
        $max = min(10 + ($this->gameStreak * 2), 50);

        $num1 = rand(1, $max);
        $num2 = rand(1, $max);

        // Ensure positive result for subtraction
        if ($operation === '-' && $num1 < $num2) {
            [$num1, $num2] = [$num2, $num1];
        }

        $question = "Berapa {$num1} {$operation} {$num2}?";

        switch ($operation) {
            case '+':
                $answer = $num1 + $num2;
                break;
            case '-':
                $answer = $num1 - $num2;
                break;
            case '*':
                $answer = $num1 * $num2;
                break;
            default:
                $answer = 0;
        }

        $this->activeGame['question'] = $question;
        $this->activeGame['answer'] = $answer;
        $this->activeGame['input_type'] = 'number';
        $this->activeGame['options'] = null;
    }

    /**
     * Generate puzzle/teka-teki question
     */
    private function generatePuzzleQuestion(): void
    {
        $puzzles = [
            [
                'question' => '🧩 Teka-teki: Aku punya lobang di tengah, tapi aku bisa menampung air. Apakah aku?',
                'options' => ['Gelas', 'Ember', 'Spons', 'Piring'],
                'answer' => 2, // Spons (index 2)
            ],
            [
                'question' => '🧩 Teka-teki: Semakin banyak aku diambil, semakin banyak aku tinggalkan. Apakah aku?',
                'options' => ['Uang', 'Jejak', 'Waktu', 'Memori'],
                'answer' => 1, // Jejak (index 1)
            ],
            [
                'question' => '🧩 Teka-teki: Aku selalu naik tapi tidak pernah turun. Apakah aku?',
                'options' => ['Balon', 'Usia', 'Harga', 'Tangga'],
                'answer' => 1, // Usia (index 1)
            ],
            [
                'question' => '🧩 Teka-teki: Apa yang punya 4 kaki di pagi hari, 2 kaki di siang hari, dan 3 kaki di malam hari?',
                'options' => ['Kursi', 'Manusia', 'Meja', 'Binatang'],
                'answer' => 1, // Manusia (index 1)
            ],
            [
                'question' => '🧩 Teka-teki: Aku ringan seperti bulu, tapi orang terkuat pun tidak bisa memegangku lebih dari 5 menit. Apakah aku?',
                'options' => ['Bulu', 'Napas', 'Asap', 'Kapas'],
                'answer' => 1, // Napas (index 1)
            ],
            [
                'question' => '🧩 Teka-teki: Apa yang hancur ketika kamu sebut namanya?',
                'options' => ['Cermin', 'Kesunyian', 'Hati', 'Es'],
                'answer' => 1, // Kesunyian (index 1)
            ],
        ];

        $puzzle = $puzzles[array_rand($puzzles)];

        $this->activeGame['question'] = $puzzle['question'];
        $this->activeGame['answer'] = $puzzle['answer'];
        $this->activeGame['input_type'] = 'select';
        $this->activeGame['options'] = $puzzle['options'];
    }

    /**
     * Generate multiple choice quiz
     */
    private function generateQuizQuestion(): void
    {
        $quizzes = [
            [
                'question' => '📚 Apa kepanjangan dari HTML?',
                'options' => ['Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Mark Language', 'Home Tool Markup Language'],
                'answer' => 0,
            ],
            [
                'question' => '📚 Bahasa pemrograman apa yang digunakan framework Laravel?',
                'options' => ['Python', 'JavaScript', 'PHP', 'Ruby'],
                'answer' => 2,
            ],
            [
                'question' => '📚 CSS digunakan untuk?',
                'options' => ['Membuat struktur web', 'Mendesain tampilan web', 'Membuat logika program', 'Mengelola database'],
                'answer' => 1,
            ],
            [
                'question' => '📚 Apa fungsi utama dari Git?',
                'options' => ['Membuat website', 'Version control / Mengelola versi kode', 'Database management', 'Server hosting'],
                'answer' => 1,
            ],
            [
                'question' => '📚 Manakah yang BUKAN merupakan database?',
                'options' => ['MySQL', 'MongoDB', 'PostgreSQL', 'Bootstrap'],
                'answer' => 3,
            ],
            [
                'question' => '📚 Apa kepanjangan dari API?',
                'options' => ['Application Programming Interface', 'Advanced Program Integration', 'Automated Processing Instruction', 'Application Process Interface'],
                'answer' => 0,
            ],
            [
                'question' => '📚 Framework CSS yang populer saat ini?',
                'options' => ['jQuery', 'Bootstrap', 'Tailwind CSS', 'Semua benar'],
                'answer' => 3,
            ],
        ];

        $quiz = $quizzes[array_rand($quizzes)];

        $this->activeGame['question'] = $quiz['question'];
        $this->activeGame['answer'] = $quiz['answer'];
        $this->activeGame['input_type'] = 'select';
        $this->activeGame['options'] = $quiz['options'];
    }

    /**
     * Format message by converting markdown to HTML and extracting buttons/suggestions.
     */
    public function formatMessage(string $content): array
    {
        // Game inputs (to be rendered specially)
        $gameInputs = [];

        // Parse number input [INPUT:number|game_answer]
        if (preg_match('/\[INPUT:(.*?)\|(game_answer)\]/', $content, $matches)) {
            $gameInputs[] = [
                'type' => 'number',
                'action' => $matches[2],
            ];
            $content = preg_replace('/\[INPUT:(.*?)\|game_answer\]/', '', $content);
        }

        // Parse select input [SELECT:game_answer|option1,option2,option3]
        if (preg_match('/\[SELECT:(game_answer)\|(.*?)\]/', $content, $matches)) {
            $options = explode(',', $matches[2]);
            $gameInputs[] = [
                'type' => 'select',
                'action' => $matches[1],
                'options' => array_map('trim', $options),
            ];
            $content = preg_replace('/\[SELECT:game_answer\|.*?\]/', '', $content);
        }

        // Parse buttons first [BUTTON:Label|/path] or [BUTTON:Label|game:xxx]
        $buttons = [];
        $content = preg_replace_callback(
            '/\[BUTTON:(.*?)\|(.*?)\]/',
            function ($matches) use (&$buttons) {
                $buttons[] = [
                    'label' => trim($matches[1]),
                    'url' => trim($matches[2]),
                    'isGameAction' => str_starts_with(trim($matches[2]), 'game:'),
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
            'gameInputs' => $gameInputs,
        ];
    }

    public function render()
    {
        return view('livewire.ai-chat-widget', [
            'activeGame' => $this->activeGame,
        ]);
    }
}
