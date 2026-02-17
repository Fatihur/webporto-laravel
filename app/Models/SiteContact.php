<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'email_label',
        'whatsapp',
        'whatsapp_label',
        'instagram',
        'instagram_label',
        'linkedin',
        'linkedin_label',
        'github',
        'github_label',
        'twitter',
        'twitter_label',
        'facebook',
        'facebook_label',
        'youtube',
        'youtube_label',
        'tiktok',
        'tiktok_label',
        'address',
        'maps_url',
        'working_hours',
        'phone',
        'phone_label',
    ];

    /**
     * Get the first/only record (singleton pattern)
     */
    public static function getSettings(): ?self
    {
        return static::first();
    }

    /**
     * Get or create default settings
     */
    public static function getOrCreateDefault(): self
    {
        $settings = static::first();

        if (! $settings) {
            $settings = static::create([
                'email' => config('mail.from.address', 'fatihur17@gmail.com'),
                'email_label' => 'Email',
                'whatsapp_label' => 'WhatsApp',
                'instagram_label' => 'Instagram',
                'linkedin_label' => 'LinkedIn',
                'github_label' => 'GitHub',
                'twitter_label' => 'Twitter',
                'facebook_label' => 'Facebook',
                'youtube_label' => 'YouTube',
                'tiktok_label' => 'TikTok',
                'phone_label' => 'Phone',
            ]);
        }

        return $settings;
    }

    /**
     * Get active social media links
     */
    public function getActiveSocialLinks(): array
    {
        $links = [];

        $socials = [
            'instagram' => ['url' => $this->instagram, 'label' => $this->instagram_label],
            'linkedin' => ['url' => $this->linkedin, 'label' => $this->linkedin_label],
            'github' => ['url' => $this->github, 'label' => $this->github_label],
            'twitter' => ['url' => $this->twitter, 'label' => $this->twitter_label],
            'facebook' => ['url' => $this->facebook, 'label' => $this->facebook_label],
            'youtube' => ['url' => $this->youtube, 'label' => $this->youtube_label],
            'tiktok' => ['url' => $this->tiktok, 'label' => $this->tiktok_label],
        ];

        foreach ($socials as $key => $social) {
            if (! empty($social['url'])) {
                $links[$key] = $social;
            }
        }

        return $links;
    }

    /**
     * Get WhatsApp URL with proper format
     */
    public function getWhatsappUrl(): ?string
    {
        if (empty($this->whatsapp)) {
            return null;
        }

        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);

        return "https://wa.me/{$number}";
    }
}
