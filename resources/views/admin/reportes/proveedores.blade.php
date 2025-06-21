@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte de Proveedores</h1>
    @if($fechaInicio && $fechaFin)
        <p class="text-muted">Del {{ $fechaInicio }} al {{ $fechaFin }}</p>
    @else
        <p class="text-muted">Todos los registros</p>
    @endif
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Proveedor</th>
                <th>RUC</th>
                <th>Pedidos</th>
                <th>Total Pedidos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proveedores as $proveedor)
            <tr>
                <td>{{ $proveedor->razonSocialProv }}</td>
                <td>{{ $proveedor->rucProv }}</td>
                <td>{{ $proveedor->pedidos_count }}</td>
                <td>S/ {{ number_format($proveedor->pedidos_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="{{ route('admin.reportes') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection