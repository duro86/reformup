@php
    $nombreCampoDesde = $nombreCampoDesde ?? 'fecha_desde';
    $nombreCampoHasta = $nombreCampoHasta ?? 'fecha_hasta';
@endphp

<div class="col-6 col-md-3 col-lg-2">
    <input
        type="date"
        name="{{ $nombreCampoDesde }}"
        value="{{ request($nombreCampoDesde) }}"
        class="form-control form-control-sm"
        placeholder="Desde">
</div>

<div class="col-6 col-md-3 col-lg-2">
    <input
        type="date"
        name="{{ $nombreCampoHasta }}"
        value="{{ request($nombreCampoHasta) }}"
        class="form-control form-control-sm"
        placeholder="Hasta">
</div>
