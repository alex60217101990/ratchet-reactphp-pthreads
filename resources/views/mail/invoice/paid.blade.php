@component('mail::message')
# Introduction

You was send: {{ $send_data }}.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
