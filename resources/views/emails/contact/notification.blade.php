<x-mail::message>
# New Contact Message

You have received a new contact message from your website.

<x-mail::panel>
**Name:** {{ $contact->name }}<br>
**Email:** {{ $contact->email }}<br>
@if ($contact->phone)
    **Phone:** {{ $contact->phone }}<br>
@endif
**Subject:** {{ $contact->subject }}<br>
**Date:** {{ $contact->created_at->format('F j, Y g:i A') }}
</x-mail::panel>

<x-mail::panel>
**Message:**<br><br>
{{ $contact->message }}
</x-mail::panel>

<x-mail::button :url="$adminUrl">
View in Admin
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
