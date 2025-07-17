@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Cotización</h1>
            <p class="text-muted mb-0">Fecha: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <style>
            @media print {
                .no-print, .card-footer, header, footer, .navbar {
                        display: none !important;
                }
            }
        </style>
        <div class="card-body">
            @if($cliente)
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Datos del Cliente</h4>
                    @if($request->tipo_cliente === 'natural')
                    <p><strong>Nombre:</strong> {{ $cliente->nomClieNat }} {{ $cliente->apelClieNat }}</p>
                    <p><strong>Documento:</strong> {{ $cliente->docIdenClieNat }}</p>
                    @else
                    <p><strong>Razón Social:</strong> {{ $cliente->razSociClieJuri }}</p>
                    <p><strong>RUC:</strong> {{ $cliente->rucClieJuri }}</p>
                    <p><strong>Dirección Fiscal:</strong> {{ $cliente->dirfiscClieJuri }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4>Datos de la Cotización</h4>
                    <p><strong>Vendedor:</strong> {{ Auth::user()->empleado->nomEmp }} {{ Auth::user()->empleado->apelEmp }}</p>
                    <p><strong>Validez:</strong> 7 días</p>
                </div>
            </div>
            @endif
            
            <h4>Productos</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Desc(%)</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productosSeleccionados as $item)
                    <tr>
                        <td>{{ $item['producto']->nomProd }}</td>
                        <td>{{ $item['cantidad'] }}</td>
                        <td>S/ {{ number_format($item['precio'], 2) }}</td>
                        <td>{{ $item['descuento'] }}</td>
                        <td>S/ {{ number_format($item['subtotal'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Subtotal:</th>
                        <th>S/ {{ number_format($subtotal, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">IGV (18%):</th>
                        <th>S/ {{ number_format($igv, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Total:</th>
                        <th>S/ {{ number_format($total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
            
            @if($request->observaciones)
            <div class="mt-4">
                <h5>Observaciones</h5>
                <p>{{ $request->observaciones }}</p>
            </div>
            @endif
        </div>
        
        <div class="card-footer text-right">
            <button class="btn btn-primary" onclick="window.print()">Imprimir Cotización</button>
            <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection