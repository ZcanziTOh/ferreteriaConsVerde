document.addEventListener('DOMContentLoaded', function() {
    // Consultar RUC
    const rucInput = document.getElementById('ruc');
    if (rucInput) {
        rucInput.addEventListener('blur', function() {
            const ruc = this.value.trim();
            if (ruc.length === 11) {
                consultarSunat('ruc', ruc);
            }
        });
    }

    // Consultar DNI
    const dniInput = document.getElementById('docIdenClieNat');
    if (dniInput) {
        dniInput.addEventListener('blur', function() {
            const dni = this.value.trim();
            if (dni.length === 8) {
                consultarSunat('dni', dni);
            }
        });
    }
});

function consultarSunat(tipo, numero) {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const url = `/api/consultar-${tipo}?${tipo}=${numero}`;

    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            llenarDatosSunat(tipo, data.data);
        } else {
            console.error('Error al consultar:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function llenarDatosSunat(tipo, data) {
    if (tipo === 'ruc') {
        document.getElementById('razSociClieJuri').value = data.razonSocial || '';
        document.getElementById('dirfiscClieJuri').value = data.direccion || '';
        document.getElementById('nomComClieJuri').value = data.nombreComercial || '';
    } else if (tipo === 'dni') {
        document.getElementById('nomClieNat').value = data.nombres || '';
        document.getElementById('apelClieNat').value = `${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
    }
}