<x-mail::message>
{!! nl2br(e($content)) !!}

Thanks,
{{ config('app.name') }}

<x-mail::button :url="$unsubscribeUrl" color="gray">
Unsubscribe
</x-mail::button>
</x-mail::message>
