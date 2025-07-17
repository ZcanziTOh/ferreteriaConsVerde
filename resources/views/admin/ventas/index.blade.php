@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel de Ventas</h1>
    <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-primary">
        <i class="bi bi-file-earmark-text"></i> Nueva Cotización
    </a>
    <a href="{{ route('admin.ventas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Venta
    </a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
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
                        <span style="text-transform: uppercase;">Cliente sin datos</span>
                    @endif
                </td>
                <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                <td>
                    <a href="{{ route('admin.ventas.show', $venta->IDVent) }}" class="btn btn-sm btn-info">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection