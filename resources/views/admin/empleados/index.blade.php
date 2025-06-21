@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Empleados</h1>
    <a href="{{ route('admin.empleados.create') }}" class="btn btn-primary mb-3">Nuevo Empleado</a>
    
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