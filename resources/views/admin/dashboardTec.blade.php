@extends('layouts.app')

@section('title', 'Dashboard Analítico')

@section('content')
<div class="dashboard-container">
    <h1 class="dashboard-title">Dashboard Administrador</h1>
    
    <!-- Sección de Métricas con KPI avanzados -->
    <div class="metrics-grid">
        <div class="metric-card products-card">
            <div class="metric-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Productos</span>
                <span class="metric-value">{{ $totalProductos }}</span>
                <div class="metric-trend {{ $productosTrend >= 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $productosTrend >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($productosTrend) }}%
                </div>
                <a href="{{ route('admin.productos') }}" class="metric-link">Ver Productos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="metric-card sales-card">
            <div class="metric-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Ingresos</span>
                <span class="metric-value">S/ {{ $ingresosTotales }}</span>
                <div class="metric-trend {{ $ingresosTrend >= 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $ingresosTrend >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($ingresosTrend) }}%
                </div>
                <a href="{{ route('admin.ventas') }}" class="metric-link">Ver Ventas  <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="metric-card employees-card">
            <div class="metric-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="metric-content">
                <span class="metric-title">Empleados</span>
                <span class="metric-value">{{ $totalEmpleados }}</span>

                <div class="metric-trend ">
                    <i class="bi bi-arrow-up"></i>
                    
                </div>
                <a href="{{ route('admin.empleados') }}" class="metric-link">Ver equipo <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <!-- Sección de Análisis Estratégico -->
    <div class="dashboard-section analytics-section">
        <div class="section-header">
            <h2 class="section-title">Análisis de Rendimiento</h2>
        </div>
        <div class="analytics-grid">
            <!-- Gráfico de Tendencias de Ventas -->
            <div class="analytics-card">
                <div class="analytics-header">
                    <h3>Tendencias de Ventas</h3>
                    <div class="analytics-legend">
                        <span><i class="bi bi-square-fill current"></i> Actual</span>
                        <span><i class="bi bi-square-fill previous"></i> Periodo anterior</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesTrendChart" height="250"></canvas>
                </div>
            </div>
            
            <!-- Mapa de Calor de Productos -->
            <div class="analytics-card">
                <div class="analytics-header">
                    <h3>Rendimiento por Producto</h3>
                    <div class="analytics-legend">
                        <span><i class="bi bi-circle-fill high"></i> Alto</span>
                        <span><i class="bi bi-circle-fill medium"></i> Medio</span>
                        <span><i class="bi bi-circle-fill low"></i> Bajo</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="productHeatmapChart" height="250"></canvas>
                </div>
            </div>
            
           <!-- Distribución de Tipos de Comprobante - VERSIÓN ACTUALIZADA -->
            <div class="analytics-card">
                <div class="analytics-header">
                    <h3>Distribución de Comprobantes</h3>
                    <div class="analytics-legend">
                        <span><i class="bi bi-square-fill" style="color: #4CAF50;"></i> Boletas</span>
                        <span><i class="bi bi-square-fill" style="color: #2196F3;"></i> Facturas</span>
                        <span><i class="bi bi-square-fill" style="color: #FF9800;"></i> Proformas</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="comprobanteChart"></canvas>
                </div>
                <div class="chart-summary">
                    <div class="summary-item">
                        <span class="summary-value" style="color: #4CAF50;">{{ $comprobanteData['data'][0] ?? 0 }}</span>
                        <span class="summary-label">Boletas</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value" style="color: #2196F3;">{{ $comprobanteData['data'][1] ?? 0 }}</span>
                        <span class="summary-label">Facturas</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value" style="color: #FF9800;">{{ $comprobanteData['data'][2] ?? 0 }}</span>
                        <span class="summary-label">Proformas</span>
                    </div>
                </div>
            </div>

            
            <!-- Métodos de Pago - Versión Actualizada -->
            <div class="analytics-card">
                <div class="analytics-header">
                    <h3>Métodos de Pago</h3>
                    <div class="analytics-legend">
                        <span><i class="bi bi-square-fill" style="color: #9C27B0;"></i> Efectivo</span>
                        <span><i class="bi bi-square-fill" style="color: #FFC107;"></i> Tarjeta</span>
                        <span><i class="bi bi-square-fill" style="color: #3F51B5;"></i> Transferencia</span>
                        <span><i class="bi bi-square-fill" style="color: #00BCD4;"></i> Yape</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="metodosPagoChart"></canvas>
                </div>
                <div class="chart-summary">
                    <div class="summary-item">
                        <span class="summary-value" style="color: #9C27B0;">{{ $metodosPago['cantidades'][0] ?? 0 }}</span>
                        <span class="summary-label">Efectivo</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value" style="color: #FFC107;">{{ $metodosPago['cantidades'][1] ?? 0 }}</span>
                        <span class="summary-label">Tarjeta</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value" style="color: #3F51B5;">{{ $metodosPago['cantidades'][2] ?? 0 }}</span>
                        <span class="summary-label">Transferencia</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value" style="color: #00BCD4;">{{ $metodosPago['cantidades'][3] ?? 0 }}</span>
                        <span class="summary-label">Yape</span>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Compras vs Ventas - Versión Mejorada -->
            <div class="analytics-card">
                <div class="analytics-header">
                    <h3>Compras vs Ventas</h3>
                    <div class="analytics-legend">
                        <span><i class="bi bi-square-fill" style="color: #EF5350;"></i> Compras</span>
                        <span><i class="bi bi-square-fill" style="color: #42A5F5;"></i> Ventas</span>
                    </div>
                </div>
                <div class="chart-container ">
                    <canvas id="graficoComprasVentas" height="250" ></canvas>
                </div>
                <div class="chart-summary">
                    <div class="summary-item">
                        <span class="summary-value" style="color: #EF5350;">S/ {{ number_format(array_sum(array_column($datosGrafico, 'compras')), 2) }}</span>
                        <span class="summary-label">Total Compras</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value" style="color: #42A5F5;">S/ {{ number_format(array_sum(array_column($datosGrafico, 'ventas')), 2) }}</span>
                        <span class="summary-label">Total Ventas</span>
                    </div>
                    <div class="summary-item">
                        @php
                            $gananciaTotal = array_sum(array_column($datosGrafico, 'ganancia'));
                            $gananciaClass = $gananciaTotal >= 0 ? 'text-success' : 'text-danger';
                        @endphp
                        <span class="summary-value {{ $gananciaClass }}">S/ {{ number_format($gananciaTotal, 2) }}</span>
                        <span class="summary-label">Ganancia Total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de Análisis de Ventas -->
    <div class="dashboard-section sales-section">
        <div class="section-header">
            <h2 class="section-title">Ultimas Ventas</h2>
        </div>
        <div class="sales-analytics-grid">
            
            <!-- Tabla de Últimas Ventas con más detalle -->
            <div class="sales-table-card">
                <div class="table-responsive">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Ticket Promedio</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasVentas as $venta)
                            <tr>
                                <td>{{ $venta->fechVent->format('d/m H:i') }}</td>
                                <td>S/ {{ number_format($venta->totalVent, 0) }}</td>
                                <td>
                                    @if($venta->clienteNatural)
                                        {{ Str::limit($venta->clienteNatural->nomClieNat.' '.$venta->clienteNatural->apelClieNat, 15) }}
                                    @elseif($venta->clienteJuridica)
                                        {{ Str::limit($venta->clienteJuridica->razSociClieJuri, 15) }}
                                    @else
                                        <span style="text-transform: uppercase;">Cliente sin datos</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($venta->usuario->empleado->nomEmp ?? 'N/A', 15) }}</td>
                                <td>
                                    <a href="{{ route('admin.ventas.show', $venta->IDVent) }}" class="btn-action view">
                                        <i class="bi bi-zoom-in"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    /* Mantenemos todos tus estilos anteriores y añadimos los nuevos */
    :root {
        --green-light: #e8f5e9;
        --green-medium: #81c784;
        --green-dark:rgb(26, 151, 32);
        --blue-light: #e3f2fd;
        --blue-medium: #64b5f6;
        --blue-dark: #1976d2;
        --orange-light: #fff3e0;
        --orange-medium: #ffb74d;
        --orange-dark: #f57c00;
        --purple-light: #f3e5f5;
        --purple-medium: #ba68c8;
        --purple-dark: #7b1fa2;
        --text-dark:rgb(83, 12, 237);
        --text-light: #f5f5f5;
        --shadow: 0 4px 6px rgb(16, 239, 23);
        --shadow-hover: 0 6px 12px rgba(16, 241, 31, 0.84);
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
     .chart-summary {
        display: flex;
        justify-content: space-around;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
    
    .summary-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .summary-value {
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .summary-label {
        font-size: 0.8rem;
        color: #666;
        margin-top: 0.25rem;
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
    /* Nuevos estilos para el dashboard analítico */
    .metric-trend {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: inline-flex;
        align-items: center;
    }
    
    .metric-trend.positive {
        color: var(--green-dark);
    }
    
    .metric-trend.negative {
        color: var(--danger-color);
    }
    
    .metric-trend i {
        margin-right: 0.25rem;
    }
    
    .analytics-section {
        border-top-color: var(--purple-medium);
    }
    
    .analytics-section .section-title {
        color: var(--purple-dark);
    }
    
    .time-filters {
        display: flex;
        gap: 0.5rem;
    }
    
    .time-filter {
        border: 1px solid #ddd;
        background: white;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .time-filter:hover, .time-filter.active {
        background-color: var(--purple-medium);
        color: white;
        border-color: var(--purple-medium);
    }
    
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .analytics-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .analytics-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }
    
    .analytics-legend {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
    }
    
    .analytics-legend span {
        display: flex;
        align-items: center;
    }
    
    .analytics-legend i {
        margin-right: 0.35rem;
        font-size: 0.8rem;
    }
    
    /* Colores para las leyendas */
    .current { color: var(--blue-medium); }
    .previous { color: #ddd; }
    .high { color: var(--green-dark); }
    .medium { color: var(--orange-medium); }
    .low { color: var(--danger-color); }
    .a-class { color: var(--green-dark); }
    .b-class { color: var(--orange-dark); }
    .c-class { color: var(--danger-color); }
    .stock { color: var(--blue-medium); }
    .forecast { color: var(--purple-medium); }
    .reorder { color: var(--danger-color); }
    
    /* Indicadores avanzados */
    .stock-gauge {
        height: 6px;
        background: #eee;
        border-radius: 3px;
        overflow: hidden;
        position: relative;
    }
    
    .stock-gauge::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: calc((var(--value) / var(--max)) * 100%);
        background: linear-gradient(90deg, var(--green-medium), var(--green-dark));
        border-radius: 3px;
    }
    
    .rotation-indicator {
        width: 80px;
        height: 8px;
        background: #eee;
        border-radius: 4px;
        position: relative;
    }
    
    .rotation-indicator::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: calc(var(--rotation) * 100%);
        background: linear-gradient(90deg, var(--orange-medium), var(--orange-dark));
        border-radius: 4px;
    }
    
    .profit-margin {
        width: 60px;
        height: 6px;
        background: #eee;
        border-radius: 3px;
        position: relative;
    }
    
    .profit-margin::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: calc(var(--margin) * 100%);
        background: linear-gradient(90deg, var(--green-medium), var(--green-dark));
        border-radius: 3px;
    }
    
    /* Sección de ventas mejorada */
    .sales-analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    
    .sales-analytics-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .sales-table-card {
        grid-column: span 2;
    }
    
    .sales-filters {
        display: flex;
        gap: 1rem;
    }
    
    .form-select {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0.35rem 0.75rem;
        font-size: 0.9rem;
    }
    
    /* Mejoras para la tabla de productos */
    .product-info {
        display: flex;
        align-items: center;
    }
    
    .product-img {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background-color: var(--blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: var(--blue-dark);
    }
    
    .product-name {
        font-weight: 500;
    }
    
    .product-category {
        font-size: 0.75rem;
        color: #666;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-action.order {
        background-color: var(--orange-light);
        color: var(--orange-dark);
    }
    
    .btn-action.analytics {
        background-color: var(--purple-light);
        color: var(--purple-dark);
    }
    
    .btn-action.order:hover {
        background-color: var(--orange-medium);
        color: white;
    }
    
    .btn-action.analytics:hover {
        background-color: var(--purple-medium);
        color: white;
    }
    
    /* Responsividad mejorada */
    @media (max-width: 1200px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
        
        .sales-analytics-grid {
            grid-template-columns: 1fr;
        }
        
        .sales-table-card {
            grid-column: span 1;
        }
    }
    
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .time-filters, .sales-filters {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
        
        .analytics-legend {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Librerías para gráficos avanzados -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-trendline@1.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.2"></script>

<script>
    // Configuración global de Chart.js
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.color = '#666';
    
    // 1. Gráfico de Tendencias de Ventas
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    const salesTrendChart = new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: @json($tendenciasVentas['labels']),
            datasets: [
                {
                    label: 'Actual',
                    data: @json($tendenciasVentas['actual']),
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Periodo anterior',
                    data: @json($tendenciasVentas['anterior']),
                    borderColor: '#ddd',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': S/ ' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
    
    // 2. Mapa de Calor de Productos
    const productHeatmapCtx = document.getElementById('productHeatmapChart').getContext('2d');
    const productHeatmapChart = new Chart(productHeatmapCtx, {
        type: 'bar',
        data: {
            labels: @json($productosHeatmap['productos']),
            datasets: [{
                data: @json($productosHeatmap['ventas']),
                backgroundColor: function(context) {
                    const value = context.raw;
                    const max = Math.max(...@json($productosHeatmap['ventas']));
                    const ratio = value / max;
                    
                    if (ratio > 0.7) return '#388e3c'; // Verde oscuro
                    if (ratio > 0.3) return '#ffb74d'; // Naranja
                    return '#f44336'; // Rojo
                },
                borderColor: 'rgba(255, 255, 255, 0.3)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ventas: S/ ' + context.raw.toLocaleString();
                        },
                        afterLabel: function(context) {
                            const producto = @json($productosHeatmap['productos'])[context.dataIndex];
                            const margen = @json($productosHeatmap['margenes'])[context.dataIndex];
                            return `Margen: ${(margen * 100).toFixed(1)}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
     // 3. Distribución de Comprobantes - VERSIÓN ACTUALIZADA
    const comprobanteChartCtx = document.getElementById('comprobanteChart').getContext('2d');
    const comprobanteChart = new Chart(comprobanteChartCtx, {
        type: 'doughnut',
        data: {
            labels: @json($comprobanteData['labels']),
            datasets: [{
                data: @json($comprobanteData['data']),
                backgroundColor: [
                    '#4CAF50', // Verde para Boletas
                    '#2196F3', // Azul para Facturas
                    '#FF9800'  // Naranja para Proformas
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        },
        plugins: [ChartDataLabels]
    });
    // Métodos de Pago Chart - Versión Actualizada
    const metodosPagoCtx = document.getElementById('metodosPagoChart').getContext('2d');
    const metodosPagoChart = new Chart(metodosPagoCtx, {
        type: 'doughnut',
        data: {
            labels: @json($metodosPago['metodos']),
            datasets: [{
                data: @json($metodosPago['cantidades']),
                backgroundColor: [
                    '#9C27B0', // Morado para Efectivo
                    '#FFC107', // Morado oscuro para Tarjeta
                    '#3F51B5', // Azul indigo para Transferencia
                    '#00BCD4'  // Cyan para Yape
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
     // 5. Gráfico de Compras vs Ventas - ESTILO COHERENTE
    const comprasVentasCtx = document.getElementById('graficoComprasVentas').getContext('2d');
    const comprasVentasChart = new Chart(comprasVentasCtx, {
        type: 'bar',
        data: {
            labels: @json(collect($datosGrafico)->pluck('periodo')),
            datasets: [
                {
                    label: 'Compras (S/)',
                    data: @json(collect($datosGrafico)->pluck('compras')),
                    backgroundColor: 'rgba(239, 83, 80, 0.8)',
                    borderColor: 'rgba(239, 83, 80, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Ventas (S/)',
                    data: @json(collect($datosGrafico)->pluck('ventas')),
                    backgroundColor: 'rgba(66, 165, 245, 0.8)',
                    borderColor: 'rgba(66, 165, 245, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: S/ ${context.raw.toLocaleString('es-PE', {minimumFractionDigits: 2})}`;
                        },
                        afterLabel: function(context) {
                            const ganancias = @json(collect($datosGrafico)->pluck('ganancia'));
                            const ganancia = ganancias[context.dataIndex];
                            return `Ganancia: S/ ${ganancia.toLocaleString('es-PE', {minimumFractionDigits: 2})}`;
                        }
                    }
                },
                datalabels: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString('es-PE');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        callback: function(value, index) {
                            const ganancias = @json(collect($datosGrafico)->pluck('ganancia'));
                            const ganancia = ganancias[index];
                            return [
                                this.getLabelForValue(value),
                                `G: S/${ganancia.toFixed(2)}`
                            ];
                        }
                    }
                }
            },
            layout: {
                padding: {
                    bottom: 20
                }
            }
        }
    });
    
    // Filtros de tiempo
    document.querySelectorAll('.time-filter').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelector('.time-filter.active').classList.remove('active');
            this.classList.add('active');
            
            // Aquí iría la lógica para actualizar los gráficos según el periodo seleccionado
            const period = this.dataset.period;
            console.log('Periodo seleccionado:', period);
            // Actualizaría los datos mediante AJAX o recargando la página con parámetros
        });
    });
    
    // Exportar inventario
    document.getElementById('exportInventory').addEventListener('click', function() {
        // Lógica para exportar el inventario
        console.log('Exportando inventario...');
    });
    
    // Filtros de ventas
    document.getElementById('salesRegionFilter').addEventListener('change', function() {
        // Lógica para filtrar por región
        console.log('Filtrando por región:', this.value);
    });
    
    document.getElementById('salesCategoryFilter').addEventListener('change', function() {
        // Lógica para filtrar por categoría
        console.log('Filtrando por categoría:', this.value);
    });
</script>
@endpush