@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del Pedido #{{ $pedido->IDPed }}</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> {{ $pedido->fechPed->format('d/m/Y') }}</p>
                    <p><strong>Estado:</strong> 
                        <span class="badge badge-{{ $pedido->estadPed == 'pendiente' ? 'warning' : ($pedido->estadPed == 'recibido' ? 'success' : 'danger') }}">
                            {{ ucfirst($pedido->estadPed) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Proveedor:</strong> {{ $pedido->proveedor->razonSocialProv }}</p>
                    <p><strong>Empleado:</strong> {{ $pedido->empleado->nomEmp }} {{ $pedido->empleado->apelEmp }}</p>
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
            @foreach($pedido->detallePedidos as $detalle)
            <tr>
                <td>{{ $detalle->producto->nomProd }}</td>
                <td>{{ $detalle->cant }}</td>
                <td>S/ {{ number_format($detalle->precUni, 2) }}</td>
                <td>S/ {{ number_format($detalle->cant * $detalle->precUni, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th>S/ {{ number_format($pedido->totalPed, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    
    @if($pedido->estadPed != 'cancelado')
    <form action="{{ route('admin.pedidos.update-status', $pedido->IDPed) }}" method="POST" class="mt-3">
        @csrf
        <div class="form-group">
            <label for="estadPed">Cambiar Estado</label>
            <select class="form-control" id="estadPed" name="estadPed" required>
                <option value="pendiente" {{ $pedido->estadPed == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="recibido" {{ $pedido->estadPed == 'recibido' ? 'selected' : '' }}>Recibido</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Estado</button>
        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-secondary">Volver</a>
    </form>
    @else
    <a href="{{ route('admin.pedidos') }}" class="btn btn-secondary">Volver</a>
    @endif
</div>
@endsection