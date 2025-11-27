@props([
    'action',          // ruta del form
    'placeholder' => 'Buscar...', 
    'param' => 'q',    // nombre del campo
])

<form method="GET" action="{{ $action }}" class="row g-2 mb-3">
    <div class="col-12 col-md-6 col-lg-4">
        <input
            type="text"
            name="{{ $param }}"
            value="{{ request($param) }}"
            class="form-control form-control-sm"
            placeholder="{{ $placeholder }}"
        >
    </div>

    <div class="col-6 col-md-3 col-lg-2 d-grid">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-search"></i> Buscar
        </button>
    </div>

    <div class="col-6 col-md-3 col-lg-2 d-grid">
        @if (request($param))
            <a href="{{ $action }}" class="btn btn-sm btn-outline-secondary">
                Limpiar
            </a>
        @endif
    </div>
</form>
