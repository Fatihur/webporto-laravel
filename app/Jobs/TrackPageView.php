<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stevebauman\Location\Facades\Location;

class TrackPageView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Model $viewable,
        public string $ipAddress,
        public ?string $userAgent = null,
        public ?string $referrer = null,
        public ?string $sessionId = null
    ) {}

    public function handle(): void
    {
        try {
            $location = Location::get($this->ipAddress);

            $this->viewable->pageViews()->create([
                'session_id' => $this->sessionId ?? uniqid('job_', true),
                'ip_address' => $this->ipAddress,
                'user_agent' => $this->userAgent,
                'referrer' => $this->referrer,
                'country' => $location?->countryCode,
                'city' => $location?->cityName,
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan throw (jangan ganggu user experience)
            \Log::error('TrackPageView failed: ' . $e->getMessage(), [
                'ip' => $this->ipAddress,
                'viewable_type' => get_class($this->viewable),
                'viewable_id' => $this->viewable->id,
            ]);
        }
    }
}
