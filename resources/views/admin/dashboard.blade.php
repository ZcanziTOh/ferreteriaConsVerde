@extends('layouts.app')

@section('title', 'Dashboard Administrador')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Administrador</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Productos</h5>
                    <p class="card-text display-6">{{ $totalProductos }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Proveedores</h5>
                    <p class="card-text display-6">{{ $totalProveedores }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Ventas</h5>
                    <p class="card-text display-6">{{ $totalVentas }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Empleados</h5>
                    <p class="card-text display-6">{{ $totalEmpleados }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Productos con bajo stock</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
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
                                <td>{{ $producto->stockProd }}</td>
                                <td>
                                    <a href="{{ route('admin.productos.edit', $producto->IDProd) }}" class="btn btn-sm btn-warning">Editar</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                                <th>Vendedor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasVentas as $venta)
                            <tr>
                                <td>{{ $venta->fechVent->format('d/m/Y H:i') }}</td>
                                <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                                <td>{{ $venta->usuario->empleado->nomEmp ?? 'N/A' }}</td>
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