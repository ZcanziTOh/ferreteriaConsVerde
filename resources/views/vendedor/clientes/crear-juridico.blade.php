@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nuevo Cliente Jurídico</h1>
    
    <form action="{{ route('vendedor.clientes.store-juridico') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="raz_socCieJuri">Razón Social</label>
            <input type="text" class="form-control" id="raz_socCieJuri" name="raz_socCieJuri" required>
        </div>
        
        <div class="form-group">
            <label for="dirfiscCieJuri">Dirección Fiscal</label>
            <input type="text" class="form-control" id="dirfiscCieJuri" name="dirfiscCieJuri" required>
        </div>
        
        <div class="form-group">
            <label for="rucCieJuri">RUC</label>
            <input type="text" class="form-control" id="rucCieJuri" name="rucCieJuri" minlength="11" maxlength="11" required>
        </div>
        
        <div class="form-group">
            <label for="nomComClieJuri">Nombre Comercial (Opcional)</label>
            <input type="text" class="form-control" id="nomComClieJuri" name="nomComClieJuri">
        </div>
        
        <div class="form-group">
            <label for="persRespClieJuri">Persona Responsable (Opcional)</label>
            <input type="text" class="form-control" id="persRespClieJuri" name="persRespClieJuri">
        </div>
        
        <div class="form-group">
            <label for="rubrCieJuri">Rubro (Opcional)</label>
            <input type="text" class="form-control" id="rubrCieJuri" name="rubrCieJuri">
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar</button>
        <a href="{{ route('vendedor.clientes.juridicos') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection