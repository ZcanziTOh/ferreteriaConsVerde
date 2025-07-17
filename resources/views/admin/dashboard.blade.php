@extends('layouts.app')

@section('title', 'Dashboard Administrador')

@section('content')
<div class="dashboard-container">
    <h1 class="dashboard-title">Panel de Administración</h1>
    
    <!-- Sección de Métricas con clases específicas -->
    <div class="metrics-grid">
        <div class="metric-card products-card">
            <div class="metric-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Productos</span>
                <span class="metric-value">{{ $totalProductos }}</span>
                <a href="{{ route('admin.productos') }}" class="metric-link">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="metric-card providers-card">
            <div class="metric-icon">
                <i class="bi bi-truck"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Proveedores</span>
                <span class="metric-value">{{ $totalProveedores }}</span>
                <a href="{{ route('admin.proveedores') }}" class="metric-link">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="metric-card sales-card">
            <div class="metric-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Ventas</span>
                <span class="metric-value">{{ $totalVentas }}</span>
                <a href="{{ route('admin.pedidos') }}" class="metric-link">Ver reporte <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="metric-card employees-card">
            <div class="metric-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Empleados</span>
                <span class="metric-value">{{ $totalEmpleados }}</span>
                <a href="{{ route('admin.empleados') }}" class="metric-link">Ver equipo <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <!-- Sección de Productos con bajo stock -->
    <div class="dashboard-section stock-section">
        <div class="section-header">
            <h2 class="section-title">Productos con bajo stock</h2>
            <a href="{{ route('admin.productos') }}" class="section-link">Gestionar inventario</a>
        </div>
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productosBajoStock as $producto)
                    <tr>
                        <td>{{ $producto->nomProd }}</td>
                        <td>
                            <span class="badge badge-low-stock">{{ $producto->stockProd }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.productos.edit', $producto->IDProd) }}" class="btn-action edit">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Sección de Últimas ventas -->
    <div class="dashboard-section sales-section">
        <div class="section-header">
            <h2 class="section-title">Últimas ventas</h2>
            <a href="{{ route('admin.ventas') }}" class="section-link">Ver historial completo</a>
        </div>
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Vendedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimasVentas as $venta)
                    <tr>
                        <td>{{ $venta->fechVent->format('d/m/Y H:i') }}</td>
                        <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                        <td>{{ $venta->usuario->empleado->nomEmp ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.ventas.show', $venta->IDVent) }}" class="btn-action view">
                                <i class="bi bi-eye"></i> Detalles
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --green-light: #e8f5e9;
        --green-medium: #81c784;
        --green-dark: #388e3c;
        --blue-light: #e3f2fd;
        --blue-medium: #64b5f6;
        --blue-dark: #1976d2;
        --orange-light: #fff3e0;
        --orange-medium: #ffb74d;
        --orange-dark: #f57c00;
        --purple-light: #f3e5f5;
        --purple-medium: #ba68c8;
        --purple-dark: #7b1fa2;
        --text-dark: #212121;
        --text-light: #f5f5f5;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .dashboard-container {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-title {
        color: var(--green-dark);
        font-weight: 600;
        margin-bottom: 2rem;
        font-size: 2rem;
        border-bottom: 2px solid var(--green-medium);
        padding-bottom: 0.5rem;
    }

    /* Tarjetas de métricas con bordes de colores */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left: 5px solid;
    }

    /* Bordes y colores específicos para cada tarjeta */
    .products-card {
        border-left-color: var(--blue-medium);
    }
    .providers-card {
        border-left-color: var(--purple-medium);
    }
    .sales-card {
        border-left-color: var(--green-medium);
    }
    .employees-card {
        border-left-color: var(--orange-medium);
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .metric-icon {
        font-size: 2rem;
        margin-right: 1.5rem;
    }

    /* Iconos por tipo de tarjeta */
    .products-card .metric-icon {
        color: var(--blue-medium);
    }
    .providers-card .metric-icon {
        color: var(--purple-medium);
    }
    .sales-card .metric-icon {
        color: var(--green-medium);
    }
    .employees-card .metric-icon {
        color: var(--orange-medium);
    }

    .metric-content {
        display: flex;
        flex-direction: column;
    }

    .metric-title {
        font-size: 1rem;
        color: #666;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .metric-value {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    /* Colores de texto para métricas */
    .products-card .metric-value {
        color: var(--blue-dark);
    }
    .providers-card .metric-value {
        color: var(--purple-dark);
    }
    .sales-card .metric-value {
        color: var(--green-dark);
    }
    .employees-card .metric-value {
        color: var(--orange-dark);
    }

    .metric-link {
        color: #666;
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        transition: color 0.2s ease;
        font-weight: 500;
    }

    /* Colores de enlace por tipo */
    .products-card .metric-link:hover {
        color: var(--blue-dark);
    }
    .providers-card .metric-link:hover {
        color: var(--purple-dark);
    }
    .sales-card .metric-link:hover {
        color: var(--green-dark);
    }
    .employees-card .metric-link:hover {
        color: var(--orange-dark);
    }

    /* Secciones del dashboard con bordes */
    .dashboard-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        border-top: 4px solid;
    }

    /* Bordes de colores para diferentes secciones */
    .stock-section {
        border-top-color: var(--orange-medium);
    }
    .sales-section {
        border-top-color: var(--blue-medium);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-weight: 600;
        font-size: 1.5rem;
    }

    /* Colores de título por sección */
    .stock-section .section-title {
        color: var(--orange-dark);
    }
    .sales-section .section-title {
        color: var(--blue-dark);
    }

    .section-link {
        color: #666;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
    }

    .section-link i {
        margin-left: 0.5rem;
    }

    /* Colores de enlace por sección */
    .stock-section .section-link:hover {
        color: var(--orange-dark);
    }
    .sales-section .section-link:hover {
        color: var(--blue-dark);
    }

    /* Tablas modernas con bordes */
    .dashboard-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .dashboard-table th {
        background-color: var(--green-light);
        color: var(--green-dark);
        font-weight: 600;
        padding: 1rem;
        text-align: left;
    }

    .dashboard-table td {
        padding: 1rem;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .dashboard-table tr:last-child td {
        border-bottom: none;
    }

    .dashboard-table tr:hover td {
        background-color: rgba(129, 199, 132, 0.1);
    }

    /* Badges con más variedad */
    .badge-low-stock {
        background-color: var(--orange-light);
        color: var(--orange-dark);
        padding: 0.35rem 0.65rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Botones de acción con estilos específicos */
    .btn-action {
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Variantes de botones */
    .btn-action.edit {
        background-color: var(--orange-light);
        color: var(--orange-dark);
    }
    .btn-action.view {
        background-color: var(--blue-light);
        color: var(--blue-dark);
    }
    .btn-action.delete {
        background-color: #f8d7da;
        color: #721c24;
    }

    .btn-action:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    .btn-action.edit:hover {
        background-color: var(--orange-medium);
    }
    .btn-action.view:hover {
        background-color: var(--blue-medium);
    }
    .btn-action.delete:hover {
        background-color: #dc3545;
    }

    .btn-action i {
        margin-right: 0.5rem;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: 1fr;
        }
        
        .dashboard-container {
            padding: 1rem;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .section-link {
            margin-top: 0.5rem;
        }
        
        .dashboard-table th, 
        .dashboard-table td {
            padding: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Incluir Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endpush