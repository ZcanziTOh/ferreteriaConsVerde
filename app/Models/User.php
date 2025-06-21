<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'IDUsu';

    protected $fillable = [
        'usuario',
        'contraUsu',
        'rolUsu',
        'IDEmp'
    ];

    protected $hidden = [
        'contraUsu',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->contraUsu;
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'IDEmp');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'IDUsu');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'IDUsu');
    }

    public function isAdmin()
    {
        return $this->rolUsu === 'admin';
    }

    public function isVendedor()
    {
        return $this->rolUsu === 'vendedor';
    }
}