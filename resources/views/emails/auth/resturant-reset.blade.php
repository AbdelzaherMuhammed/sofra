@component('mail::message')
# Introduction

Sofra Reset Password.

@component('mail::button', ['url' => 'www.Facebook.com'])
visit us
@endcomponent

<p>Your reset password id <strong>{{$code}}</strong></p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
