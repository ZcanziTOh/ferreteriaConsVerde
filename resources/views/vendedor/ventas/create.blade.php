@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nueva Venta</h1>
    
    <form action="{{ route('vendedor.ventas.store') }}" method="POST">
        @csrf
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tipo de Cliente</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="natural" value="natural" checked>
                        <label class="form-check-label" for="natural">Natural</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="juridico" value="juridico">
                        <label class="form-check-label" for="juridico">Jurídico</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="none" value="none">
                        <label class="form-check-label" for="none">Sin cliente</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cliente_id">Seleccionar Cliente</label>
                    <select class="form-control" id="cliente_id" name="cliente_id">
                        <option value="">-- Seleccione --</option>
                        <optgroup label="Clientes Naturales" id="naturales-group">
                            @foreach($clientesNaturales as $cliente)
                            <option value="N-{{ $cliente->IDClieNat }}">{{ $cliente->nomClieNat }} {{ $cliente->apelClieNat }} - {{ $cliente->docIdenClieNat }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Clientes Jurídicos" id="juridicos-group">
                            @foreach($clientesJuridicos as $cliente)
                            <option value="J-{{ $cliente->IDClieJuri }}">{{ $cliente->razSociClieJuri }} - {{ $cliente->rucClieJuri }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="metPagVent">Método de Pago</label>
                    <select class="form-control" id="metPagVent" name="metPagVent" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="yape">Yape</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tipo_comprobante">Tipo de Comprobante</label>
                    <select class="form-control" id="tipo_comprobante" name="tipo_comprobante" required>
                        <option value="boleta">Boleta</option>
                        <option value="factura">Factura</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Productos</label>
            <div id="productos-container">
                <div class="row producto-item mb-2">
                    <div class="col-md-6">
                        <select class="form-control producto-select" name="productos[0][IDProd]" required>
                            <option value="">Seleccionar Producto</option>
                            @foreach($productos as $producto)
                            <option value="{{ $producto->IDProd }}" data-precio="{{ $producto->precUniProd }}" data-stock="{{ $producto->stockProd }}">
                                {{ $producto->nomProd }} (S/ {{ number_format($producto->precUniProd, 2) }}) - Stock: {{ $producto->stockProd }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control cantidad" name="productos[0][cantidad]" min="1" value="1" required>
                        <small class="text-muted stock-display">Stock: 0</small>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control subtotal" readonly value="S/ 0.00">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm btn-eliminar">X</button>
                    </div>
                </div>
            </div>
            <button type="button" id="agregar-producto" class="btn btn-sm btn-secondary mt-2">Agregar Producto</button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Subtotal:</label>
                    <input type="text" class="form-control" id="subtotal" readonly value="S/ 0.00">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>IGV (18%):</label>
                    <input type="text" class="form-control" id="igv" readonly value="S/ 0.00">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Total:</label>
            <input type="text" class="form-control" id="total" readonly value="S/ 0.00">
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar Venta</button>
        <a href="{{ route('vendedor.ventas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar grupos de clientes según tipo seleccionado
    const tipoClienteRadios = document.querySelectorAll('input[name="tipo_cliente"]');
    const clienteSelect = document.getElementById('cliente_id');
    const tipoComprobante = document.getElementById('tipo_comprobante');
    
    tipoClienteRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'natural') {
                document.getElementById('naturales-group').style.display = 'block';
                document.getElementById('juridicos-group').style.display = 'none';
            } else if (this.value === 'juridico') {
                document.getElementById('naturales-group').style.display = 'none';
                document.getElementById('juridicos-group').style.display = 'block';
                tipoComprobante.value = 'factura';
            } else {
                document.getElementById('naturales-group').style.display = 'none';
                document.getElementById('juridicos-group').style.display = 'none';
                clienteSelect.value = '';
                tipoComprobante.value = 'boleta';
            }
        });
    });
    
    // Inicializar mostrando solo naturales
    document.getElementById('juridicos-group').style.display = 'none';
    
    // Agregar nuevo producto
    let contador = 1;
    document.getElementById('agregar-producto').addEventListener('click', function() {
        const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
        nuevoProducto.innerHTML = nuevoProducto.innerHTML.replace(/\[0\]/g, `[${contador}]`);
        nuevoProducto.querySelector('.producto-select').value = '';
        nuevoProducto.querySelector('.cantidad').value = 1;
        nuevoProducto.querySelector('.subtotal').value = 'S/ 0.00';
        nuevoProducto.querySelector('.stock-display').textContent = 'Stock: 0';
        document.getElementById('productos-container').appendChild(nuevoProducto);
        contador++;
    });
    
    // Eliminar producto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-eliminar')) {
            if (document.querySelectorAll('.producto-item').length > 1) {
                e.target.closest('.producto-item').remove();
                calcularTotales();
            } else {
                alert('Debe haber al menos un producto');
            }
        }
    });
    
    // Cambio en selección de producto
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const precio = e.target.selectedOptions[0].dataset.precio;
            const stock = e.target.selectedOptions[0].dataset.stock;
            const cantidadInput = e.target.closest('.producto-item').querySelector('.cantidad');
            const subtotalInput = e.target.closest('.producto-item').querySelector('.subtotal');
            const stockDisplay = e.target.closest('.producto-item').querySelector('.stock-display');
            
            stockDisplay.textContent = `Stock: ${stock}`;
            
            if (precio) {
                const cantidad = cantidadInput.value || 1;
                subtotalInput.value = `S/ ${(cantidad * precio).toFixed(2)}`;
                calcularTotales();
            } else {
                subtotalInput.value = 'S/ 0.00';
            }
        }
    });
    
    // Cambio en cantidad
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad')) {
            const productoSelect = e.target.closest('.producto-item').querySelector('.producto-select');
            const precio = productoSelect.selectedOptions[0]?.dataset.precio;
            const subtotalInput = e.target.closest('.producto-item').querySelector('.subtotal');
            
            if (precio) {
                const cantidad = e.target.value || 0;
                subtotalInput.value = `S/ ${(cantidad * precio).toFixed(2)}`;
                calcularTotales();
            }
        }
    });
    
    // Calcular totales
    function calcularTotales() {
        let subtotal = 0;
        
        document.querySelectorAll('.producto-item').forEach(item => {
            const subtotalText = item.querySelector('.subtotal').value;
            const valor = parseFloat(subtotalText.replace('S/ ', '')) || 0;
            subtotal += valor;
        });
        
        const igv = subtotal * 0.18;
        const total = subtotal + igv;
        
        document.getElementById('subtotal').value = `S/ ${subtotal.toFixed(2)}`;
        document.getElementById('igv').value = `S/ ${igv.toFixed(2)}`;
        document.getElementById('total').value = `S/ ${total.toFixed(2)}`;
    }
});
</script>
@endsection