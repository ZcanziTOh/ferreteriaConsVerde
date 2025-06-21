@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ventas</h1>
    
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
                        Cliente no registrado
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