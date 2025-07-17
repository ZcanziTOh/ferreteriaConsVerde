@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header no-print">
            <h1>Detalle de Venta #{{ $venta->IDVent }}</h1>
            <p class="text-muted mb-0">Fecha: {{ $venta->fechVent->format('d/m/Y H:i') }}</p>
        </div>
        
        <div class="card-body">
            <style>
                /* Estilos para impresión profesional */
                @media print {
                    @page {
                        size: A4;
                        margin: 0;
                    }
                    
                    body {
                        margin: 0;
                        padding: 15mm;
                        font-family: 'Arial', sans-serif;
                        background-image: url('{{ asset('img/logo.jpg') }}');
                        background-repeat: no-repeat;
                        background-position: center;
                        background-size: 250px;
                        opacity: 1;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    
                    .no-print, .card-header, .card-footer, header, footer, .navbar {
                        display: none !important;
                    }
                    
                    .card {
                        border: none !important;
                        box-shadow: none !important;
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    
                    .comprobante-header {
                        text-align: center;
                        margin-bottom: 15px;
                    }
                    
                    .comprobante-header img {
                        height: 120px;
                        margin-bottom: 10px;
                    }
                    
                    .comprobante-header h2 {
                        font-size: 18px;
                        margin: 5px 0;
                        color: #333;
                    }
                    
                    .comprobante-header p {
                        font-size: 12px;
                        margin: 2px 0;
                        color: #555;
                    }
                    
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 15px 0;
                        font-size: 12px;
                    }
                    
                    th {
                        background-color: #f8f9fa !important;
                        color: #333;
                        font-weight: bold;
                        padding: 8px;
                        border: 1px solid #dee2e6;
                    }
                    
                    td {
                        padding: 8px;
                        border: 1px solid #dee2e6;
                    }
                    
                    .text-right {
                        text-align: right;
                    }
                    
                    .total-row {
                        font-weight: bold;
                        background-color: #f8f9fa;
                    }
                }
                
                /* Estilos para vista web */
                .comprobante-header-web {
                    display: none;
                }
                img {
                    width: 100%; /* Asegura que la imagen no exceda el ancho del contenedor */
                    height: auto;    /* Mantiene la proporción de aspecto */
                    display: block;  /* Elimina espacios no deseados alrededor */
                    margin: 0 auto;  /* Centra la imagen horizontalmente */
                    border: 2px solid #ddd; /* Opcional: añade un borde sutil */
                    padding: 1px;   /* Opcional: añade un poco de espacio alrededor */
                    box-sizing: border-box; /* Incluye el padding y border en el ancho total */
                }
            </style>
            
            <!-- Encabezado del comprobante (solo visible al imprimir) -->
            <div class="comprobante-header d-none d-print-block">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo Construye Verde">
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Datos del Cliente</h4>
                    @if($venta->clienteNatural)
                    <p><strong>Nombre:</strong> {{ $venta->clienteNatural->nomClieNat }} {{ $venta->clienteNatural->apelClieNat }}</p>
                    <p><strong>Documento:</strong> {{ $venta->clienteNatural->docIdenClieNat }}</p>
                    @elseif($venta->clienteJuridica)
                    <p><strong>Razón Social:</strong> {{ $venta->clienteJuridica->razSociClieJuri }}</p>
                    <p><strong>RUC:</strong> {{ $venta->clienteJuridica->rucClieJuri }}</p>
                    <p><strong>Dirección Fiscal:</strong> {{ $venta->clienteJuridica->dirfiscClieJuri }}</p>
                    @else
                    <p style="text-transform: uppercase;">Cliente sin datos</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4>Datos de la Venta</h4>
                    <p><strong>Vendedor:</strong> {{ $venta->usuario->empleado->nomEmp }} {{ $venta->usuario->empleado->apelEmp }}</p>
                    <p><strong>Método de Pago:</strong> {{ ucfirst($venta->metPagVent) }}</p>
                    <p><strong>Comprobante:</strong> {{ ucfirst($venta->comprobantes->first()->tipCompr ?? 'N/A') }}</p>
                    @if($venta->codSunatVent)
                    <p><strong>Código SUNAT:</strong> {{ $venta->codSunatVent }}</p>
                    @endif
                </div>
            </div>
            
            <h4>Productos</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Descuento</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->detalleVentas as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nomProd }}</td>
                        <td>
                            @php
                                // Calcula la cantidad SIN considerar descuento
                                $cantidadSinDescuento = $detalle->prec_uni != 0 
                                    ? round(($detalle->subtotal / (1 - ($detalle->descuento ?? 0)/100)) / $detalle->prec_uni)
                                    : 0;
                            @endphp
                            {{ $cantidadSinDescuento }}
                        </td>
                        <td>S/ {{ number_format($detalle->prec_uni, 2) }}</td>
                        <td>{{ $detalle->descuento ?? 0 }} %</td>
                        <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-right">Subtotal:</td>
                        <td>S/ {{ number_format($venta->totalVent / 1.18, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="text-right">IGV (18%):</td>
                        <td>S/ {{ number_format($venta->totalVent - ($venta->totalVent / 1.18), 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="text-right">Total:</td>
                        <td>S/ {{ number_format($venta->totalVent, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Pie de página del comprobante -->
            <div class="d-none d-print-block" style="margin-top: 30px; font-size: 10px; text-align: center;">
                <p>¡Gracias por su compra!</p>
                <p>Teléfono: (01) 123-4567 | Email: info@construyeverde.com</p>
            </div>
        </div>
        
        <div class="card-footer text-right no-print">
            <button class="btn btn-primary" onclick="printComprobante()">Imprimir Comprobante</button>
            <a href="{{ route('admin.ventas') }}" class="btn btn-secondary">Volver</a>
            <a href="{{ route('admin.devoluciones.create', $venta->IDVent) }}" class="btn btn-warning">Registrar Devolución</a>
        </div>
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