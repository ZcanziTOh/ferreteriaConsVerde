<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;

    protected $table = 'detalle_pedido';
    protected $primaryKey = 'IDdetalle';

    protected $fillable = [
        'cant',
        'precUni',
        'nomProd',
        'IDPed',
        'IDProd'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'IDPed');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'IDProd');
    }
}