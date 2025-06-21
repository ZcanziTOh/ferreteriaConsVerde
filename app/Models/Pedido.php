<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    protected $primaryKey = 'IDPed';

    protected $fillable = [
        'fechPed',
        'totalProd',
        'fechEntrPed',
        'estadPed',
        'IDprov',
        'IDEmp'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IDprov');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'IDEmp');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'IDPed');
    }
}