@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Producto</h1>
    
    <form action="{{ route('admin.productos.update', $producto->IDProd) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nomProd">Nombre</label>
            <input type="text" class="form-control" id="nomProd" name="nomProd" value="{{ $producto->nomProd }}" required>
        </div>
        
        <div class="form-group">
            <label for="estProd">Estado</label>
            <select class="form-control" id="estProd" name="estProd" required>
                <option value="activo" {{ $producto->estProd == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ $producto->estProd == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="uniMedProd">Unidad de Medida</label>
            <input type="text" class="form-control" id="uniMedProd" name="uniMedProd" value="{{ $producto->uniMedProd }}" required>
        </div>
        
        <!-- Nuevo campo: Precio unitario de compra -->
        <div class="form-group">
            <label for="precUniComProd">Precio Unitario de Compra </label>
            <input type="number" step="0.01" class="form-control" id="precUniComProd" name="precUniComProd" 
                   value="{{ $producto->precUniComProd }}" required>
        </div>
        
        <div class="form-group">
            <label for="precUniProd">Precio Unitario de Venta</label>
            <input type="number" step="0.01" class="form-control" id="precUniProd" name="precUniProd" 
                   value="{{ $producto->precUniProd }}" required>
        </div>
        
        <div class="form-group">
            <label for="cantComProd">Cantidad Comprada</label>
            <input type="number" class="form-control" id="cantComProd" name="cantComProd" required min="1">
        </div>

        <div class="form-group">
            <label for="stockProd">Stock Inicial</label>
            <input type="number" class="form-control" id="stockProd" name="stockProd" required min="1">
            <small class="form-text text-muted">Normalmente igual a la cantidad comprada</small>
        </div>
        
        <div class="form-group">
            <label for="stockMinProd">Stock Mínimo</label>
            <input type="number" class="form-control" id="stockMinProd" name="stockMinProd" value="{{ $producto->stockMinProd }}" required>
        </div>
        
        <div class="form-group">
            <label for="IDCat">Categoría</label>
            <select class="form-control" id="IDCat" name="IDCat" required>
                @foreach($categorias as $categoria)
                <option value="{{ $categoria->IDCat }}" {{ $producto->IDCat == $categoria->IDCat ? 'selected' : '' }}>
                    {{ $categoria->nomCat }}
                </option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('admin.productos') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection