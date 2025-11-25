<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de usuarios(paginado) - ReformUp</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .small-text {
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>

    <h1>Listado de usuarios</h1>
    <p class="small-text">
        Generado el {{ now()->format('d/m/Y H:i') }}
        @isset($page)
        — Página {{ $page }}
        @endisset
    </p>

    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Rol(es)</th>
                <th>Fecha creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->id }}</td>
                <td>{{ $usuario->nombre }} {{ $usuario->apellidos }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ $usuario->telefono ?: '—' }}</td>
                <td>{{ $usuario->getRoleNames()->implode(', ') ?: '—' }}</td>
                <td>
                    {{ $usuario->created_at?->format('d/m/Y H:i') ?? '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>