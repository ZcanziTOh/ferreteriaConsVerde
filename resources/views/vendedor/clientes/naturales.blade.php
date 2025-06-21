@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Clientes Naturales</h1>
    <a href="{{ route('vendedor.clientes.crear-natural') }}" class="btn btn-primary mb-3">Nuevo Cliente Natural</a>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Apellido</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
            <tr>
                <td>{{ $cliente->IDClieNat }}</td>
                <td>{{ $cliente->docIdenClieNat }}</td>
                <td>{{ $cliente->nomClieNat }}</td>
                <td>{{ $cliente->apelClieNat }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection