<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteNatural extends Model
{
    use HasFactory;

    protected $table = 'cliente_natural';
    protected $primaryKey = 'IDClieNat';

    protected $fillable = [
        'docIdenClieNat',
        'nomClieNat',
        'apelClieNat'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'IDClieNat');
    }
}