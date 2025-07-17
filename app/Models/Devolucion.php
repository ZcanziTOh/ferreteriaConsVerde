<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devoluciones';
    protected $primaryKey = 'IDDev';

    protected $fillable = [
        'fechDev',
        'motivDev',
        'totalRembDev',
        'IDUsu',
        'IDVent'
    ];
    protected $casts = [
        'fechDev' => 'datetime'
    ];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'IDUsu');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'IDVent');
    }
}