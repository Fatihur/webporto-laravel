<x-mail::message>
# Welcome to the Newsletter!

Hi {{ $subscriber->name ?? 'there' }},

Thank you for subscribing to our newsletter! You'll receive the latest updates, insights, and exclusive content directly in your inbox.

<x-mail::button :url="$unsubscribeUrl">
Unsubscribe
</x-mail::button>

If you didn't subscribe to this newsletter, you can safely ignore this email or click the unsubscribe button above.

Thanks,
{{ config('app.name') }}
</x-mail::message>
