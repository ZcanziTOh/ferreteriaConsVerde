@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Proveedores</h1>
    <a href="{{ route('admin.proveedores.create') }}" class="btn btn-primary mb-3">Nuevo Proveedor</a>
    
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
                <th>Razón Social</th>
                <th>RUC</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proveedores as $proveedor)
            <tr>
                <td>{{ $proveedor->IDprov }}</td>
                <td>{{ $proveedor->razonSocialProv }}</td>
                <td>{{ $proveedor->rucProv }}</td>
                <td>{{ $proveedor->telProv }}</td>
                <td>{{ $proveedor->emailProv }}</td>
                <td>
                    <a href="{{ route('admin.proveedores.edit', $proveedor->IDprov) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('admin.proveedores.destroy', $proveedor->IDprov) }}" method="POST" style="display:inline;">
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