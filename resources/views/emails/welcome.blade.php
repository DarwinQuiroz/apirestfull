@component('mail::message')
# Hola {{ $user->name }}

Gracias por crear una cuenta por favor verificala usando el siguiente botón:

@component('mail::button', ['url' => route('verify', ['token' => $user->verification_token])])
    Confirmar mi cuenta
@endcomponent

Gracias, <br>
{{ config('app.name') }}
@endcomponent
