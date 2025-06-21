@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Editar Categoría: {{ $categoria->nomCat }}</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categorias.update', $categoria->IDCat) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nomCat">Nombre de la Categoría</label>
                    <input type="text" class="form-control @error('nomCat') is-invalid @enderror" 
                           id="nomCat" name="nomCat" value="{{ old('nomCat', $categoria->nomCat) }}" required>
                    @error('nomCat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descCat">Descripción</label>
                    <textarea class="form-control @error('descCat') is-invalid @enderror" 
                              id="descCat" name="descCat" rows="3">{{ old('descCat', $categoria->descCat) }}</textarea>
                    @error('descCat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('admin.categorias') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection