<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'IDProd';

    protected $fillable = [
        'nomProd',
        'estProd',
        'uniMedProd',
        'precUniProd',
        'precUniComProd',
        'totalComp',
        'stockProd',
        'cantComProd',
        'stockMinProd',
        'IDCat',
        'IDprov'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'IDCat');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'IDVent');
    }
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'IDProd');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'IDProd');
    }
    protected static function boot()
    {
        parent::boot();

        // Calcula totalComp antes de guardar
        static::saving(function ($producto) {
            $producto->totalComp = round($producto->precUniComProd * $producto->cantComProd, 2);
        });
    }
}