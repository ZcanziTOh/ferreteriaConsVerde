@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Empleado</h1>
    
    <form action="{{ route('admin.empleados.update', $empleado->IDEmp) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nomEmp">Nombre</label>
            <input type="text" class="form-control" id="nomEmp" name="nomEmp" value="{{ $empleado->nomEmp }}" required>
        </div>
        
        <div class="form-group">
            <label for="apelEmp">Apellido</label>
            <input type="text" class="form-control" id="apelEmp" name="apelEmp" value="{{ $empleado->apelEmp }}" required>
        </div>
        
        <div class="form-group">
            <label for="docIdenEmp">Documento de Identidad</label>
            <input type="text" class="form-control" id="docIdenEmp" name="docIdenEmp" value="{{ $empleado->docIdenEmp }}" required>
        </div>
        
        <div class="form-group">
            <label for="telEmp">Teléfono</label>
            <input type="text" class="form-control" id="telEmp" name="telEmp" value="{{ $empleado->telEmp }}">
        </div>
        
        <div class="form-group">
            <label for="dirEmp">Dirección</label>
            <input type="text" class="form-control" id="dirEmp" name="dirEmp" value="{{ $empleado->dirEmp }}">
        </div>
        
        <div class="form-group">
            <label for="usuario">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="{{ $empleado->user->usuario }}" required>
        </div>
        
        <div class="form-group">
            <label for="contraUsu">Nueva Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" class="form-control" id="contraUsu" name="contraUsu" minlength="8">
        </div>
        
        <div class="form-group">
            <label for="rolUsu">Rol</label>
            <select class="form-control" id="rolUsu" name="rolUsu" required>
                <option value="admin" {{ $empleado->user->rolUsu == 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="vendedor" {{ $empleado->user->rolUsu == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('admin.empleados') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection