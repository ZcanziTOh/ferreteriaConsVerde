@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle de Venta #{{ $venta->IDVent }}</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> {{ $venta->fechVent->format('d/m/Y H:i') }}</p>
                    <p><strong>Total:</strong> S/ {{ number_format($venta->totalVent, 2) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> 
                        @if($venta->clienteNatural)
                            {{ $venta->clienteNatural->nomClieNat }} {{ $venta->clienteNatural->apelClieNat }}
                        @elseif($venta->clienteJuridica)
                            {{ $venta->clienteJuridica->razSociClieJuri }}
                        @else
                            Cliente no registrado
                        @endif
                    </p>
                    <p><strong>Vendedor:</strong> {{ $venta->usuario->empleado->nomEmp }} {{ $venta->usuario->empleado->apelEmp }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <h3>Productos</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalleVentas as $detalle)
            <tr>
                <td>{{ $detalle->producto->nomProd }}</td>
                <td>{{ $detalle->cant }}</td>
                <td>S/ {{ number_format($detalle->prec_uni, 2) }}</td>
                <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th>S/ {{ number_format($venta->totalVent, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    
    @if($venta->comprobantes->count() > 0)
    <h3>Comprobantes</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Código</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->comprobantes as $comprobante)
            <tr>
                <td>{{ ucfirst($comprobante->tipCompr) }}</td>
                <td>{{ $comprobante->codSunatVent ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <a href="{{ route('admin.ventas') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection