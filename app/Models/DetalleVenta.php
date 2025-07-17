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
        'descuento',
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
    public function getCantidadAttribute()
    {
        if ($this->prec_uni > 0) {
            return intval($this->subtotal / $this->prec_uni);
        }
        return 0;
    }
}