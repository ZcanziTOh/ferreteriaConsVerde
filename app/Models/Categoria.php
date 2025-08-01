<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'IDCat';

    protected $fillable = [
        'nomCat',
        'descCat'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'IDCat');
    }
}