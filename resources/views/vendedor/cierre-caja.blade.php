@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Cierre de Caja - {{ $hoy }}</h1>
        </div>
        <style>
            @media print {
                .no-print, .card-footer, header, footer, .navbar {
                        display: none !important;
                }
            }
        </style>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Resumen de Ventas</h4>
                    <p><strong>Total Ventas:</strong> {{ $ventas->count() }}</p>
                    <p><strong>Boletas Emitidas:</strong> {{ $boletas }}</p>
                    <p><strong>Facturas Emitidas:</strong> {{ $facturas }}</p>
                    <p><strong>Proformas Emitidas:</strong> {{ $proformas }}</p>
                </div>
                <div class="col-md-6">
                    <h4>Totales por Método de Pago</h4>
                    <p><strong>Efectivo:</strong> S/ {{ number_format($totalEfectivo, 2) }}</p>
                    <p><strong>Tarjeta:</strong> S/ {{ number_format($totalTarjeta, 2) }}</p>
                    <p><strong>Yape:</strong> S/ {{ number_format($totalYape, 2) }}</p>
                    <p><strong>Transferencia:</strong> S/ {{ number_format($totalTransferencia, 2) }}</p>
                    <p class="font-weight-bold"><strong>Total General:</strong> S/ {{ number_format($totalGeneral, 2) }}</p>
                </div>
            </div>
            
            <h4>Detalle de Ventas</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Método Pago</th>
                            <th>Comprobante</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventas as $venta)
                        <tr>
                            <td>{{ $venta->IDVent }}</td>
                            <td>{{ $venta->fechVent->format('H:i') }}</td>
                            <td>
                                @if($venta->clienteNatural)
                                    {{ $venta->clienteNatural->nomClieNat }} {{ $venta->clienteNatural->apelClieNat }}
                                @elseif($venta->clienteJuridica)
                                    {{ $venta->clienteJuridica->razSociClieJuri }}
                                @elseif(session('cliente_temporal'))
                                    {{ strtoupper(session('cliente_temporal.nombre')) }} {{ strtoupper(session('cliente_temporal.apellido')) }}
                                @else
                                    Sin cliente
                                @endif
                            </td>
                            <td>{{ ucfirst($venta->metPagVent) }}</td>
                            <td>{{ ucfirst($venta->comprobantes->first()->tipCompr) }}</td>
                            <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer text-right">
            <button class="btn btn-primary" onclick="window.print()">Imprimir Reporte</button>
        </div>
    </div>
</div>
@endsection