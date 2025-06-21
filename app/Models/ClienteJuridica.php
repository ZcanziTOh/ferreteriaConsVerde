<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteJuridica extends Model
{
    use HasFactory;

    protected $table = 'cliente_juridica';
    protected $primaryKey = 'IDClieJuri';

    protected $fillable = [
        'razSociClieJuri',
        'dirfiscClieJuri',
        'rucClieJuri',
        'nomComClieJuri',
        'perRespClieJuri',
        'rubrClieJuri'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'IDClieJuri');
    }
}