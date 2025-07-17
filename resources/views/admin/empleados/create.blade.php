@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Empleado</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.empleados.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="nomEmp">Nombre</label>
            <input type="text" class="form-control" id="nomEmp" name="nomEmp" required>
        </div>
        
        <div class="form-group">
            <label for="apelEmp">Apellido</label>
            <input type="text" class="form-control" id="apelEmp" name="apelEmp" required>
        </div>
        
        <div class="form-group">
            <label for="docIdenEmp">Documento de Identidad</label>
            <input type="text" class="form-control" id="docIdenEmp" name="docIdenEmp" required>
        </div>
        
        <div class="form-group">
            <label for="telEmp">Teléfono</label>
            <input type="text" class="form-control" id="telEmp" name="telEmp">
        </div>
        
        <div class="form-group">
            <label for="dirEmp">Dirección</label>
            <input type="text" class="form-control" id="dirEmp" name="dirEmp">
        </div>
        
        <div class="form-group">
            <label for="usuario">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        
        <div class="form-group">
            <label for="contraUsu">Contraseña</label>
            <input type="password" class="form-control" id="contraUsu" name="contraUsu" required minlength="8">
        </div>
        
        <div class="form-group">
            <label for="rolUsu">Rol</label>
            <select class="form-control" id="rolUsu" name="rolUsu" required>
                <option value="admin">Administrador</option>
                <option value="vendedor">Vendedor</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('admin.empleados') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection