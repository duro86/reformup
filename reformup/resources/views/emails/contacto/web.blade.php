@component('mail::message')

# Nuevo mensaje desde el formulario de contacto

**Nombre:** {{ $nombre }}  
**Email de contacto:** {{ $email }}  
**Asunto indicado:** {{ $asuntoUsuario }}

---

{!! $mensaje !!}

---

Este mensaje se ha enviado desde el formulario de contacto de **ReformUp**.

@endcomponent
