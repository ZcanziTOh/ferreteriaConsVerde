@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Clientes Jurídicos</h1>
    <a href="{{ route('vendedor.clientes.crear-juridico') }}" class="btn btn-primary mb-3">Nuevo Cliente Jurídico</a>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Razón Social</th>
                <th>RUC</th>
                <th>Dirección Fiscal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
            <tr>
                <td>{{ $cliente->IDClieJuri }}</td>
                <td>{{ $cliente->razSociClieJuri }}</td>
                <td>{{ $cliente->rucClieJuri }}</td>
                <td>{{ $cliente->dirfiscClieJuri }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection