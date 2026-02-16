<?php

namespace App\Livewire\Admin\Analytics;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ChatAnalytics extends Component
{
    public string $period = '7days'; // 24hours, 7days, 30days, all

    protected $queryString = ['period'];

    public function getStatsProperty()
    {
        $startDate = match($this->period) {
            '24hours' => now()->subDay(),
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            'all' => null,
            default => now()->subDays(7),
        };

        $query = ChatSession::query();
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $totalSessions = $query->clone()->count();
        $activeSessions = $query->clone()->where('is_active', true)->count();
        $uniqueUsers = $query->clone()->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        $messageQuery = ChatMessage::query();
        if ($startDate) {
            $messageQuery->where('created_at', '>=', $startDate);
        }

        $totalMessages = $messageQuery->clone()->count();
        $userMessages = $messageQuery->clone()->where('role', 'user')->count();
        $assistantMessages = $messageQuery->clone()->where('role', 'assistant')->count();

        // Average messages per session
        $avgMessages = $totalSessions > 0 ? round($totalMessages / $totalSessions, 2) : 0;

        return compact(
            'totalSessions',
            'activeSessions',
            'uniqueUsers',
            'totalMessages',
            'userMessages',
            'assistantMessages',
            'avgMessages'
        );
    }

    public function getDailyStatsProperty()
    {
        $days = match($this->period) {
            '24hours' => 1,
            '7days' => 7,
            '30days' => 30,
            'all' => 30,
            default => 7,
        };

        $startDate = now()->subDays($days);

        return ChatMessage::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(CASE WHEN role = "user" THEN 1 ELSE 0 END) as user_count'),
            DB::raw('SUM(CASE WHEN role = "assistant" THEN 1 ELSE 0 END) as assistant_count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getRecentConversationsProperty()
    {
        return ChatSession::withCount('messages')
            ->with(['user', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getTopQuestionsProperty()
    {
        // Get most common keywords from user messages
        $messages = ChatMessage::user()
            ->where('created_at', '>=', now()->subDays(7))
            ->pluck('content');

        $keywords = [];
        foreach ($messages as $message) {
            $words = str_word_count(strtolower($message), 1);
            foreach ($words as $word) {
                if (strlen($word) > 3) {
                    $keywords[$word] = ($keywords[$word] ?? 0) + 1;
                }
            }
        }

        arsort($keywords);
        return array_slice($keywords, 0, 10, true);
    }

    public function toggleSessionStatus($sessionId)
    {
        $session = ChatSession::find($sessionId);
        if ($session) {
            $session->update(['is_active' => !$session->is_active]);
        }
    }

    public function deleteSession($sessionId)
    {
        $session = ChatSession::find($sessionId);
        if ($session) {
            $session->delete();
        }
    }

    public function render()
    {
        return view('livewire.admin.analytics.chat-analytics');
    }
}
