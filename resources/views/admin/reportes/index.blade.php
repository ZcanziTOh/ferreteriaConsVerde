@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reportes</h1>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Ventas</h5>
                    <form action="{{ route('admin.reportes.ventas') }}" method="GET">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Productos</h5>
                    <form action="{{ route('admin.reportes.productos') }}" method="GET">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Proveedores</h5>
                    <form action="{{ route('admin.reportes.proveedores') }}" method="GET">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio (opcional)</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin (opcional)</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                        </div>
                        <button type="submit" class="btn btn-primary">Generar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection