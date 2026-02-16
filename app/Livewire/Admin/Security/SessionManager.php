<?php

namespace App\Livewire\Admin\Security;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.admin')]
class SessionManager extends Component
{
    public array $sessions = [];
    public ?string $currentSessionId = null;

    public function mount(): void
    {
        $this->loadSessions();
    }

    protected function loadSessions(): void
    {
        $this->currentSessionId = Session::getId();
        
        if (config('session.driver') === 'database') {
            $sessions = DB::table(config('session.table', 'sessions'))
                ->where('user_id', auth()->id())
                ->orderBy('last_activity', 'desc')
                ->get();

            $this->sessions = $sessions->map(function ($session) {
                $payload = $this->decodePayload($session->payload);
                
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $this->parseUserAgent($session->user_agent),
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity),
                    'is_current' => $session->id === $this->currentSessionId,
                    'browser' => $this->extractBrowser($session->user_agent),
                    'platform' => $this->extractPlatform($session->user_agent),
                ];
            })->toArray();
        } else {
            // For file driver, only show current session
            $this->sessions = [
                [
                    'id' => $this->currentSessionId,
                    'ip_address' => request()->ip(),
                    'user_agent' => $this->parseUserAgent(request()->userAgent()),
                    'last_activity' => now(),
                    'is_current' => true,
                    'browser' => $this->extractBrowser(request()->userAgent()),
                    'platform' => $this->extractPlatform(request()->userAgent()),
                ],
            ];
        }
    }

    protected function decodePayload(string $payload): array
    {
        try {
            return unserialize(base64_decode($payload)) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function parseUserAgent(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        return $userAgent;
    }

    protected function extractBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Edg') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            return 'Opera';
        }

        return 'Unknown';
    }

    protected function extractPlatform(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        }

        return 'Unknown';
    }

    public function terminateSession(string $sessionId): void
    {
        if ($sessionId === $this->currentSessionId) {
            session()->flash('error', 'Cannot terminate your current session.');
            return;
        }

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('id', $sessionId)
                ->where('user_id', auth()->id())
                ->delete();
        }

        $this->loadSessions();
        session()->flash('success', 'Session terminated successfully.');
    }

    public function terminateAllOtherSessions(): void
    {
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', auth()->id())
                ->where('id', '!=', $this->currentSessionId)
                ->delete();
        }

        $this->loadSessions();
        session()->flash('success', 'All other sessions have been terminated.');
    }

    public function render()
    {
        return view('livewire.admin.security.session-manager');
    }
}
