<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $primaryKey = 'IDprov';

    protected $fillable = [
        'razonSocialProv',
        'rucProv',
        'telProv',
        'emailProv'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'IDprov');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'IDprov');
    }
}