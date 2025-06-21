<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('scripts')
    <title>Construye Verde - @yield('title')</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    @if(Request::is('login*'))
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endif
    @if(Request::is('admin*'))
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @endif
    @if(Request::is('vendedor*'))
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('css/vendedor.css') }}" rel="stylesheet">
    @endif
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="inventarioDropdown" role="button" data-bs-toggle="dropdown">
                                    Inventario
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.productos') }}">Productos</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.categorias') }}">Categorías</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.proveedores') }}">Proveedores</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.pedidos') }}">Pedidos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.empleados') }}">Empleados</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.reportes') }}">Reportes</a>
                            </li>
                        @elseif(auth()->user()->isVendedor())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vendedor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vendedor.ventas.index') }}">Ventas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vendedor.cotizaciones.index') }}">Cotizaciones</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="clientesDropdown" role="button" data-bs-toggle="dropdown">
                                    Clientes
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('vendedor.clientes.naturales') }}">Naturales</a></li>
                                    <li><a class="dropdown-item" href="{{ route('vendedor.clientes.juridicos') }}">Jurídicos</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vendedor.devoluciones.index') }}">Devoluciones</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vendedor.cierre-caja') }}">Cierre de Caja</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->empleado->nomEmp ?? auth()->user()->usuario }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Perfil</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
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
        
        @yield('content')
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">Sistema de Gestión Ferretería Construye Verde &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sunat.js') }}"></script>
    @stack('scripts')
</body>
</html>