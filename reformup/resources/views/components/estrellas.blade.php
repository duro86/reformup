@php
    $full  = floor($valor);
    $half  = ($valor - $full) >= 0.5;
    $empty = 5 - $full - ($half ? 1 : 0);
@endphp

<span class="text-warning">
    @for ($i = 0; $i < $full; $i++)
        <i class="bi bi-star-fill"></i>
    @endfor

    @if ($half)
        <i class="bi bi-star-half"></i>
    @endif

    @for ($i = 0; $i < $empty; $i++)
        <i class="bi bi-star"></i>
    @endfor
</span>

<span class="fw-semibold ms-1">
    {{ number_format($valor, 1) }}
</span>
