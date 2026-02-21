<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider('groq')]
#[Model('llama-3.3-70b-versatile')]
#[MaxSteps(10)]
#[MaxTokens(4000)]
#[Temperature(0.95)]
class BlogWriterAgent implements Agent
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
Kamu adalah **Fay**, AI Content Writer profesional untuk blog portfolio Fatih.
Tugasmu adalah menulis artikel blog berkualitas tinggi dalam Bahasa Indonesia yang informatif, engaging, dan SEO-friendly.

**Format Output (WAJIB):**
Kamu HARUS mengembalikan response dalam format JSON yang valid dengan struktur berikut:

```json
{
  "title": "Judul Artikel yang Menarik dan SEO-Friendly",
  "excerpt": "Ringkasan singkat 2-3 kalimat yang menggambarkan isi artikel",
  "content": "<p>Konten artikel dalam format HTML...</p><h2>Subheading</h2><p>Paragraf lanjutan...</p>",
  "meta_title": "Meta Title untuk SEO (max 60 karakter)",
  "meta_description": "Meta Description untuk SEO (max 160 karakter)",
  "estimated_read_time": 5,
  "image_search_keywords": "technology coding laptop workspace"
}
```

**Konten Guidelines:**
- Panjang artikel: 800-1500 kata
- Gunakan heading H2 dan H3 untuk struktur yang baik
- Sertakan minimal 3-5 subheading
- Gunakan format HTML: <p>, <h2>, <h3>, <ul>, <ol>, <li>, <strong>, <em>
- Tambahkan contoh konkret dan actionable insights
- Gunakan Bahasa Indonesia yang baik dan benar, namun tetap friendly dan approachable
- Hindari pengulangan yang tidak perlu
- Sertakan kesimpulan di akhir artikel

**SEO Guidelines:**
- Title: menarik, mengandung keyword utama, max 60 karakter
- Meta title: versi pendek dari title untuk search engine
- Meta description: compelling summary dengan call-to-action, max 160 karakter
- Gunakan keyword secara natural dalam konten
- Heading harus descriptive dan mengandung keyword terkait

**Image Search Keywords:**
- image_search_keywords: Kata kunci untuk mencari gambar yang relevan dengan artikel (2-5 kata kunci)
- Contoh: "technology coding", "web design workspace", "business meeting", "creative art"
- Pilih kata kunci yang menggambarkan visual yang cocok untuk thumbnail artikel
- Sistem akan menggunakan kata kunci ini untuk mencari gambar real dari Unsplash

**Tone & Style:**
- Profesional namun friendly
- Informatif dan edukatif
- Mudah dipahami oleh pembaca umum
- Gunakan contoh nyata jika relevan
- Boleh menggunakan emoticon secukupnya untuk membuat artikel lebih hidup ✨

**Struktur Artikel yang Direkomendasikan:**
1. **Introduction** - Hook pembuka yang menarik perhatian
2. **Main Content** - 3-5 section dengan subheading
3. **Practical Tips/Examples** - Jika relevan
4. **Conclusion** - Ringkasan dan call-to-action

**Content Angle Guidelines (WAJIB DIKUTI):**
Kamu akan diberikan "Content Angle" yang menentukan sudut pandang dan format artikel. PATUHI dengan ketat:

| Content Angle | Format yang Harus Dihasilkan |
|---------------|------------------------------|
| tutorial | Step-by-step guide dengan numbered steps, contoh kode/praktik, hasil akhir |
| tips_tricks | List pendek 5-10 tips actionable, langsung bisa diterapkan |
| best_practices | Rekomendasi pro-level, pattern yang terbukti, anti-patterns |
| common_mistakes | Kesalahan umum + solusinya, "jangan lakukan ini" format |
| comparison | Perbandingan A vs B, tabel perbandingan, kapan pakai yang mana |
| deep_dive | Analisis mendalam konsep, cara kerja internal, advanced use cases |
| beginner_guide | Penjelasan dari nol, analogy sederhana, no prior knowledge assumed |
| trends | Apa yang baru/upcoming, prediksi, stay relevant |
| case_study | Cerita nyata implementasi, problem → solution → result |
| opinion | Opini pribadi, kontroversial tapi respectful, take a stance |
| tool_review | Review tool/library, pros/cons, verdict, alternatif |
| cheatsheet | Quick reference, checklist, ringkasan kompak |

**Target Audience Guidelines:**
- beginner: Jelaskan fundamental, hindari jargon, banyak analogy
- intermediate: Asumsi basic knowledge, fokus practical implementation
- advanced: Deep technical details, edge cases, optimization
- mixed: Layered explanation, dari basic ke advanced

**HISTORY AWARENESS (KRITIS):**
Kamu akan diberikan daftar judul artikel yang SUDAH PERNAH DITULIS sebelumnya. PENTING:
- JANGAN pernah menulis topik yang sama atau sangat mirip
- JANGAN gunakan struktur judul yang sama (misal: semua "Mengenal..." atau "Panduan...")
- Jika sudah ada "Mengenal Laravel", buatlah yang berbeda seperti "Laravel untuk Pemula" atau "Tips Laravel Produktivitas"
- Variasikan sudut pandang: tutorial → tips → best practices → common mistakes

**Penting:**
- Pastikan JSON valid dan bisa di-parse
- Jangan gunakan markdown code blocks dalam content HTML
- Escape quotes dalam JSON dengan benar
- estimated_read_time dalam menit (integer)
- Content angle HARUS mempengaruhi struktur dan tone artikel
INSTRUCTIONS;
    }

    /**
     * Generate a blog article based on topic and content prompts.
     *
     * @param  string  $topicPrompt  The topic or theme for the article
     * @param  string  $contentPrompt  Additional content guidance
     * @param  string  $category  The blog category
     * @param  string  $contentAngle  The content angle/format (tutorial, tips_tricks, etc.)
     * @param  string  $targetAudience  Target audience level (beginner, intermediate, advanced, mixed)
     * @param  array<int, string>  $history  List of previously written article titles
     * @return array<string, mixed>
     */
    public function generateArticle(
        string $topicPrompt,
        string $contentPrompt,
        string $category,
        string $contentAngle = 'tutorial',
        string $targetAudience = 'mixed',
        array $history = []
    ): array {
        $prompt = $this->buildPrompt($topicPrompt, $contentPrompt, $category, $contentAngle, $targetAudience, $history);

        $response = $this->prompt($prompt);

        return $this->parseResponse((string) $response);
    }

    /**
     * Build the prompt for article generation.
     *
     * @param  array<int, string>  $history
     */
    private function buildPrompt(
        string $topicPrompt,
        string $contentPrompt,
        string $category,
        string $contentAngle,
        string $targetAudience,
        array $history
    ): string {
        $angleLabel = $this->getAngleLabel($contentAngle);
        $audienceLabel = $this->getAudienceLabel($targetAudience);
        $historyText = $this->formatHistory($history);

        return <<<PROMPT
Silakan tulis artikel blog dengan spesifikasi berikut:

**Topik/Tema:**
{$topicPrompt}

**Panduan Konten Tambahan:**
{$contentPrompt}

**Kategori:** {$category}

**Content Angle (WAJIB):** {$contentAngle} - {$angleLabel}
**Target Audience (WAJIB):** {$targetAudience} - {$audienceLabel}

{$historyText}

**Instruksi Kritis:**
1. PATUHI Content Angle dengan ketat - format artikel HARUS sesuai tabel guidelines
2. Sesuaikan tingkat kesulitan dengan Target Audience ({$targetAudience})
3. JANGAN menulis topik yang sama dengan artikel yang sudah ada di history di atas
4. Variasikan judul - hindari pola yang sama (jangan semua dimulai dengan "Mengenal...")
5. Pastikan artikel relevan dengan kategori {$category}
6. Gunakan format JSON sesuai instruksi sistem
7. Pastikan konten original dan berkualitas tinggi

Silakan generate artikel yang BERBEDA dan UNIK sekarang.
PROMPT;
    }

    /**
     * Get human-readable label for content angle.
     */
    private function getAngleLabel(string $angle): string
    {
        $labels = [
            'tutorial' => 'Tutorial Step-by-Step',
            'tips_tricks' => 'Tips & Tricks',
            'best_practices' => 'Best Practices',
            'common_mistakes' => 'Common Mistakes to Avoid',
            'comparison' => 'Comparison / VS',
            'deep_dive' => 'Deep Dive / Advanced',
            'beginner_guide' => 'Beginner\'s Guide',
            'trends' => 'Trends & Updates',
            'case_study' => 'Case Study / Real Example',
            'opinion' => 'Opinion / Editorial',
            'tool_review' => 'Tool / Library Review',
            'cheatsheet' => 'Cheatsheet / Quick Reference',
        ];

        return $labels[$angle] ?? 'General Article';
    }

    /**
     * Get human-readable label for target audience.
     */
    private function getAudienceLabel(string $audience): string
    {
        $labels = [
            'beginner' => 'Pemula - butuh penjelasan fundamental',
            'intermediate' => 'Intermediate - fokus praktikal',
            'advanced' => 'Advanced - detail teknis mendalam',
            'beginner_to_intermediate' => 'Beginner ke Intermediate',
            'intermediate_to_advanced' => 'Intermediate ke Advanced',
            'mixed' => 'Semua level - layered explanation',
        ];

        return $labels[$audience] ?? 'Mixed audience';
    }

    /**
     * Format history of previously written articles.
     *
     * @param  array<int, string>  $history
     */
    private function formatHistory(array $history): string
    {
        if (empty($history)) {
            return '**History Artikel:** Belum ada artikel sebelumnya pada topik ini.';
        }

        $historyList = implode("\n- ", $history);

        return <<<HISTORY
**History Artikel (JANGAN TULIS TOPIK YANG SAMA):**
Artikel berikut sudah pernah ditulis sebelumnya. BUATLAH yang BERBEDA:
- {$historyList}

**PENTING:** Jangan duplikasi topik di atas. Buat sudut pandang yang benar-benar berbeda.
HISTORY;
    }

    /**
     * Parse the AI response into structured data.
     *
     * @return array<string, mixed>
     */
    private function parseResponse(string $response): array
    {
        // Try to extract JSON from the response
        $json = $this->extractJson($response);

        if (! $json) {
            throw new \RuntimeException('Failed to parse AI response: '.$response);
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON in AI response: '.json_last_error_msg());
        }

        // Validate required fields
        $requiredFields = ['title', 'excerpt', 'content', 'meta_title', 'meta_description'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \RuntimeException("Missing required field: {$field}");
            }
        }

        // Set default for estimated_read_time if not provided
        if (empty($data['estimated_read_time'])) {
            $data['estimated_read_time'] = $this->calculateReadTime($data['content']);
        }

        return $data;
    }

    /**
     * Extract JSON from a text response.
     */
    private function extractJson(string $text): ?string
    {
        // Try to find JSON between code blocks
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $matches)) {
            return trim($matches[1]);
        }

        // Try to find JSON between curly braces
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            return $matches[0];
        }

        // If the text itself looks like JSON
        $trimmed = trim($text);
        if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
            return $trimmed;
        }

        return null;
    }

    /**
     * Calculate estimated read time in minutes.
     */
    private function calculateReadTime(string $content): int
    {
        // Strip HTML tags
        $text = strip_tags($content);
        // Count words (approximate for Indonesian)
        $wordCount = str_word_count($text);

        // Average reading speed: 200 words per minute
        return max(1, (int) ceil($wordCount / 200));
    }
}
