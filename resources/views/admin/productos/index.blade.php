@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Productos</h1>
    <a href="{{ route('admin.productos.create') }}" class="btn btn-primary mb-3">Nuevo Producto</a>
  
    <div class="card mb-4">
        <div class="card-body p-2">
            <form action="{{ route('admin.productos') }}" method="GET" class="form-inline">
                <div class="input-group w-100">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por nombre de producto..." 
                           value="{{ request('search') }}"
                           aria-label="Buscar productos">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        @if(request('search'))
                        <a href="{{ route('admin.productos') }}" class="btn btn-outline-secondary">
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
                <th>Estado</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
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