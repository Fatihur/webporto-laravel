<x-mail::message>
# {{ $subject }}

{!! $content !!}

@if ($unsubscribeUrl)
<x-mail::footer>
    <a href="{{ $unsubscribeUrl }}">{{ __('Unsubscribe from our newsletter') }}</a>
</x-mail::footer>
@endif
</x-mail::message>
