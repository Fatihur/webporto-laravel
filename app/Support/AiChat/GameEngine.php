<?php

declare(strict_types=1);

namespace App\Support\AiChat;

class GameEngine
{
    /**
     * @return array{question: string, answer: int, input_type: string, options: null}
     */
    public function mathQuestion(int $streak): array
    {
        $operations = ['+', '-', '*'];
        $operation = $operations[array_rand($operations)];

        $max = min(10 + ($streak * 2), 50);

        $num1 = rand(1, $max);
        $num2 = rand(1, $max);

        if ($operation === '-' && $num1 < $num2) {
            [$num1, $num2] = [$num2, $num1];
        }

        $answer = match ($operation) {
            '+' => $num1 + $num2,
            '-' => $num1 - $num2,
            '*' => $num1 * $num2,
            default => 0,
        };

        return [
            'question' => "Berapa {$num1} {$operation} {$num2}?",
            'answer' => $answer,
            'input_type' => 'number',
            'options' => null,
        ];
    }

    /**
     * @return array{question: string, answer: int, input_type: string, options: array<int, string>}
     */
    public function puzzleQuestion(): array
    {
        $puzzles = [
            [
                'question' => '🧩 Teka-teki: Aku punya lobang di tengah, tapi aku bisa menampung air. Apakah aku?',
                'options' => ['Gelas', 'Ember', 'Spons', 'Piring'],
                'answer' => 2,
            ],
            [
                'question' => '🧩 Teka-teki: Semakin banyak aku diambil, semakin banyak aku tinggalkan. Apakah aku?',
                'options' => ['Uang', 'Jejak', 'Waktu', 'Memori'],
                'answer' => 1,
            ],
            [
                'question' => '🧩 Teka-teki: Aku selalu naik tapi tidak pernah turun. Apakah aku?',
                'options' => ['Balon', 'Usia', 'Harga', 'Tangga'],
                'answer' => 1,
            ],
            [
                'question' => '🧩 Teka-teki: Apa yang punya 4 kaki di pagi hari, 2 kaki di siang hari, dan 3 kaki di malam hari?',
                'options' => ['Kursi', 'Manusia', 'Meja', 'Binatang'],
                'answer' => 1,
            ],
            [
                'question' => '🧩 Teka-teki: Aku ringan seperti bulu, tapi orang terkuat pun tidak bisa memegangku lebih dari 5 menit. Apakah aku?',
                'options' => ['Bulu', 'Napas', 'Asap', 'Kapas'],
                'answer' => 1,
            ],
            [
                'question' => '🧩 Teka-teki: Apa yang hancur ketika kamu sebut namanya?',
                'options' => ['Cermin', 'Kesunyian', 'Hati', 'Es'],
                'answer' => 1,
            ],
        ];

        $puzzle = $puzzles[array_rand($puzzles)];

        return [
            'question' => $puzzle['question'],
            'answer' => $puzzle['answer'],
            'input_type' => 'select',
            'options' => $puzzle['options'],
        ];
    }

    /**
     * @return array{question: string, answer: int, input_type: string, options: array<int, string>}
     */
    public function quizQuestion(): array
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

        return [
            'question' => $quiz['question'],
            'answer' => $quiz['answer'],
            'input_type' => 'select',
            'options' => $quiz['options'],
        ];
    }
}
