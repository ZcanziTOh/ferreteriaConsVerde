@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ventas Registradas</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Método Pago</th>
                <th>Comprobante</th>
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
                <td>{{ ucfirst($venta->metPagVent) }}</td>
                <td>{{ ucfirst($venta->comprobantes->first()->tipCompr ?? 'N/A') }}</td>
                <td>
                    <a href="{{ route('vendedor.ventas.show', $venta->IDVent) }}" class="btn btn-sm btn-info">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection