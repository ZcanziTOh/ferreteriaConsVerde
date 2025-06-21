@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Proveedor</h1>
    
    <form action="{{ route('admin.proveedores.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="razonSocialProv">Razón Social</label>
            <input type="text" class="form-control" id="razonSocialProv" name="razonSocialProv" required>
        </div>
        
        <div class="form-group">
            <label for="rucProv">RUC</label>
            <input type="text" class="form-control" id="rucProv" name="rucProv" maxlength="11" required>
        </div>
        
        <div class="form-group">
            <label for="telProv">Teléfono</label>
            <input type="text" class="form-control" id="telProv" name="telProv">
        </div>
        
        <div class="form-group">
            <label for="emailProv">Email</label>
            <input type="email" class="form-control" id="emailProv" name="emailProv">
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('admin.proveedores') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection