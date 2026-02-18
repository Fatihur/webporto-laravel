<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserContext extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_identifier',
        'context_type',
        'context_value',
        'is_sensitive',
        'expires_at',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Scope for specific session only (ISOLASI UTAMA)
     */
    public function scopeForSession(Builder $query, string $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for active (not expired) contexts
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for specific context type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('context_type', $type);
    }

    /**
     * Auto-encrypt sensitive values
     */
    public function setContextValueAttribute($value): void
    {
        if ($this->attributes['is_sensitive'] ?? false) {
            $this->attributes['context_value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['context_value'] = $value;
        }
    }

    /**
     * Auto-decrypt sensitive values
     */
    public function getContextValueAttribute($value): string
    {
        if ($this->is_sensitive) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // Fallback if not encrypted
            }
        }

        return $value;
    }

    /**
     * Get formatted context for AI prompt
     */
    public static function getFormattedContextForAi(string $sessionId): string
    {
        $contexts = self::forSession($sessionId)
            ->active()
            ->orderBy('context_type')
            ->get();

        if ($contexts->isEmpty()) {
            return '';
        }

        $lines = ['=== INFORMASI PENGGUNA (HANYA UNTUK SESSION INI) ==='];
        foreach ($contexts as $ctx) {
            $lines[] = "- {$ctx->context_type}: {$ctx->context_value}";
        }
        $lines[] = '===================================================';

        return implode("\n", $lines);
    }

    /**
     * Clean up expired contexts
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', now())->delete();
    }
}
