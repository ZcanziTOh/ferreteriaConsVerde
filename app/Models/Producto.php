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
        'stockProd',
        'stockMinProd',
        'IDCat',
        'IDprov'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'IDCat');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IDprov');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'IDProd');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'IDProd');
    }
}