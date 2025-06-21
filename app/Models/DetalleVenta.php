<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_venta';
    protected $primaryKey = 'IDDetall_vent';

    protected $fillable = [
        'prec_uni',
        'subtotal',
        'IDProd',
        'IDVent'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'IDProd');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'IDVent');
    }
}