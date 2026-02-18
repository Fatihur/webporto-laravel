<?php

namespace App\Console\Commands;

use App\Models\UserContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestAiContextMemory extends Command
{
    protected $signature = 'ai:test-context';

    protected $description = 'Test AI Context Memory isolation and functionality';

    public function handle(): int
    {
        $this->info('🧪 Testing AI Context Memory System...');
        $this->newLine();

        // Test 1: Session Isolation
        $this->testSessionIsolation();

        // Test 2: Context Extraction
        $this->testContextExtraction();

        // Test 3: AI Prompt Formatting
        $this->testAiPromptFormatting();

        // Test 4: Auto-expire
        $this->testAutoExpire();

        $this->newLine();
        $this->info('✅ All tests completed!');
        $this->newLine();
        $this->info('📊 Summary:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Contexts in DB', UserContext::count()],
                ['Active Contexts', UserContext::active()->count()],
                ['Expired Contexts', UserContext::where('expires_at', '<', now())->count()],
            ]
        );

        return self::SUCCESS;
    }

    private function testSessionIsolation(): void
    {
        $this->info('Test 1: Session Isolation');
        $this->line('───────────────────────────────────────');

        // Create User A context directly (simulating different sessions)
        $sessionA = 'test-session-a-' . uniqid();
        DB::table('user_contexts')->insert([
            [
                'session_id' => $sessionA,
                'context_type' => 'name',
                'context_value' => 'John',
                'is_sensitive' => false,
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'session_id' => $sessionA,
                'context_type' => 'company',
                'context_value' => 'PT ABC',
                'is_sensitive' => false,
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create User B context
        $sessionB = 'test-session-b-' . uniqid();
        DB::table('user_contexts')->insert([
            [
                'session_id' => $sessionB,
                'context_type' => 'name',
                'context_value' => 'Sarah',
                'is_sensitive' => false,
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'session_id' => $sessionB,
                'context_type' => 'company',
                'context_value' => 'Startup XYZ',
                'is_sensitive' => false,
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Verify isolation using model scopes
        $contextA = UserContext::forSession($sessionA)->where('context_type', 'name')->first();
        $contextB = UserContext::forSession($sessionB)->where('context_type', 'name')->first();

        if ($contextA && $contextA->context_value === 'John') {
            $this->line('✅ User A context saved: name = John');
        } else {
            $this->error('❌ User A context failed');
        }

        if ($contextB && $contextB->context_value === 'Sarah') {
            $this->line('✅ User B context saved: name = Sarah');
        } else {
            $this->error('❌ User B context failed');
        }

        // Verify they don't mix
        $crossCheck = UserContext::forSession($sessionA)
            ->where('context_value', 'Sarah')
            ->exists();

        if (! $crossCheck) {
            $this->line('✅ No cross-contamination between sessions');
        } else {
            $this->error('❌ Context leaked between sessions!');
        }

        $this->newLine();
    }

    private function testContextExtraction(): void
    {
        $this->info('Test 2: Context Extraction from Messages');
        $this->line('───────────────────────────────────────');

        $testCases = [
            [
                'message' => 'nama saya Budi dari PT Maju Jaya',
                'expected' => [
                    ['type' => 'name', 'value' => 'Budi'],
                    ['type' => 'company', 'value' => 'PT Maju Jaya'],
                ],
            ],
            [
                'message' => 'budget kita sekitar 75 juta',
                'expected' => [
                    ['type' => 'budget', 'value' => '75 juta'],
                ],
            ],
            [
                'message' => 'aku Michael, mau bikin e-commerce',
                'expected' => [
                    ['type' => 'name', 'value' => 'Michael'],
                    ['type' => 'project_type', 'value' => 'e-commerce'],
                ],
            ],
            [
                'message' => 'call me Alice, dari Apple Inc',
                'expected' => [
                    ['type' => 'name', 'value' => 'Alice'],
                    ['type' => 'company', 'value' => 'Apple Inc'],
                ],
            ],
        ];

        foreach ($testCases as $index => $testCase) {
            $sessionId = 'test-extract-' . $index . '-' . uniqid();
            $message = $testCase['message'];
            $expected = $testCase['expected'];

            // Simulate extraction logic
            $this->extractAndSaveContext($message, $sessionId);

            foreach ($expected as $exp) {
                $saved = UserContext::forSession($sessionId)
                    ->where('context_type', $exp['type'])
                    ->where('context_value', $exp['value'])
                    ->exists();

                if ($saved) {
                    $this->line("✅ Extracted '{$exp['type']}' = '{$exp['value']}'");
                } else {
                    $this->error("❌ Failed to extract '{$exp['type']}' = '{$exp['value']}'");
                }
            }
        }

        $this->newLine();
    }

    private function extractAndSaveContext(string $message, string $sessionId): void
    {
        // Extract name - improved patterns
        $namePatterns = [
            '/nama saya ([A-Za-z]+)(?:\s+dari|\s+ke|\.|,|!|$|\s+\w)/i',
            '/saya ([A-Za-z]{2,20})(?:\s+dari|\s+ke|\.|,|!|$)/i',
            '/aku ([A-Za-z]{2,20})(?:\s+dari|\s+ke|\.|,|!|$)/i',
            '/call me ([A-Za-z\s]+)(?:,|\.|!|$)/i',
            '/my name is ([A-Za-z\s]+)(?:,|\.|!|$)/i',
        ];

        foreach ($namePatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $name = trim($matches[1]);
                if (strlen($name) > 1 && strlen($name) < 50) {
                    $this->saveContext($sessionId, 'name', $name);
                    break;
                }
            }
        }

        // Extract company
        $companyPatterns = [
            '/(?:dari|from)\s+(PT\s+[A-Za-z0-9\s\.]+)(?:,|\.|\!|$)/i',
            '/(?:dari|from)\s+(CV\s+[A-Za-z0-9\s\.]+)(?:,|\.|\!|$)/i',
            '/(?:dari|from)\s+([A-Za-z0-9\s]+(?:Company|Corp|Inc|Ltd))(?:,|\.|\!|$)/i',
        ];

        foreach ($companyPatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $company = trim($matches[1]);
                if (strlen($company) > 2 && strlen($company) < 100) {
                    $this->saveContext($sessionId, 'company', $company);
                    break;
                }
            }
        }

        // Extract budget - improved patterns
        $budgetPatterns = [
            '/(?:budget|biaya|harga)(?:nya)?\s+(?:sekitar|around|kira[\-]?kira)?\s*[:]?\s*Rp?\.?\s*([\d\.]+)\s*(?:jt|juta|jutaan)/i',
            '/([\d\.]+)\s*(?:jt|juta|jutaan)(?:\s+(?:untuk|for|rupiah))?/i',
            '/sekitar\s+([\d\.]+)\s*(?:jt|juta|jutaan)/i',
        ];

        foreach ($budgetPatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $budget = trim($matches[1]) . ' juta';
                $this->saveContext($sessionId, 'budget', $budget);
                break;
            }
        }

        // Extract project type
        $projectTypes = [
            'e-commerce' => '/\b(e[-]?commerce|toko online|online shop|marketplace)\b/i',
            'company-profile' => '/\b(company profile|profil perusahaan|web perusahaan)\b/i',
        ];

        foreach ($projectTypes as $type => $pattern) {
            if (preg_match($pattern, $message)) {
                $this->saveContext($sessionId, 'project_type', $type);
                break;
            }
        }
    }

    private function saveContext(string $sessionId, string $type, string $value): void
    {
        DB::table('user_contexts')->insert([
            'session_id' => $sessionId,
            'context_type' => $type,
            'context_value' => $value,
            'is_sensitive' => false,
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function testAiPromptFormatting(): void
    {
        $this->info('Test 3: AI Prompt Formatting');
        $this->line('───────────────────────────────────────');

        // Create test context
        $sessionId = 'test-prompt-' . uniqid();
        DB::table('user_contexts')->insert([
            [
                'session_id' => $sessionId,
                'context_type' => 'name',
                'context_value' => 'Diana',
                'is_sensitive' => false,
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'session_id' => $sessionId,
                'context_type' => 'company',
                'context_value' => 'PT Kreatif',
                'is_sensitive' => false,
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'session_id' => $sessionId,
                'context_type' => 'budget',
                'context_value' => '100 juta',
                'is_sensitive' => false,
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $formatted = UserContext::getFormattedContextForAi($sessionId);

        $this->line('Generated AI Prompt:');
        $this->line('─────────────────────');
        $this->line($formatted);
        $this->line('─────────────────────');

        // Verify format
        $checks = [
            'Diana' => 'Name included',
            'PT Kreatif' => 'Company included',
            '100 juta' => 'Budget included',
            'HANYA UNTUK SESSION INI' => 'Session marker present',
            'INFORMASI PENGGUNA' => 'Header present',
        ];

        foreach ($checks as $needle => $description) {
            if (str_contains($formatted, $needle)) {
                $this->line("✅ {$description}");
            } else {
                $this->error("❌ {$description} missing");
            }
        }

        $this->newLine();
    }

    private function testAutoExpire(): void
    {
        $this->info('Test 4: Auto-Expire Functionality');
        $this->line('───────────────────────────────────────');

        // Create expired context
        $expiredSession = 'test-expired-old-' . uniqid();
        DB::table('user_contexts')->insert([
            'session_id' => $expiredSession,
            'context_type' => 'name',
            'context_value' => 'ExpiredUser',
            'is_sensitive' => false,
            'expires_at' => now()->subDay(), // Expired yesterday
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // Create active context
        $activeSession = 'test-expired-new-' . uniqid();
        DB::table('user_contexts')->insert([
            'session_id' => $activeSession,
            'context_type' => 'name',
            'context_value' => 'ActiveUser',
            'is_sensitive' => false,
            'expires_at' => now()->addDay(), // Expires tomorrow
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->line('Before cleanup:');
        $expiredCount = UserContext::where('expires_at', '<', now())->count();
        $activeCount = UserContext::where('expires_at', '>', now())->count();
        $this->line("- Expired contexts: {$expiredCount}");
        $this->line("- Active contexts: {$activeCount}");

        // Run cleanup
        $deleted = UserContext::cleanupExpired();

        $this->line('After cleanup:');
        $this->line("- Deleted {$deleted} expired contexts");
        $this->line('- Remaining contexts: ' . UserContext::count());

        // Verify active context still exists
        $activeExists = UserContext::where('session_id', $activeSession)->exists();
        if ($activeExists) {
            $this->line('✅ Active context preserved');
        } else {
            $this->error('❌ Active context incorrectly deleted');
        }

        // Verify expired context deleted
        $expiredExists = UserContext::where('session_id', $expiredSession)->exists();
        if (! $expiredExists) {
            $this->line('✅ Expired context cleaned up');
        } else {
            $this->error('❌ Expired context not deleted');
        }

        $this->newLine();
    }
}
