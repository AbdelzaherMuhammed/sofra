@component('mail::message')
# Introduction

Sofra Reset Password.

@component('mail::button', ['url' => 'https://facebook.com'])
Reset
@endcomponent

<p>Your reset password is <strong>{{$code}}</strong></p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
