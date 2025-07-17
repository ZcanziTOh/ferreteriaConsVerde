@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <h1 class="dashboard-title">
        <i class="bi bi-graph-up me-2"></i>Reportes
    </h1>
    
    <div class="reports-grid">
        <!-- Reporte de Ventas -->
        <div class="report-card sales-report">
            <div class="card-header">
                <i class="bi bi-currency-dollar"></i>
                <h2>Reporte de Ventas</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reportes.ventas') }}" method="GET">
                    <div class="form-group">
                        <label for="fecha_inicio">
                            <i class="bi bi-calendar-event me-2"></i>Fecha Inicio
                        </label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">
                            <i class="bi bi-calendar-check me-2"></i>Fecha Fin
                        </label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Generar Reporte
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Reporte de Productos -->
        <div class="report-card products-report">
            <div class="card-header">
                <i class="bi bi-box-seam"></i>
                <h2>Reporte de Productos</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reportes.productos') }}" method="GET">
                    <div class="form-group">
                        <label for="fecha_inicio">
                            <i class="bi bi-calendar-event me-2"></i>Fecha Inicio
                        </label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">
                            <i class="bi bi-calendar-check me-2"></i>Fecha Fin
                        </label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Generar Reporte
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Espacio para más reportes -->
        <div class="report-card more-reports">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i>
                <h2>Otros Reportes</h2>
            </div>
            <div class="card-body">
                <p>Próximamente más opciones de reportes</p>
                <button class="btn-coming-soon" disabled>
                    <i class="bi bi-hourglass-split me-2"></i>Próximamente
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos específicos para la página de reportes */
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .report-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        border-top: 4px solid;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .sales-report {
        border-top-color: #4caf50;
    }

    .products-report {
        border-top-color: #2196f3;
    }

    .more-reports {
        border-top-color: #9c27b0;
    }

    .card-header {
        padding: 1.5rem;
        background-color: white;
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .card-header i {
        font-size: 1.75rem;
        margin-right: 1rem;
    }

    .sales-report .card-header i {
        color: #4caf50;
    }

    .products-report .card-header i {
        color: #2196f3;
    }

    .more-reports .card-header i {
        color: #9c27b0;
    }

    .card-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        color:rgb(28, 153, 36);
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: flex;
        align-items: center;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #555;
    }

    .form-control {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: #81c784;
        box-shadow: 0 0 0 3px rgba(129, 199, 132, 0.2);
    }

    .btn-generate {
        background-color: #388e3c;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-generate:hover {
        background-color: #2e7d32;
        transform: translateY(-2px);
    }

    .btn-coming-soon {
        background-color: #f5f5f5;
        color: #757575;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        cursor: not-allowed;
    }

    .more-reports .card-body p {
        text-align: center;
        color: #757575;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .reports-grid {
            grid-template-columns: 1fr;
        }
        
        .card-header {
            padding: 1.25rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Script para establecer la fecha de hoy como valor predeterminado
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        dateInputs.forEach(input => {
            input.value = today;
        });
    });
</script>
@endpush
@endsection