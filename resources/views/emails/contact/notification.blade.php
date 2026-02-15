<x-mail::message>
# {{ __('New Contact Message') }}

{{ __('You have received a new contact message from your website.') }}

<x-mail::panel>
**{{ __('Name') }}:** {{ $contact->name }}<br>
**{{ __('Email') }}:** {{ $contact->email }}<br>
@if ($contact->phone)
    **{{ __('Phone') }}:** {{ $contact->phone }}<br>
@endif
**{{ __('Subject') }}:** {{ $contact->subject }}<br>
**{{ __('Date') }}:** {{ $contact->created_at->format('F j, Y g:i A') }}
</x-mail::panel>

<x-mail::panel>
**{{ __('Message') }}:**<br><br>
{{ $contact->message }}
</x-mail::panel>

<x-mail::button :url="$adminUrl">
{{ __('View in Admin') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>
