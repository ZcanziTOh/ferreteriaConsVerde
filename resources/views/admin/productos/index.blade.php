@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Productos</h1>
    <a href="{{ route('admin.productos.create') }}" class="btn btn-primary mb-3">Nuevo Producto</a>
    
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
                <th>Estado</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->IDProd }}</td>
                <td>{{ $producto->nomProd }}</td>
                <td>{{ $producto->estProd == 'activo' ? 'Activo' : 'Inactivo' }}</td>
                <td>S/ {{ number_format($producto->precUniProd, 2) }}</td>
                <td>{{ $producto->stockProd }}</td>
                <td>{{ $producto->categoria->nomCat }}</td>
                <td>{{ $producto->proveedor->razonSocialProv }}</td>
                <td>
                    <a href="{{ route('admin.productos.edit', $producto->IDProd) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('admin.productos.destroy', $producto->IDProd) }}" method="POST" style="display:inline;">
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