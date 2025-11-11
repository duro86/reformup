@extends('layouts.main')

@section('content')
<div class="d-flex" style="gap: 1rem;"> {{-- gap agrega espacio entre hijos del flex container --}}

  {{-- Sidebar --}}
  <div style="width: 220px;">
    <x-admin_sidebar />
  </div>

  {{-- Contenido principal --}}
  <div class="flex-grow-1">
    <x-user_bienvenido />

    <div class="container p-3">
      <h1 class="text-center">Listado de Usuarios</h1>

      <table class="table table-sm">
        <thead>
          <tr style="font-size: 0.875rem;">
            <th>Avatar</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody style="font-size: 0.875rem;">
          @foreach($usuarios as $usuario)
          <tr>
            <td>
              @if($usuario->avatar)
                <img src="{{ asset('storage/' . $usuario->avatar) }}" alt="avatar" style="width:30px; height:30px; border-radius:50%;">
              @else
                <i class="bi bi-person-circle" style="font-size: 1rem;"></i>
              @endif
            </td>
            <td>{{ $usuario->nombre }}</td>
            <td>{{ $usuario->apellidos }}</td>
            <td>{{ $usuario->email }}</td>
            <td>{{ $usuario->telefono }}</td>
            <td>
              <a href="{{ route('admin.usuarios.ver', $usuario->id) }}" class="btn btn-info btn-sm px-2 py-1">Ver</a>
              <a href="{{ route('admin.usuarios.editar', $usuario->id) }}" class="btn btn-warning btn-sm px-2 py-1">Editar</a>
              <form action="{{ route('admin.usuarios.eliminar', $usuario->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm px-2 py-1" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      {{-- Paginación --}}
      {{ $usuarios->links('pagination::bootstrap-5') }}

    </div>
  </div>
</div>
@endsection


