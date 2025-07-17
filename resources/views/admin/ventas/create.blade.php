@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nueva Venta</h1>
    
    <form action="{{ route('admin.ventas.store') }}" method="POST" id="ventaForm">
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
                        <label class="form-check-label" for="none">N/A</label>
                    </div>
                </div>
                
                <!-- Grupo para clientes registrados (naturales/jurídicos) -->
                <div class="form-group" id="cliente-registrado-group">
                    <label for="cliente_id">Seleccionar Cliente</label>
                    <select class="form-control" id="cliente_id" name="cliente_id" required>
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
                
                <!-- Grupo para cliente no registrado (N/A) -->
                <div id="cliente-no-registrado-group" style="display: none;">
                    <div class="form-group">
                        <label for="nombre_cliente">Nombre del Cliente</label>
                        <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" placeholder="Ingrese nombre">
                    </div>
                    <div class="form-group">
                        <label for="apellido_cliente">Apellido del Cliente</label>
                        <input type="text" class="form-control" id="apellido_cliente" name="apellido_cliente" placeholder="Ingrese apellido">
                    </div>
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
                        <option value="proforma">Proforma</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Resto del formulario se mantiene igual -->
        <div class="form-group">
            <label>Productos</label>
            <div id="productos-container">
                <div class="row producto-item mb-2">
                    <div class="col-md-5">
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
                    <div class="col-md-2">
                        <input type="number" class="form-control descuento" name="productos[0][descuento]" min="0" max="100" value="0" placeholder="%">
                        <small class="text-muted stock-display">Desc. (%)</small>
                    </div>
                    <div class="col-md-2">
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
        <a href="{{ route('admin.ventas') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar grupos de clientes según tipo seleccionado
    const tipoClienteRadios = document.querySelectorAll('input[name="tipo_cliente"]');
    const clienteRegistradoGroup = document.getElementById('cliente-registrado-group');
    const clienteNoRegistradoGroup = document.getElementById('cliente-no-registrado-group');
    const naturalesGroup = document.getElementById('naturales-group');
    const juridicosGroup = document.getElementById('juridicos-group');
    const tipoComprobante = document.getElementById('tipo_comprobante');
    const clienteIdSelect = document.getElementById('cliente_id');
    const nombreClienteInput = document.getElementById('nombre_cliente');
    const apellidoClienteInput = document.getElementById('apellido_cliente');
    const ventaForm = document.getElementById('ventaForm');
    
    // Función para manejar el cambio de tipo de cliente
    function handleTipoClienteChange() {
        const tipoSeleccionado = document.querySelector('input[name="tipo_cliente"]:checked').value;
        
        if (tipoSeleccionado === 'natural') {
            clienteRegistradoGroup.style.display = 'block';
            clienteNoRegistradoGroup.style.display = 'none';
            naturalesGroup.style.display = 'block';
            juridicosGroup.style.display = 'none';
            tipoComprobante.value = 'boleta';
            
            // Configurar requeridos
            clienteIdSelect.required = true;
            nombreClienteInput.required = false;
            apellidoClienteInput.required = false;
            
        } else if (tipoSeleccionado === 'juridico') {
            clienteRegistradoGroup.style.display = 'block';
            clienteNoRegistradoGroup.style.display = 'none';
            naturalesGroup.style.display = 'none';
            juridicosGroup.style.display = 'block';
            tipoComprobante.value = 'factura';
            
            // Configurar requeridos
            clienteIdSelect.required = true;
            nombreClienteInput.required = false;
            apellidoClienteInput.required = false;
            
        } else {
            clienteRegistradoGroup.style.display = 'none';
            clienteNoRegistradoGroup.style.display = 'block';
            naturalesGroup.style.display = 'none';
            juridicosGroup.style.display = 'none';
            tipoComprobante.value = 'proforma';
            
            // Configurar requeridos
            clienteIdSelect.required = false;
            nombreClienteInput.required = true;
            apellidoClienteInput.required = true;
        }
    }
    
    // Configurar eventos
    tipoClienteRadios.forEach(radio => {
        radio.addEventListener('change', handleTipoClienteChange);
    });
    
    // Inicializar mostrando solo naturales
    document.getElementById('juridicos-group').style.display = 'none';
    handleTipoClienteChange(); // Aplicar configuración inicial
    
    // Validación personalizada del formulario
    ventaForm.addEventListener('submit', function(e) {
        const tipoSeleccionado = document.querySelector('input[name="tipo_cliente"]:checked').value;
        
        if (tipoSeleccionado === 'natural' || tipoSeleccionado === 'juridico') {
            if (clienteIdSelect.value === '') {
                e.preventDefault();
                alert('Por favor seleccione un cliente');
                clienteIdSelect.focus();
                return false;
            }
        } else {
            if (nombreClienteInput.value === '' || apellidoClienteInput.value === '') {
                e.preventDefault();
                alert('Por favor complete los datos del cliente');
                if (nombreClienteInput.value === '') {
                    nombreClienteInput.focus();
                } else {
                    apellidoClienteInput.focus();
                }
                return false;
            }
        }
        
        return true;
    });
    
    // Resto del JavaScript se mantiene igual
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
    
    // Actualiza la función que maneja el cambio en selección de producto
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const precio = e.target.selectedOptions[0].dataset.precio;
            const stock = e.target.selectedOptions[0].dataset.stock;
            const cantidadInput = e.target.closest('.producto-item').querySelector('.cantidad');
            const descuentoInput = e.target.closest('.producto-item').querySelector('.descuento');
            const subtotalInput = e.target.closest('.producto-item').querySelector('.subtotal');
            const stockDisplay = e.target.closest('.producto-item').querySelector('.stock-display');
            
            stockDisplay.textContent = `Stock: ${stock}`;
            
            if (precio) {
                const cantidad = cantidadInput.value || 1;
                const descuento = descuentoInput.value || 0;
                const subtotalSinDescuento = cantidad * precio;
                const montoDescuento = subtotalSinDescuento * (descuento / 100);
                subtotalInput.value = `S/ ${(subtotalSinDescuento - montoDescuento).toFixed(2)}`;
                calcularTotales();
            } else {
                subtotalInput.value = 'S/ 0.00';
            }
        }
    });

    // Actualiza la función que maneja el cambio en cantidad y descuento
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad')) {
            const productoSelect = e.target.closest('.producto-item').querySelector('.producto-select');
            const precio = productoSelect.selectedOptions[0]?.dataset.precio;
            const descuentoInput = e.target.closest('.producto-item').querySelector('.descuento');
            const subtotalInput = e.target.closest('.producto-item').querySelector('.subtotal');
            
            if (precio) {
                const cantidad = e.target.value || 0;
                const descuento = descuentoInput.value || 0;
                const subtotalSinDescuento = cantidad * precio;
                const montoDescuento = subtotalSinDescuento * (descuento / 100);
                subtotalInput.value = `S/ ${(subtotalSinDescuento - montoDescuento).toFixed(2)}`;
                calcularTotales();
            }
        }
        
        if (e.target.classList.contains('descuento')) {
            const productoSelect = e.target.closest('.producto-item').querySelector('.producto-select');
            const precio = productoSelect.selectedOptions[0]?.dataset.precio;
            const cantidadInput = e.target.closest('.producto-item').querySelector('.cantidad');
            const subtotalInput = e.target.closest('.producto-item').querySelector('.subtotal');
            
            if (precio) {
                const cantidad = cantidadInput.value || 0;
                const descuento = e.target.value || 0;
                const subtotalSinDescuento = cantidad * precio;
                const montoDescuento = subtotalSinDescuento * (descuento / 100);
                subtotalInput.value = `S/ ${(subtotalSinDescuento - montoDescuento).toFixed(2)}`;
                calcularTotales();
            }
        }
    });

    // Actualiza la función calcularTotales para incluir descuentos
    function calcularTotales() {
        let subtotal = 0;
        let totalDescuentos = 0;
        
        document.querySelectorAll('.producto-item').forEach(item => {
            const subtotalText = item.querySelector('.subtotal').value;
            const valor = parseFloat(subtotalText.replace('S/ ', '')) || 0;
            subtotal += valor;
            
            // Calcular descuentos
            const productoSelect = item.querySelector('.producto-select');
            const precio = productoSelect.selectedOptions[0]?.dataset.precio;
            const cantidad = item.querySelector('.cantidad').value || 0;
            const descuento = item.querySelector('.descuento').value || 0;
            
            if (precio && descuento > 0) {
                const subtotalSinDescuento = cantidad * precio;
                totalDescuentos += subtotalSinDescuento * (descuento / 100);
            }
        });
        
        const igv = subtotal * 0.18;
        const total = subtotal + igv;
        
        document.getElementById('subtotal').value = `S/ ${subtotal.toFixed(2)}`;
        document.getElementById('igv').value = `S/ ${igv.toFixed(2)}`;
        document.getElementById('total').value = `S/ ${total.toFixed(2)}`;
        
        // Opcional: Mostrar total de descuentos si lo deseas
        // document.getElementById('descuentos').value = `S/ ${totalDescuentos.toFixed(2)}`;
    }
});
</script>
@endsection