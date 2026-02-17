<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\SiteContact;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetSiteContactsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Get contact information including email, WhatsApp, phone, address, working hours, and social media links for the portfolio website.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $type = $request['type'] ?? 'all';

        $contact = SiteContact::first();

        if (! $contact) {
            return 'Contact information is not configured yet.';
        }

        return match ($type) {
            'email' => $this->getEmailInfo($contact),
            'whatsapp' => $this->getWhatsAppInfo($contact),
            'phone' => $this->getPhoneInfo($contact),
            'social' => $this->getSocialInfo($contact),
            'address' => $this->getAddressInfo($contact),
            default => $this->getAllInfo($contact),
        };
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->enum(['email', 'whatsapp', 'phone', 'social', 'address', 'all'])->description('Type of contact information to retrieve. Use "all" for complete contact info, "social" for social media links, or specific type.'),
        ];
    }

    private function getEmailInfo(SiteContact $contact): string
    {
        if (empty($contact->email)) {
            return 'Email address is not configured.';
        }

        return "Email:\n- {$contact->email_label}: {$contact->email}\n- mailto:{$contact->email}";
    }

    private function getWhatsAppInfo(SiteContact $contact): string
    {
        if (empty($contact->whatsapp)) {
            return 'WhatsApp number is not configured.';
        }

        $url = $contact->getWhatsappUrl();

        return "WhatsApp:\n- {$contact->whatsapp_label}: {$contact->whatsapp}\n- Chat URL: {$url}";
    }

    private function getPhoneInfo(SiteContact $contact): string
    {
        if (empty($contact->phone)) {
            return 'Phone number is not configured.';
        }

        return "Phone:\n- {$contact->phone_label}: {$contact->phone}\n- tel:{$contact->phone}";
    }

    private function getSocialInfo(SiteContact $contact): string
    {
        $links = $contact->getActiveSocialLinks();

        if (empty($links)) {
            return 'No social media links are configured.';
        }

        $result = "Social Media Links:\n";
        foreach ($links as $key => $social) {
            $result .= "- {$social['label']}: {$social['url']}\n";
        }

        return trim($result);
    }

    private function getAddressInfo(SiteContact $contact): string
    {
        $parts = [];

        if (! empty($contact->address)) {
            $parts[] = "Address:\n{$contact->address}";
        }

        if (! empty($contact->maps_url)) {
            $parts[] = "Google Maps: {$contact->maps_url}";
        }

        if (! empty($contact->working_hours)) {
            $parts[] = "Working Hours: {$contact->working_hours}";
        }

        if (empty($parts)) {
            return 'Address and working hours are not configured.';
        }

        return implode("\n\n", $parts);
    }

    private function getAllInfo(SiteContact $contact): string
    {
        $parts = [];

        // Email
        if (! empty($contact->email)) {
            $parts[] = "📧 {$contact->email_label}: {$contact->email}";
        }

        // Phone
        if (! empty($contact->phone)) {
            $parts[] = "📞 {$contact->phone_label}: {$contact->phone}";
        }

        // WhatsApp
        if (! empty($contact->whatsapp)) {
            $url = $contact->getWhatsappUrl();
            $parts[] = "💬 {$contact->whatsapp_label}: {$contact->whatsapp}\n   [BUTTON:Chat WhatsApp|{$url}]";
        }

        // Address
        if (! empty($contact->address)) {
            $parts[] = "📍 Address:\n{$contact->address}";
        }

        // Working Hours
        if (! empty($contact->working_hours)) {
            $parts[] = "🕒 Working Hours: {$contact->working_hours}";
        }

        // Social Media
        $socialLinks = $contact->getActiveSocialLinks();
        if (! empty($socialLinks)) {
            $socialPart = '🔗 Social Media:';
            foreach ($socialLinks as $key => $social) {
                $socialPart .= "\n   - {$social['label']}: {$social['url']}";
            }
            $parts[] = $socialPart;
        }

        if (empty($parts)) {
            return 'Contact information is not fully configured yet.';
        }

        return "Contact Information:\n\n".implode("\n\n", $parts);
    }
}
