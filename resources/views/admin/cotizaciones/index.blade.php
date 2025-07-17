@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Generar Cotización</h1>
    
    <form action="{{ route('admin.cotizaciones.generar') }}" method="POST">
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
            </div>
            
            <div class="col-md-6">
                <!-- Grupo para clientes registrados (naturales/jurídicos) -->
                <div class="form-group" id="cliente-registrado-group">
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
                
                <!-- Campos para nuevo cliente natural -->
                <div id="nuevo-cliente-natural" class="mt-3" style="display: none;">
                    <h5>Registrar Nuevo Cliente Natural</h5>
                    <div class="form-group">
                        <label for="docIdenClieNat">DNI</label>
                        <input type="text" class="form-control" id="docIdenClieNat" name="docIdenClieNat">
                        <small class="text-muted">Ingrese los 8 dígitos del DNI</small>
                        <div id="dniLoading" class="spinner-border text-primary d-none" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nomClieNat">Nombres</label>
                        <input type="text" class="form-control" id="nomClieNat" name="nomClieNat">
                    </div>
                    <div class="form-group">
                        <label for="apelClieNat">Apellidos</label>
                        <input type="text" class="form-control" id="apelClieNat" name="apelClieNat">
                    </div>
                </div>
                
                <!-- Campos para nuevo cliente jurídico -->
                <div id="nuevo-cliente-juridico" class="mt-3" style="display: none;">
                    <h5>Registrar Nuevo Cliente Jurídico</h5>
                    <div class="form-group">
                        <label for="rucClieJuri">RUC</label>
                        <input type="text" class="form-control" id="rucClieJuri" name="rucClieJuri">
                        <small class="text-muted">Ingrese los 11 dígitos del RUC</small>
                        <div id="rucLoading" class="spinner-border text-primary d-none" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="razSociClieJuri">Razón Social</label>
                        <input type="text" class="form-control" id="razSociClieJuri" name="razSociClieJuri">
                    </div>
                    <div class="form-group">
                        <label for="dirfiscClieJuri">Dirección Fiscal</label>
                        <input type="text" class="form-control" id="dirfiscClieJuri" name="dirfiscClieJuri">
                    </div>
                </div>
                
                <button type="button" id="btn-nuevo-cliente" class="btn btn-sm btn-info mt-2">Nuevo Cliente</button>
                <button type="button" id="btn-cancelar-nuevo" class="btn btn-sm btn-secondary mt-2" style="display: none;">Cancelar</button>
            </div>
        </div>
        
        <div class="form-group">
        <label>Productos</label>
        <div id="productos-container">
            <div class="row producto-item mb-2">
                <div class="col-md-5">
                    <select class="form-control producto-select" name="productos[0][IDProd]" required>
                        <option value="">Seleccionar Producto</option>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->IDProd }}" data-precio="{{ $producto->precUniProd }}" data-stock="{{ $producto->stockProd }}" data-desc="{{ $producto->descProd }}">
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
                    <input type="number" class="form-control descuento" name="productos[0][descuento]" min="0" max="100" value="0" step="0.01">
                    <small class="text-muted">% Desc.</small>
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
        
        <div class="form-group">
            <label for="observaciones">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Generar Cotización</button>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
    
    tipoClienteRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'natural') {
                clienteRegistradoGroup.style.display = 'block';
                clienteNoRegistradoGroup.style.display = 'none';
                naturalesGroup.style.display = 'block';
                juridicosGroup.style.display = 'none';
                
                // Ocultar formularios de nuevo cliente si están visibles
                if (document.getElementById('btn-nuevo-cliente').style.display === 'none') {
                    toggleNuevoCliente(false);
                }
            } else if (this.value === 'juridico') {
                clienteRegistradoGroup.style.display = 'block';
                clienteNoRegistradoGroup.style.display = 'none';
                naturalesGroup.style.display = 'none';
                juridicosGroup.style.display = 'block';
                
                // Ocultar formularios de nuevo cliente si están visibles
                if (document.getElementById('btn-nuevo-cliente').style.display === 'none') {
                    toggleNuevoCliente(false);
                }
            } else {
                clienteRegistradoGroup.style.display = 'none';
                clienteNoRegistradoGroup.style.display = 'block';
                naturalesGroup.style.display = 'none';
                juridicosGroup.style.display = 'none';
                
                // Ocultar formularios de nuevo cliente si están visibles
                toggleNuevoCliente(false);
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
            const descuento = e.target.selectedOptions[0].dataset.desc || 0;
            const cantidadInput = e.target.closest('.producto-item').querySelector('.cantidad');
            const descuentoInput = e.target.closest('.producto-item').querySelector('.descuento');
            const subtotalInput = e.target.closest('.producto-item').querySelector('.subtotal');
            const stockDisplay = e.target.closest('.producto-item').querySelector('.stock-display');
            
            stockDisplay.textContent = `Stock: ${stock}`;
            descuentoInput.value = descuento;
            
            if (precio) {
                const cantidad = cantidadInput.value || 1;
                const descuentoVal = descuentoInput.value || 0;
                const precioConDescuento = precio * (1 - descuentoVal / 100);
                subtotalInput.value = `S/ ${(cantidad * precioConDescuento).toFixed(2)}`;
            } else {
                subtotalInput.value = 'S/ 0.00';
            }
        }
    });

    // Cambio en cantidad o descuento
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad') || e.target.classList.contains('descuento')) {
            const productoSelect = e.target.closest('.producto-item').querySelector('.producto-select');
            const precio = productoSelect.selectedOptions[0]?.dataset.precio;
            const subtotalInput = e.target.closest('.producto-item').querySelector('.subtotal');
            const cantidadInput = e.target.closest('.producto-item').querySelector('.cantidad');
            const descuentoInput = e.target.closest('.producto-item').querySelector('.descuento');
            
            if (precio) {
                const cantidad = cantidadInput.value || 0;
                const descuento = descuentoInput.value || 0;
                const precioConDescuento = precio * (1 - descuento / 100);
                subtotalInput.value = `S/ ${(cantidad * precioConDescuento).toFixed(2)}`;
            }
        }
    });
    
    // Mostrar/ocultar formulario de nuevo cliente
    document.getElementById('btn-nuevo-cliente').addEventListener('click', function() {
        toggleNuevoCliente(true);
    });
    
    document.getElementById('btn-cancelar-nuevo').addEventListener('click', function() {
        toggleNuevoCliente(false);
    });
    
    function toggleNuevoCliente(show) {
        const tipoCliente = document.querySelector('input[name="tipo_cliente"]:checked').value;
        const btnNuevo = document.getElementById('btn-nuevo-cliente');
        const btnCancelar = document.getElementById('btn-cancelar-nuevo');
        const clienteSelect = document.getElementById('cliente_id');
        
        if (show) {
            btnNuevo.style.display = 'none';
            btnCancelar.style.display = 'inline-block';
            clienteSelect.disabled = true;
            
            if (tipoCliente === 'natural') {
                document.getElementById('nuevo-cliente-natural').style.display = 'block';
                document.getElementById('nuevo-cliente-juridico').style.display = 'none';
            } else if (tipoCliente === 'juridico') {
                document.getElementById('nuevo-cliente-natural').style.display = 'none';
                document.getElementById('nuevo-cliente-juridico').style.display = 'block';
            }
        } else {
            btnNuevo.style.display = 'inline-block';
            btnCancelar.style.display = 'none';
            clienteSelect.disabled = false;
            document.getElementById('nuevo-cliente-natural').style.display = 'none';
            document.getElementById('nuevo-cliente-juridico').style.display = 'none';
            
            // Limpiar campos
            if (tipoCliente === 'natural') {
                document.getElementById('docIdenClieNat').value = '';
                document.getElementById('nomClieNat').value = '';
                document.getElementById('apelClieNat').value = '';
            } else {
                document.getElementById('rucClieJuri').value = '';
                document.getElementById('razSociClieJuri').value = '';
                document.getElementById('dirfiscClieJuri').value = '';
            }
        }
    }
    
    // Consulta DNI
    document.getElementById('docIdenClieNat').addEventListener('blur', async function() {
        const dni = this.value.trim();
        if (dni.length === 8) {
            const loading = document.getElementById('dniLoading');
            const nomInput = document.getElementById('nomClieNat');
            const apeInput = document.getElementById('apelClieNat');
            
            loading.classList.remove('d-none');
            
            try {
                const response = await fetch('{{ route("admin.consultar-dni") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ dni: dni })
                });

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Error desconocido');
                }

                // Asignar valores solo si existen
                if (data.nombres) nomInput.value = data.nombres;
                if (data.apellidos) apeInput.value = data.apellidos;
                
            } catch (error) {
                console.error('Error consultando DNI:', error);
                alert(`Error: ${error.message}`);
            } finally {
                loading.classList.add('d-none');
            }
        }
    });
    
    // Consulta RUC
    document.getElementById('rucClieJuri').addEventListener('blur', async function() {
        const ruc = this.value.trim();
        if (ruc.length === 11) {
            const loading = document.getElementById('rucLoading');
            const razonInput = document.getElementById('razSociClieJuri');
            const dirInput = document.getElementById('dirfiscClieJuri');
            
            loading.classList.remove('d-none');
            
            try {
                const response = await fetch('{{ route("admin.consultar-ruc") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ruc: ruc })
                });

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Error desconocido');
                }

                // Asignar valores
                if (data.razon_social) razonInput.value = data.razon_social;
                if (data.direccion) dirInput.value = data.direccion;
                
            } catch (error) {
                console.error('Error consultando RUC:', error);
                alert(`Error: ${error.message}`);
            } finally {
                loading.classList.add('d-none');
            }
        }
    });
});
</script>
@endsection