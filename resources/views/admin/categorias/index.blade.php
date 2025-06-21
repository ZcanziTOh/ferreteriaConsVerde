@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Categorías</h1>
    
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

    <div class="mb-3">
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary mb-3">Nueva Categoria</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categorias as $categoria)
            <tr>
                <td>{{ $categoria->IDCat }}</td>
                <td>{{ $categoria->nomCat }}</td>
                <td>{{ $categoria->descCat }}</td>
                <td>
                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editarCategoriaModal{{ $categoria->IDCat }}">Editar</button>
                    <form action="{{ route('admin.categorias.destroy', $categoria->IDCat) }}" method="POST" style="display:inline;">
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

<!-- Modal Crear Categoría -->
<div class="modal fade" id="crearCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="crearCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearCategoriaModalLabel">Nueva Categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nomCat">Nombre</label>
                        <input type="text" class="form-control" id="nomCat" name="nomCat" required>
                    </div>
                    <div class="form-group">
                        <label for="descCat">Descripción</label>
                        <textarea class="form-control" id="descCat" name="descCat"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales Editar Categoría -->
@foreach($categorias as $categoria)
<div class="modal fade" id="editarCategoriaModal{{ $categoria->IDCat }}" tabindex="-1" role="dialog" aria-labelledby="editarCategoriaModalLabel{{ $categoria->IDCat }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarCategoriaModalLabel{{ $categoria->IDCat }}">Editar Categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.categorias.update', $categoria->IDCat) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nomCat">Nombre</label>
                        <input type="text" class="form-control" id="nomCat" name="nomCat" value="{{ $categoria->nomCat }}" required>
                    </div>
                    <div class="form-group">
                        <label for="descCat">Descripción</label>
                        <textarea class="form-control" id="descCat" name="descCat">{{ $categoria->descCat }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection