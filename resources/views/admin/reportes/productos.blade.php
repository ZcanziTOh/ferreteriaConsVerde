@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte de Productos</h1>
    <p class="text-muted">Del {{ $fechaInicio }} al {{ $fechaFin }}</p>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Unidades Vendidas</th>
                <th>Total Ventas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->nomProd }}</td>
                <td>{{ $producto->categoria->nomCat }}</td>
                <td>{{ $producto->proveedor->razonSocialProv }}</td>
                <td>{{ $producto->ventas_count }}</td>
                <td>S/ {{ number_format($producto->ventas_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="{{ route('admin.reportes') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection