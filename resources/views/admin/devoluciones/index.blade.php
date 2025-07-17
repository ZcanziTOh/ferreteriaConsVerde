@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Devoluciones Registradas</h1>
    
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
                <th>Venta Relacionada</th>
                <th>Motivo</th>
                <th>Total Reembolso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devoluciones as $devolucion)
            <tr>
                <td>{{ $devolucion->IDDev }}</td>
                <td>{{ $devolucion->fechDev->format('d/m/Y H:i') }}</td>
                <td>#{{ $devolucion->venta->IDVent }}</td>
                <td>{{ Str::limit($devolucion->motivDev, 50) }}</td>
                <td>S/ {{ number_format($devolucion->totalRembDev, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection