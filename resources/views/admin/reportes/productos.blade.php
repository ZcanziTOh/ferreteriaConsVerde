@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte de Productos</h1>
    <p class="text-muted">Del {{ $fechaInicio }} al {{ $fechaFin }}</p>
        <style>
                /* Estilos para impresión profesional */
                @media print {
                    @page {
                        size: A4;
                        margin: 0;
                    }
                    .no-print, .card-header, .card-footer, header, footer, .navbar {
                        display: none !important;
                    }
                }
        </style>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Unidades Vendidas</th>
                <th>Total Ventas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->nomProd }}</td>
                <td>{{ $producto->categoria->nomCat }}</td>
                <td>{{ $producto->ventas_count }}</td>
                <td>S/ {{ number_format($producto->ventas_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
   <div class="card-footer text-right no-print">
        <a href="{{ route('admin.reportes') }}" class="btn btn-secondary">Volver</a>
        <button class="btn btn-primary" onclick="printComprobante()">Imprimir Reporte</button>
    </div>
</div>
<script>
    function printComprobante() {
        // Ocultar elementos no deseados antes de imprimir
        document.querySelectorAll('.no-print').forEach(el => {
            el.style.display = 'none';
        });
        
        // Activar la impresión
        window.print();
        
        // Restaurar los elementos ocultos
        setTimeout(() => {
            document.querySelectorAll('.no-print').forEach(el => {
                el.style.display = '';
            });
        }, 500);
    }
</script>
@endsection