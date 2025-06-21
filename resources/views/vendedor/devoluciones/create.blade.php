@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Devolución para Venta #{{ $venta->IDVent }}</h1>
    
    <form action="{{ route('vendedor.devoluciones.store', $venta->IDVent) }}" method="POST">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                <h4>Información de la Venta</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Fecha:</strong> {{ $venta->fechVent->format('d/m/Y H:i') }}</p>
                        <p><strong>Total:</strong> S/ {{ number_format($venta->totalVent, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> 
                            @if($venta->clienteNatural)
                                {{ $venta->clienteNatural->nomClieNat }} {{ $venta->clienteNatural->apelClieNat }}
                            @elseif($venta->clienteJuridica)
                                {{ $venta->clienteJuridica->razSociClieJuri }}
                            @else
                                Sin cliente registrado
                            @endif
                        </p>
                        <p><strong>Comprobante:</strong> {{ ucfirst($venta->comprobantes->first()->tipCompr) }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="motivDev">Motivo de la Devolución</label>
            <textarea class="form-control" id="motivDev" name="motivDev" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Productos a Devolver</label>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Vendida</th>
                            <th>Cantidad a Devolver</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalleVentas as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nomProd }}</td>
                            <td>{{ $detalle->cant }}</td>
                            <td>
                                <input type="hidden" name="productos[{{ $loop->index }}][IDProd]" value="{{ $detalle->IDProd }}">
                                <input type="number" class="form-control cantidad-devolver" 
                                       name="productos[{{ $loop->index }}][cantidad]" 
                                       min="0" max="{{ $detalle->cant }}" 
                                       value="0" data-precio="{{ $detalle->prec_uni }}">
                            </td>
                            <td>S/ {{ number_format($detalle->prec_uni, 2) }}</td>
                            <td class="subtotal-devolucion">S/ 0.00</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total Reembolso:</th>
                            <th id="total-reembolso">S/ 0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar Devolución</button>
        <a href="{{ route('vendedor.ventas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular total de reembolso cuando cambia la cantidad
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad-devolver')) {
            const cantidad = parseInt(e.target.value) || 0;
            const precio = parseFloat(e.target.dataset.precio);
            const subtotal = cantidad * precio;
            
            const subtotalCell = e.target.closest('tr').querySelector('.subtotal-devolucion');
            subtotalCell.textContent = `S/ ${subtotal.toFixed(2)}`;
            
            calcularTotalReembolso();
        }
    });
    
    // Calcular el total de reembolso
    function calcularTotalReembolso() {
        let total = 0;
        
        document.querySelectorAll('.cantidad-devolver').forEach(input => {
            const cantidad = parseInt(input.value) || 0;
            const precio = parseFloat(input.dataset.precio);
            total += cantidad * precio;
        });
        
        document.getElementById('total-reembolso').textContent = `S/ ${total.toFixed(2)}`;
    }
});
</script>
@endsection