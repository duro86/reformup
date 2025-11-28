<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait FiltroRangoFechas
{
    protected function aplicarFiltroRangoFechas(
        Builder $consulta,
        Request $request,
        string $columna = 'created_at',
        string $campoDesde = 'fecha_desde',
        string $campoHasta = 'fecha_hasta'
    ): Builder {
        $fechaDesde = $request->input($campoDesde);
        $fechaHasta = $request->input($campoHasta);

        if ($fechaDesde) {
            $consulta->whereDate($columna, '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $consulta->whereDate($columna, '<=', $fechaHasta);
        }

        return $consulta;
    }
}
