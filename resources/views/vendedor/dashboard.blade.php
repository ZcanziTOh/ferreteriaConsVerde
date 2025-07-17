@extends('layouts.app')

@section('title', 'Dashboard Vendedor')

@section('content')
<div class="dashboard-container">
    <h1 class="dashboard-title">Panel de Vendedor</h1>
    
    <!-- Sección de Métricas -->
    <div class="metrics-grid">
        <div class="metric-card sales-card">
            <div class="metric-icon">
                <i class="bi bi-cart-check"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Ventas hoy</span>
                <span class="metric-value">{{ $ventasHoy }}</span>
                <a href="{{ route('vendedor.ventas.index') }}" class="metric-link">Ver detalles <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="metric-card revenue-card">
            <div class="metric-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Total vendido hoy</span>
                <span class="metric-value">S/ {{ number_format($totalVentasHoy, 2) }}</span>            </div>
        </div>
    </div>
    
    <!-- Sección de Acciones Rápidas -->
    <div class="dashboard-section">
        <h2 class="section-title">Acciones rápidas</h2>
        <div class="quick-actions-grid">
            <a href="{{ route('vendedor.ventas.create') }}" class="quick-action-card new-sale">
                <i class="bi bi-plus-circle"></i>
                <span>Nueva Venta</span>
            </a>
            
            <a href="{{ route('vendedor.cotizaciones.index') }}" class="quick-action-card new-quote">
                <i class="bi bi-file-earmark-text"></i>
                <span>Crear Cotización</span>
            </a>
            
            <a href="{{ route('vendedor.cierre-caja') }}" class="quick-action-card cash-close">
                <i class="bi bi-cash-stack"></i>
                <span>Cierre de Caja</span>
            </a>
        </div>
    </div>
    
    <!-- Sección de Últimas Ventas -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Últimas ventas</h2>
            <a href="{{ route('vendedor.ventas.index') }}" class="section-link">Ver historial completo</a>
        </div>
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Método de pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimasVentas as $venta)
                    <tr>
                        <td>{{ $venta->fechVent->format('d/m/Y H:i') }}</td>
                        <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                        <td>{{ ucfirst($venta->metPagVent) }}</td>
                        <td>
                            <a href="{{ route('vendedor.ventas.show', $venta->IDVent) }}" class="btn-action">
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
        --green-dark:rgb(16, 173, 23);
        --blue-light: #e3f2fd;
        --blue-medium: #64b5f6;
        --blue-dark: #1976d2;
        --orange-light: #fff3e0;
        --orange-medium: #ffb74d;
        --orange-dark: #f57c00;
        --text-dark: #212121;
        --text-light: #f5f5f5;
        --shadow: 0 4px 6px rgb(6, 247, 74);
        --shadow-hover: 0 6px 12px rgba(13, 248, 76, 0.99);
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
    }

    /* Tarjetas de métricas */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        transition: transform 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .sales-card {
        border-left: 4px solid var(--blue-medium);
    }

    .revenue-card {
        border-left: 4px solid var(--green-medium);
    }

    .metric-icon {
        font-size: 2rem;
        margin-right: 1.5rem;
    }

    .sales-card .metric-icon {
        color: var(--blue-medium);
    }

    .revenue-card .metric-icon {
        color: var(--green-medium);
    }

    .metric-content {
        display: flex;
        flex-direction: column;
    }

    .metric-title {
        font-size: 1rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .sales-card .metric-value {
        color: var(--blue-dark);
    }

    .revenue-card .metric-value {
        color: var(--green-dark);
    }

    .metric-link {
        color: #666;
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        transition: color 0.2s ease;
    }

    .sales-card .metric-link:hover {
        color: var(--blue-dark);
    }

    .revenue-card .metric-link:hover {
        color: var(--green-dark);
    }

    /* Secciones del dashboard */
    .dashboard-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        color: var(--green-dark);
        font-weight: 600;
        font-size: 1.5rem;
    }

    .section-link {
        color: var(--green-medium);
        text-decoration: none;
        font-weight: 500;
    }

    .section-link:hover {
        color: var(--green-dark);
        text-decoration: underline;
    }

    /* Acciones rápidas */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .quick-action-card {
        background: white;
        border-radius: 12px;
        padding: 2rem 1rem;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .quick-action-card i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .quick-action-card span {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .new-sale {
        color: var(--green-dark);
        border-color: var(--green-medium);
    }

    .new-sale:hover {
        background-color: var(--green-light);
    }

    .new-sale i {
        color: var(--green-medium);
    }

    .new-quote {
        color: var(--blue-dark);
        border-color: var(--blue-medium);
    }

    .new-quote:hover {
        background-color: var(--blue-light);
    }

    .new-quote i {
        color: var(--blue-medium);
    }

    .cash-close {
        color: var(--orange-dark);
        border-color: var(--orange-medium);
    }

    .cash-close:hover {
        background-color: var(--orange-light);
    }

    .cash-close i {
        color: var(--orange-medium);
    }

    /* Tablas modernas */
    .dashboard-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
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
        background-color: var(--green-light);
    }

    /* Botones de acción */
    .btn-action {
        background-color: var(--green-light);
        color: var(--green-dark);
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .btn-action:hover {
        background-color: var(--green-medium);
        color: white;
    }

    .btn-action i {
        margin-right: 0.5rem;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions-grid {
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
    }
</style>
@endpush

@push('scripts')
<!-- Incluir Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endpush