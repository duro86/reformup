@component('mail::message')

# Nuevo profesional registrado

Hola administrador,

El usuario **{{ $user->nombre }} ({{ $user->email }})** acaba de registrar la empresa:

**{{ $perfil->empresa }}**

Está pendiente de revisión y activación.

@component('mail::button', ['url' => route('admin.profesionales')])
Revisar profesionales
@endcomponent

Gracias,<br>
{{ config('app.name') }}

@endcomponent
