<?php

namespace App\Exports;

use App\Models\Trabajo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TrabajosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $estado      = $this->request->query('estado');
        $q           = $this->request->query('q');
        $fechaDesde  = $this->request->query('fecha_desde');
        $fechaHasta  = $this->request->query('fecha_hasta');

        $query = Trabajo::with([
            'presupuesto.solicitud.cliente',
            'presupuesto.solicitud.profesional',
        ]);

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($q) {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                $sub
                    ->whereHas('presupuesto.solicitud', function ($q2) use ($like) {
                        $q2->where('titulo', 'like', $like)
                            ->orWhere('ciudad', 'like', $like)
                            ->orWhere('provincia', 'like', $like);
                    })
                    ->orWhereHas('presupuesto.solicitud.cliente', function ($q2) use ($like) {
                        $q2->where('nombre', 'like', $like)
                            ->orWhere('apellidos', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })
                    ->orWhereHas('presupuesto.solicitud.profesional', function ($q2) use ($like) {
                        $q2->where('empresa', 'like', $like)
                            ->orWhere('ciudad', 'like', $like)
                            ->orWhere('provincia', 'like', $like);
                    });
            });
        }

        if ($fechaDesde) {
            $query->whereDate('fecha_ini', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('fecha_ini', '<=', $fechaHasta);
        }

        return $query
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID trabajo',
            'Solicitud',
            'Cliente',
            'Email cliente',
            'Profesional',
            'Email profesional',
            'Ciudad',
            'Provincia',
            'Fecha inicio',
            'Fecha fin',
            'Estado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Ajuste de texto en encabezados (A1 hasta K1 = 11 columnas)
        $sheet->getStyle('A1:K1')->getAlignment()->setWrapText(true);

        return [
            1 => [
                'font' => ['bold' => true],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN, 
                    ],
                ],
            ],
        ];
    }

    public function map($trabajo): array
    {
        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $pro         = $presupuesto?->profesional ?? $solicitud?->profesional;

        return [
            $trabajo->id,
            $solicitud?->titulo ?? ('Solicitud #' . ($solicitud->id ?? '')),
            $cliente
                ? trim(($cliente->nombre ?? $cliente->name) . ' ' . ($cliente->apellidos ?? ''))
                : 'Sin cliente',
            $cliente?->email ?? '',
            $pro?->empresa ?? 'Sin profesional',
            $pro?->email_empresa ?? '',
            $solicitud?->ciudad ?? 'No indicada',
            $solicitud?->provincia ?? '',
            optional($trabajo->fecha_ini)->format('d/m/Y H:i') ?? '',
            optional($trabajo->fecha_fin)->format('d/m/Y H:i') ?? '',
            ucfirst(str_replace('_', ' ', $trabajo->estado)),
        ];
    }
}
