<?php

namespace App\Services;

use App\Models\UserContext;
use Illuminate\Support\Facades\Session;

class UserContextService
{
    private string $sessionId;

    public function __construct()
    {
        $this->sessionId = Session::getId();
    }

    /**
     * Get current session ID
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Save context for current session only
     */
    public function save(string $type, string $value, bool $isSensitive = false, ?int $ttlHours = null): UserContext
    {
        return UserContext::updateOrCreate(
            [
                'session_id' => $this->sessionId,
                'context_type' => $type,
            ],
            [
                'user_identifier' => $this->getUserIdentifier(),
                'context_value' => $value,
                'is_sensitive' => $isSensitive,
                'expires_at' => $ttlHours ? now()->addHours($ttlHours) : now()->addHours(24),
            ]
        );
    }

    /**
     * Get context value for current session
     */
    public function get(string $type, ?string $default = null): ?string
    {
        $context = UserContext::forSession($this->sessionId)
            ->active()
            ->ofType($type)
            ->first();

        return $context?->context_value ?? $default;
    }

    /**
     * Get all contexts for current session
     */
    public function getAll(): array
    {
        return UserContext::forSession($this->sessionId)
            ->active()
            ->get()
            ->mapWithKeys(fn ($ctx) => [$ctx->context_type => $ctx->context_value])
            ->toArray();
    }

    /**
     * Check if context exists for current session
     */
    public function has(string $type): bool
    {
        return UserContext::forSession($this->sessionId)
            ->active()
            ->ofType($type)
            ->exists();
    }

    /**
     * Delete specific context for current session
     */
    public function forget(string $type): void
    {
        UserContext::forSession($this->sessionId)
            ->ofType($type)
            ->delete();
    }

    /**
     * Clear ALL contexts for current session
     */
    public function clearAll(): void
    {
        UserContext::forSession($this->sessionId)->delete();
    }

    /**
     * Get formatted context for AI prompt
     */
    public function getForAiPrompt(): string
    {
        return UserContext::getFormattedContextForAi($this->sessionId);
    }

    /**
     * Get user identifier (session or auth)
     */
    private function getUserIdentifier(): string
    {
        if (auth()->check()) {
            return 'user:'.auth()->id();
        }

        return 'guest:'.$this->sessionId;
    }

    /**
     * Extract and save entities from AI response
     */
    public function extractFromMessage(string $message): void
    {
        // Extract name (patterns like "nama saya John", "saya John", "aku John")
        $namePatterns = [
            '/nama saya ([A-Za-z]+)(?:\s+dari|\s+ke|\.|,|!|$|\s+\w)/i',
            '/saya ([A-Za-z]{2,20})(?:\s+dari|\s+ke|\.|,|!|$)/i',
            '/aku ([A-Za-z]{2,20})(?:\s+dari|\s+ke|\.|,|!|$)/i',
            '/call me ([A-Za-z\s]+)(?:,|\.|\!|$)/i',
            '/my name is ([A-Za-z\s]+)(?:,|\.|\!|$)/i',
        ];

        foreach ($namePatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $name = trim($matches[1]);
                if (strlen($name) > 1 && strlen($name) < 50) {
                    $this->save('name', $name);
                    break;
                }
            }
        }

        // Extract company (patterns like "dari PT ABC", "company saya XYZ")
        $companyPatterns = [
            '/(?:dari|from) (PT\s+[A-Za-z0-9\s\.]+)(?:,|\.|\!|$)/i',
            '/(?:dari|from) (CV\s+[A-Za-z0-9\s\.]+)(?:,|\.|\!|$)/i',
            '/(?:dari|from) ([A-Za-z0-9\s]+(?:Company|Corp|Inc|Ltd))(?:,|\.|\!|$)/i',
            '/company saya ([A-Za-z0-9\s\.]+)(?:,|\.|\!|$)/i',
        ];

        foreach ($companyPatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $company = trim($matches[1]);
                if (strlen($company) > 2 && strlen($company) < 100) {
                    $this->save('company', $company);
                    break;
                }
            }
        }

        // Extract budget
        $budgetPatterns = [
            '/budget(?:nya)?\s+(?:sekitar|around|kira[\-]?kira)?\s*[:]?\s*Rp?\.?\s*([\d\.]+\s*(?:jt|juta|m|million))/i',
            '/(?:harga|biaya|budget)\s+(?:sekitar|around)?\s*[:]?\s*Rp?\.?\s*([\d\.]+\s*(?:jt|juta))/i',
            '/([\d\.]+)\s*(?:jt|juta)\s*(?:untuk|for)/i',
        ];

        foreach ($budgetPatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $budget = trim($matches[1]).' juta';
                $this->save('budget', $budget);
                break;
            }
        }

        // Extract project type
        $projectTypes = [
            'e-commerce' => '/\b(e[-]?commerce|toko online|online shop|marketplace)\b/i',
            'company-profile' => '/\b(company profile|profil perusahaan|web perusahaan)\b/i',
            'web-app' => '/\b(web app|web application|sistem|application)\b/i',
            'mobile-app' => '/\b(mobile app|android|ios|flutter|react native)\b/i',
            'landing-page' => '/\b(landing page|single page)\b/i',
        ];

        foreach ($projectTypes as $type => $pattern) {
            if (preg_match($pattern, $message)) {
                $this->save('project_type', $type);
                break;
            }
        }
    }
}
