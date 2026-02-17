<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider('groq')]
#[MaxSteps(10)]
#[MaxTokens(4000)]
#[Temperature(0.8)]
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

**Penting:**
- Pastikan JSON valid dan bisa di-parse
- Jangan gunakan markdown code blocks dalam content HTML
- Escape quotes dalam JSON dengan benar
- estimated_read_time dalam menit (integer)
INSTRUCTIONS;
    }

    /**
     * Generate a blog article based on topic and content prompts.
     *
     * @param  string  $topicPrompt  The topic or theme for the article
     * @param  string  $contentPrompt  Additional content guidance
     * @param  string  $category  The blog category
     * @return array<string, mixed>
     */
    public function generateArticle(string $topicPrompt, string $contentPrompt, string $category): array
    {
        $prompt = $this->buildPrompt($topicPrompt, $contentPrompt, $category);

        $response = $this->prompt($prompt);

        return $this->parseResponse((string) $response);
    }

    /**
     * Build the prompt for article generation.
     */
    private function buildPrompt(string $topicPrompt, string $contentPrompt, string $category): string
    {
        return <<<PROMPT
Silakan tulis artikel blog dengan spesifikasi berikut:

**Topik/Tema:**
{$topicPrompt}

**Panduan Konten Tambahan:**
{$contentPrompt}

**Kategori:** {$category}

**Instruksi Penting:**
1. Pastikan artikel relevan dengan kategori {$category}
2. Gunakan format JSON sesuai instruksi sistem
3. Pastikan konten original dan berkualitas tinggi
4. Sertakan tips praktis yang bisa langsung diterapkan

Silakan generate artikel sekarang.
PROMPT;
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
