@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Crear Nueva Categoría</h1>
        </div>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-body">
            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nomCat">Nombre de la Categoría</label>
                    <input type="text" class="form-control @error('nomCat') is-invalid @enderror" 
                           id="nomCat" name="nomCat" value="{{ old('nomCat') }}" required>
                    @error('nomCat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descCat">Descripción</label>
                    <textarea class="form-control @error('descCat') is-invalid @enderror" 
                              id="descCat" name="descCat" rows="3">{{ old('descCat') }}</textarea>
                    @error('descCat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('admin.categorias') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection