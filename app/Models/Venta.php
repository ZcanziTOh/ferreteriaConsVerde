<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'IDVent';

    protected $fillable = [
        'fechVent',
        'totalVent',
        'metPagVent',
        'codSunatVent',
        'IDClieNat',
        'IDClieJuri',
        'IDUsu'
    ];
    protected $casts = [
        'fechVent' => 'datetime'
    ];
    public function clienteNatural()
    {
        return $this->belongsTo(ClienteNatural::class, 'IDClieNat');
    }

    public function clienteJuridica()
    {
        return $this->belongsTo(ClienteJuridica::class, 'IDClieJuri');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'IDUsu');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'IDVent');
    }
    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'IDVent');
    }

    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class, 'IDVent');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'IDVent');
    }

}