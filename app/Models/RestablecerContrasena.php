<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestablecerContrasena extends Model
{
    use HasFactory;

    //Nombre de la tabla referencial
    protected $table = 'restablecer_contrasena';

    //Llave primaria
    protected $primaryKey = 'id';

    //Incrementar la llave primaria
    public $incrementing = false;

    //Tipo de llave primaria
    protected $keyType = 'string';

    //Campos para agregar masivamente
    protected $fillable = [
        'id', 'usuario_uuid', 'correo_electronico', 'revocado'
    ];

    //Campos de fechas
    public $timestamps = [
        'created_at', ' updated_at'
    ];
}
