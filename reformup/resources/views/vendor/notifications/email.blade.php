<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
    @if ($level === 'error')
        # {{ __('Vaya, algo ha ido mal...') }}
    @else
        # {{ __('Hola!') }}
    @endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
@php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
@endphp
<x-mail::button :url="$actionUrl" :color="$color">
    {{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
{{-- Saludo por defecto si no defines ->salutation() en la notificación --}}
{{ __('Un saludo,') }}<br>
{{ config('app.name') }}
@endif

{{-- Subcopy (texto pequeño debajo del botón) --}}
@isset($actionText)
    <x-slot:subcopy>
        {{ __('Si tienes problemas para hacer clic en el botón ":actionText", copia y pega esta URL en tu navegador:', ['actionText' => $actionText]) }}
        <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
    </x-slot:subcopy>
@endisset
</x-mail::message>
