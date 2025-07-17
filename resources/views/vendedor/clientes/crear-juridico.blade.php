@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nuevo Cliente Jurídico</h1>
    
    <form action="{{ route('vendedor.clientes.store-juridico') }}" method="POST" id="formClienteJuridico">
        @csrf
        
        <div class="form-group">
            <label for="rucClieJuri">RUC</label>
            <input type="text" class="form-control" id="rucClieJuri" name="rucClieJuri" minlength="11" maxlength="11" required>
            <small class="text-muted">Ingrese los 11 dígitos del RUC</small>
            <div id="rucLoading" class="spinner-border text-primary d-none" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>
        
        <div class="form-group">
            <label for="razSociClieJuri">Razón Social</label>
            <input type="text" class="form-control" id="razSociClieJuri" name="razSociClieJuri" required>
        </div>
        
        <div class="form-group">
            <label for="dirfiscClieJuri">Dirección Fiscal</label>
            <input type="text" class="form-control" id="dirfiscClieJuri" name="dirfiscClieJuri" required>
        </div>
        
        <!-- Resto de campos -->      
        <button type="submit" class="btn btn-primary">Registrar</button>
        <a href="{{ route('vendedor.clientes.juridicos') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('rucClieJuri').addEventListener('blur', function() {
    const ruc = this.value.trim();
    if (ruc.length === 11) {
        const loading = document.getElementById('rucLoading');
        loading.classList.remove('d-none');
        
        fetch('{{ route("vendedor.consultar-ruc") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ruc: ruc })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta completa:', data); // Para depuración
            
            if (!data.success) {
                throw new Error(data.error || 'Error desconocido');
            }
            
            // Asignación segura de valores
            if (data.razon_social) {
                document.getElementById('razSociClieJuri').value = data.razon_social;
            }
            
            if (data.direccion) {
                document.getElementById('dirfiscClieJuri').value = data.direccion;
            }
            
            // Campo opcional
            const nomComField = document.getElementById('nomComClieJuri');
            if (nomComField && data.nombre_comercial) {
                nomComField.value = data.nombre_comercial;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Ocurrió un error al consultar el RUC');
        })
        .finally(() => {
            loading.classList.add('d-none');
        });
    }
});
</script>
@endsection