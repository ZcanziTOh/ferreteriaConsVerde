<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'comprobantes';
    protected $primaryKey = 'IDCompr';

    protected $fillable = [
        'tipCompr',
        'IDVent'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'IDVent');
    }
}