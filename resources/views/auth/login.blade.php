@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">
                <img src="{{ asset('img/logo.jpg') }}" alt="Construye Verde" class="login-logo">
            </div>
            <p>Gestión de Inventarios y Ventas</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control @error('usuario') is-invalid @enderror" 
                       id="usuario" name="usuario" value="{{ old('usuario') }}" required autofocus>
                @error('usuario')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="contraUsu" class="form-label">Contraseña</label>
                <input type="password" class="form-control @error('contraUsu') is-invalid @enderror" 
                       id="contraUsu" name="contraUsu" required>
                @error('contraUsu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>

            <button type="submit" class="btn btn-success w-100">Iniciar Sesión</button>

            <div class="options-group mt-3">
                <a href="" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="register-link mt-2">
                <span>¿Necesitas una cuenta?</span>
                <a href="#">Solicitar acceso</a>
            </div>
        </form>
    </div>
</div>
@endsection