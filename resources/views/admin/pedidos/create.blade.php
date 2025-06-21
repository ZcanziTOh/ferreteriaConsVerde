@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Pedido</h1>
    
    <form action="{{ route('admin.pedidos.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fechPed">Fecha</label>
                    <input type="date" class="form-control" id="fechPed" name="fechPed" required value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fechEntrPed">Fecha Entrega (opcional)</label>
                    <input type="date" class="form-control" id="fechEntrPed" name="fechEntrPed">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="IDprov">Proveedor</label>
                    <select class="form-control" id="IDprov" name="IDprov" required>
                        @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->IDprov }}">{{ $proveedor->razonSocialProv }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="IDEmp">Empleado</label>
                    <select class="form-control" id="IDEmp" name="IDEmp" required>
                        @foreach($empleados as $empleado)
                        <option value="{{ $empleado->IDEmp }}">{{ $empleado->nomEmp }} {{ $empleado->apelEmp }}</option>
                        @endforeach
                    </select>
                </div>
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
                            <option value="{{ $producto->IDProd }}" data-precio="{{ $producto->precUniProd }}">
                                {{ $producto->nomProd }} (S/ {{ number_format($producto->precUniProd, 2) }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control cantidad" name="productos[0][cant]" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" class="form-control precio" name="productos[0][precUni]" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm btn-eliminar">Eliminar</button>
                    </div>
                </div>
            </div>
            <button type="button" id="agregar-producto" class="btn btn-sm btn-secondary mt-2">Agregar Producto</button>
        </div>
        
        <div class="form-group">
            <label>Total: S/ <span id="total-pedido">0.00</span></label>
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar Pedido</button>
        <a href="{{ route('admin.pedidos') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar nuevo producto
    let contador = 1;
    document.getElementById('agregar-producto').addEventListener('click', function() {
        const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
        nuevoProducto.innerHTML = nuevoProducto.innerHTML.replace(/\[0\]/g, `[${contador}]`);
        nuevoProducto.querySelector('.precio').value = '';
        nuevoProducto.querySelector('.cantidad').value = 1;
        document.getElementById('productos-container').appendChild(nuevoProducto);
        contador++;
    });

    // Eliminar producto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-eliminar')) {
            if (document.querySelectorAll('.producto-item').length > 1) {
                e.target.closest('.producto-item').remove();
                calcularTotal();
            } else {
                alert('Debe haber al menos un producto');
            }
        }
    });

    // Cambio en selección de producto
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const precio = e.target.selectedOptions[0].dataset.precio;
            const precioInput = e.target.closest('.producto-item').querySelector('.precio');
            precioInput.value = precio ? precio : '';
            calcularTotal();
        }
    });

    // Cambio en cantidad o precio
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad') || e.target.classList.contains('precio')) {
            calcularTotal();
        }
    });

    // Calcular total
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.producto-item').forEach(item => {
            const cantidad = parseFloat(item.querySelector('.cantidad').value) || 0;
            const precio = parseFloat(item.querySelector('.precio').value) || 0;
            total += cantidad * precio;
        });
        document.getElementById('total-pedido').textContent = total.toFixed(2);
    }
});
</script>
@endsection