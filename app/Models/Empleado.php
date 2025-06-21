<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'IDEmp';

    protected $fillable = [
        'nomEmp',
        'apelEmp',
        'docIdenEmp',
        'telEmp',
        'dirEmp'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'IDEmp');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'IDEmp');
    }

    public function ventas()
    {
        return $this->hasManyThrough(Venta::class, User::class, 'IDEmp', 'IDUsu');
    }
}