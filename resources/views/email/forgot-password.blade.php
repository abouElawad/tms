<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="url('api/password_resets')">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
