@php
    use Illuminate\Support\Facades\Auth;

    $nombreCampoDesde = $nombreCampoDesde ?? 'fecha_desde';
    $nombreCampoHasta = $nombreCampoHasta ?? 'fecha_hasta';

    $esVistaProfesional = request()->routeIs('profesional.*');

    $bgFiltro = $esVistaProfesional ? 'bg-pro-primary' : '';
@endphp

<div class="col-6 col-md-3 col-lg-2">
    <input type="date" name="{{ $nombreCampoDesde }}" value="{{ request($nombreCampoDesde) }}"
        class="form-control form-control-sm {{ $bgFiltro }}" placeholder="Desde">
</div>

<div class="col-6 col-md-3 col-lg-2">
    <input type="date" name="{{ $nombreCampoHasta }}" value="{{ request($nombreCampoHasta) }}"
        class="form-control form-control-sm {{ $bgFiltro }}" placeholder="Hasta">
</div>
