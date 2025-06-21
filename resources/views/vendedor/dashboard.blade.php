@extends('layouts.app')

@section('title', 'Dashboard Vendedor')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Vendedor</h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Ventas hoy</h5>
                    <p class="card-text display-6">{{ $ventasHoy }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total vendido hoy</h5>
                    <p class="card-text display-6">S/ {{ number_format($totalVentasHoy, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Acciones rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('vendedor.ventas.create') }}" class="btn btn-success">Nueva Venta</a>
                        <a href="{{ route('vendedor.cotizaciones.index') }}" class="btn btn-primary">Crear Cotización</a>
                        <a href="{{ route('vendedor.cierre-caja') }}" class="btn btn-warning">Cierre de Caja</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Últimas ventas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Método</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasVentas as $venta)
                            <tr>
                                <td>{{ $venta->fechVent->format('d/m/Y H:i') }}</td>
                                <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                                <td>{{ ucfirst($venta->metPagVent) }}</td>
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