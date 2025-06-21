@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pedidos</h1>
    <a href="{{ route('admin.pedidos.create') }}" class="btn btn-primary mb-3">Nuevo Pedido</a>
    
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
                <th>Total</th>
                <th>Estado</th>
                <th>Proveedor</th>
                <th>Empleado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
            <tr>
                <td>{{ $pedido->IDPed }}</td>
                <td>{{ $pedido->fechPed->format('d/m/Y') }}</td>
                <td>S/ {{ number_format($pedido->totalPed, 2) }}</td>
                <td>
                    <span class="badge badge-{{ $pedido->estadPed == 'pendiente' ? 'warning' : ($pedido->estadPed == 'recibido' ? 'success' : 'danger') }}">
                        {{ ucfirst($pedido->estadPed) }}
                    </span>
                </td>
                <td>{{ $pedido->proveedor->razonSocialProv }}</td>
                <td>{{ $pedido->empleado->nomEmp }} {{ $pedido->empleado->apelEmp }}</td>
                <td>
                    <a href="{{ route('admin.pedidos.show', $pedido->IDPed) }}" class="btn btn-sm btn-info">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection