@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Empleados</h1>
    <a href="{{ route('admin.empleados.create') }}" class="btn btn-primary mb-3">Nuevo Empleado</a>
    
    <div class="card mb-4">
        <div class="card-body p-2">
            <form action="{{ route('admin.empleados') }}" method="GET" class="form-inline">
                <div class="input-group w-100">
                    <input type="text" name="search" class="form-control" 
                        placeholder="Buscar por nombre, apellido o documento..." 
                        value="{{ request('search') }}"
                        aria-label="Buscar empleados">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        @if(request('search'))
                        <a href="{{ route('admin.empleados') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Documento</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $empleado)
            <tr>
                <td>{{ $empleado->IDEmp }}</td>
                <td>{{ $empleado->nomEmp }}</td>
                <td>{{ $empleado->apelEmp }}</td>
                <td>{{ $empleado->docIdenEmp }}</td>
                <td>{{ $empleado->user->usuario ?? 'N/A' }}</td>
                <td>{{ $empleado->user->rolUsu ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.empleados.edit', $empleado->IDEmp) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('admin.empleados.destroy', $empleado->IDEmp) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection