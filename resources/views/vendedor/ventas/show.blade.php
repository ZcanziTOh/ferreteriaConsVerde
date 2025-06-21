@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Detalle de Venta #{{ $venta->IDVent }}</h1>
            <p class="text-muted mb-0">Fecha: {{ $venta->fechVent->format('d/m/Y H:i') }}</p>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Datos del Cliente</h4>
                    @if($venta->clienteNatural)
                    <p><strong>Nombre:</strong> {{ $venta->clienteNatural->nomClieNat }} {{ $venta->clienteNatural->apelClieNat }}</p>
                    <p><strong>Documento:</strong> {{ $venta->clienteNatural->docIdenClieNat }}</p>
                    @elseif($venta->clienteJuridica)
                    <p><strong>Razón Social:</strong> {{ $venta->clienteJuridica->razSociClieJuri }}</p>
                    <p><strong>RUC:</strong> {{ $venta->clienteJuridica->rucClieJuri }}</p>
                    <p><strong>Dirección Fiscal:</strong> {{ $venta->clienteJuridica->dirfiscClieJuri }}</p>
                    @else
                    <p>Sin cliente registrado</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4>Datos de la Venta</h4>
                    <p><strong>Vendedor:</strong> {{ $venta->usuario->empleado->nomEmp }} {{ $venta->usuario->empleado->apelEmp }}</p>
                    <p><strong>Método de Pago:</strong> {{ ucfirst($venta->metPagVent) }}</p>
                    <p><strong>Comprobante:</strong> {{ ucfirst($venta->comprobantes->first()->tipCompr ?? 'N/A') }}</p>
                    @if($venta->codSunatVent)
                    <p><strong>Código SUNAT:</strong> {{ $venta->codSunatVent }}</p>
                    @endif
                </div>
            </div>
            
            <h4>Productos</h4>
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
                        <td>{{ $detalle->cant ?? intval($detalle->subtotal / $detalle->prec_uni) }}</td>
                        <td>S/ {{ number_format($detalle->prec_uni, 2) }}</td>
                        <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Subtotal:</th>
                        <th>S/ {{ number_format($venta->totalVent / 1.18, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">IGV (18%):</th>
                        <th>S/ {{ number_format($venta->totalVent - ($venta->totalVent / 1.18), 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Total:</th>
                        <th>S/ {{ number_format($venta->totalVent, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="card-footer text-right">
            <button class="btn btn-primary" onclick="window.print()">Imprimir Comprobante</button>
            <a href="{{ route('vendedor.ventas.index') }}" class="btn btn-secondary">Volver</a>
            
            @if($venta->comprobantes->first()->tipCompr !== 'factura')
            <a href="{{ route('vendedor.devoluciones.create', $venta->IDVent) }}" class="btn btn-warning">Registrar Devolución</a>
            @endif
        </div>
    </div>
</div>
@endsection