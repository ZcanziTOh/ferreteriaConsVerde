@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte de Ventas</h1>
    <p class="text-muted">Del {{ $fechaInicio }} al {{ $fechaFin }}</p>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Ventas</h5>
                    <h3>S/ {{ number_format($totalVentas, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Productos Vendidos</h5>
                    <h3>{{ $totalProductosVendidos }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>{{ $venta->IDVent }}</td>
                <td>{{ $venta->fechVent->format('d/m/Y H:i') }}</td>
                <td>
                    @if($venta->clienteNatural)
                        {{ $venta->clienteNatural->nomClieNat }} {{ $venta->clienteNatural->apelClieNat }}
                    @elseif($venta->clienteJuridica)
                        {{ $venta->clienteJuridica->razSociClieJuri }}
                    @else
                        Cliente no registrado
                    @endif
                </td>
                <td>{{ $venta->usuario->empleado->nomEmp }} {{ $venta->usuario->empleado->apelEmp }}</td>
                <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                <td>
                    <a href="{{ route('admin.ventas.show', $venta->IDVent) }}" class="btn btn-sm btn-info">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="{{ route('admin.reportes') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection