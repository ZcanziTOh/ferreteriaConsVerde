<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Construye Verde - @yield('title')</title>

    {{-- Bootstrap global --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    @if(Request::is('login*'))
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endif

    {{-- Estilos globales modernizados --}}
    <style>
        :root {
            --green-light: #e8f5e9;
            --green-medium: #81c784;
            --green-dark:rgb(27, 167, 34);
            --text-dark: #212121;
            --text-light: #f5f5f5;
            --shadow: 0 4px 6px rgba(18, 244, 56, 0.97);
            --shadow-hover: 0 6px 12px rgba(20, 231, 59, 0.98);
        }

        /* Navbar modernizada */
        .navbar {
            background-color: var(--green-dark) !important;
            box-shadow: var(--shadow);
            padding: 0.8rem 1rem;
        }

        .navbar-brand {
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 0.5rem;
            font-size: 1.5rem;
        }

        .nav-link {
            padding: 0.5rem 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .nav-link i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-hover);
            border-radius: 8px;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
            display: flex;
            align-items: center;
        }

        .dropdown-item i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }

        /* Contenedor principal */
        .main-container {
            min-height: calc(100vh - 120px);
            padding: 2rem 0;
        }

        /* Alertas modernas */
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: var(--shadow);
        }

        /* Footer modernizado */
       footer {
            background-color: var(--green-dark);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
            text-align: center; /* Centra el texto horizontalmente */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centra horizontalmente los elementos hijos */
            justify-content: center; /* Centra verticalmente los elementos hijos */
            min-height: 100px; /* Altura mínima para mejor visualización */
        }

        footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Botones en fila (como solicitaste) */
        .btn-group-horizontal {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-group-horizontal .btn {
            flex: 1 0 auto;
            min-width: 120px;
        }

        /* Botones modernizados */
        .btn-success {
            background-color: var(--green-medium);
            border: none;
            font-weight: 500;
        }

        .btn-success:hover {
            background-color: var(--green-dark);
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .navbar-collapse {
                padding-top: 1rem;
            }
            
            .nav-link {
                padding: 0.5rem 0;
            }
            
            .main-container {
                padding: 1.5rem 0;
            }
            
            .btn-group-horizontal .btn {
                flex: 1 0 100%;
            }
        }
    </style>

    {{-- Pila de estilos adicionales --}}
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboardTec') : route('vendedor.dashboard')) : '/' }}">
                <i class="bi bi-shop"></i> Construye Verde
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if(auth()->user()->isAdmin())
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.ventas') }}">
                                    <i class="bi bi-cash-stack"></i> Ventas
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="inventarioDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-boxes"></i> Inventario
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.productos') }}"><i class="bi bi-box-seam"></i> Productos</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.categorias') }}"><i class="bi bi-tags"></i> Categorías</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.empleados') }}">
                                    <i class="bi bi-people"></i> Empleados
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.reportes') }}">
                                    <i class="bi bi-graph-up"></i> Reportes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.devoluciones') }}">
                                    <i class="bi bi-arrow-return-left"></i> Devoluciones
                                </a>
                            </li>
                        @elseif(auth()->user()->isVendedor())
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vendedor.ventas.index') }}">
                                    <i class="bi bi-cash-stack"></i> Ventas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vendedor.devoluciones') }}">
                                    <i class="bi bi-arrow-return-left"></i> Devoluciones
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->empleado->nomEmp ?? auth()->user()->usuario }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="mb-0">Sistema de Gestión Ferretería Construye Verde &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    {{-- Bootstrap Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts adicionales --}}
    @stack('scripts')
</body>
</html>