<x-mail::message>
<x-slot:header>
<x-mail::header :url="config('app.url')" style="text-align: center;">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>

<div style="font-size: 16px; line-height: 1.6; color: #333;">
{!! nl2br(e($content)) !!}
</div>

Thanks,
**{{ config('app.name') }}**

---

<x-slot:footer>
<x-mail::footer>
<div style="text-align: center; font-size: 12px; color: #718096;">
<p>You're receiving this because you subscribed to our newsletter.</p>
<p>
<a href="{{ $unsubscribeUrl }}" style="color: #718096; text-decoration: underline;">Unsubscribe</a>
</p>
<p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
</div>
</x-mail::footer>
</x-slot:footer>
</x-mail::message>
