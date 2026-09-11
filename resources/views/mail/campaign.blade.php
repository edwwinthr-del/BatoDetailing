<x-mail::message>
Hello {{ $recipientName }},

{{ $body }}

<x-mail::button :url="route('home')">
Visit BatoDetailing
</x-mail::button>

You are receiving this email because you opted in to promotional emails. You can opt out anytime from your profile page.

{{ config('app.name') }}
</x-mail::message>
