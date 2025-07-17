@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nuevo Cliente Natural</h1>
    
    <form action="{{ route('vendedor.clientes.store-natural') }}" method="POST" id="formClienteNatural">
        @csrf
        
        <div class="form-group">
            <label for="docIdenClieNat">Documento de Identidad</label>
            <input type="text" class="form-control" id="docIdenClieNat" name="docIdenClieNat" required>
            <small class="text-muted">Ingrese los 8 dígitos del DNI</small>
            <div id="dniLoading" class="spinner-border text-primary d-none" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>
        
        <div class="form-group">
            <label for="nomClieNat">Nombres</label>
            <input type="text" class="form-control" id="nomClieNat" name="nomClieNat" required>
        </div>
        
        <div class="form-group">
            <label for="apelClieNat">Apellidos</label>
            <input type="text" class="form-control" id="apelClieNat" name="apelClieNat" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar</button>
        <a href="{{ route('vendedor.clientes.naturales') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('docIdenClieNat').addEventListener('blur', async function() {
    const dni = this.value.trim();
    if (dni.length === 8) {
        const loading = document.getElementById('dniLoading');
        const nomInput = document.getElementById('nomClieNat');
        const apeInput = document.getElementById('apelClieNat');
        
        loading.classList.remove('d-none');
        
        try {
            const response = await fetch('{{ route("vendedor.consultar-dni") }}', {
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
            
            // Opcional: Limpiar campos si falla
            nomInput.value = '';
            apeInput.value = '';
        } finally {
            loading.classList.add('d-none');
        }
    }
});
</script>
@endsection