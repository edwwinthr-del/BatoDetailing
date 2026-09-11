<x-mail::message>
# New support message

**From:** {{ $data['name'] }} ({{ $data['email'] }})

{{ $data['message'] }}

<x-mail::button :url="'mailto:'.$data['email']">
Reply to {{ $data['name'] }}
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
