<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de profesionales - ReformUp</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .small-text {
            font-size: 9px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px 3px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>

    <h1>Listado de profesionales</h1>

    <p class="small-text">
        Generado el {{ now()->format('d/m/Y H:i') }}
        @isset($page)
            — Página {{ $page }}
        @endisset
    </p>

    @if (!empty($busqueda))
        <p class="small-text">
            Búsqueda aplicada: "{{ $busqueda }}"
        </p>
    @endif

    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Empresa</th>
                <th>CIF</th>
                <th>Email empresa</th>
                <th>Teléfono empresa</th>
                <th>Usuario asociado</th>
                <th>Rol(es)</th>
                <th>Visible</th>
                <th>Fecha creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($profesionales as $perfil)
                @php
                    $user = $perfil->user;
                @endphp
                <tr>
                    <td>{{ $perfil->id }}</td>

                    <td>{{ $perfil->empresa }}</td>
                    <td>{{ $perfil->cif }}</td>
                    <td>{{ $perfil->email_empresa }}</td>
                    <td>{{ $perfil->telefono_empresa ?: '—' }}</td>

                    {{-- Usuario asociado --}}
                    <td>
                        @if ($user)
                            {{ $user->nombre }} {{ $user->apellidos }}<br>
                            <span class="small-text">{{ $user->email }}</span>
                        @else
                            Sin usuario
                        @endif
                    </td>

                    {{-- Roles --}}
                    <td>
                        @if ($user)
                            {{ $user->getRoleNames()->implode(', ') ?: '—' }}
                        @else
                            —
                        @endif
                    </td>

                    {{-- Visible --}}
                    <td>
                        {{ $perfil->visible ? 'Sí' : 'No' }}
                    </td>

                    {{-- Fecha creación --}}
                    <td>
                        {{ $perfil->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
