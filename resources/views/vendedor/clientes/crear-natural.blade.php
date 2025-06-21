@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nuevo Cliente Natural</h1>
    
    <form action="{{ route('vendedor.clientes.store-natural') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="docIdenClieNat">Documento de Identidad</label>
            <input type="text" class="form-control" id="docIdenClieNat" name="docIdenClieNat" required>
        </div>
        
        <div class="form-group">
            <label for="nomClieNat">Nombres</label>
            <input type="text" class="form-control" id="nomClieNat" name="nomClieNat" required>
        </div>
        
        <div class="form-group">
            <label for="apelClieNat">Apellidos</label>
            <input type="text" class="form-control" id="apelClieNat" name="apelClieNat" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar</button>
        <a href="{{ route('vendedor.clientes.naturales') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection