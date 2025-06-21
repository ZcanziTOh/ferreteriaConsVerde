@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Proveedor</h1>
    
    <form action="{{ route('admin.proveedores.update', $proveedor->IDprov) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="razonSocialProv">Razón Social</label>
            <input type="text" class="form-control" id="razonSocialProv" name="razonSocialProv" value="{{ $proveedor->razonSocialProv }}" required>
        </div>
        
        <div class="form-group">
            <label for="rucProv">RUC</label>
            <input type="text" class="form-control" id="rucProv" name="rucProv" value="{{ $proveedor->rucProv }}" maxlength="11" required>
        </div>
        
        <div class="form-group">
            <label for="telProv">Teléfono</label>
            <input type="text" class="form-control" id="telProv" name="telProv" value="{{ $proveedor->telProv }}">
        </div>
        
        <div class="form-group">
            <label for="emailProv">Email</label>
            <input type="email" class="form-control" id="emailProv" name="emailProv" value="{{ $proveedor->emailProv }}">
        </div>
        
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('admin.proveedores') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection