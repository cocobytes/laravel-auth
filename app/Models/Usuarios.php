<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Usuarios extends Model
{
    use HasFactory;

    //Nombre de la tabla referencial
    protected $table = 'usuarios';

    //Llave primaria
    protected $primaryKey = 'uuid';

    //Incrementar la llave primaria
    public $incrementing = false;

    //Tipo de llave primaria
    protected $keyType = 'string';

    //Campos para agregar masivamente
    protected $fillable = [
        'nombre', 'correo_electronico', 'contrasena', 'correo_verificado'
    ];

    //Ocultar elementos en la peticion
    protected $hidden = [
        'contrasena'
    ];

    // Creacion automatica del uuid al crear un usuario
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($usuario) {
            $usuario->uuid = Str::uuid();
        });
    }
}
