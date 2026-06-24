<?php

namespace App\Models\AccionesAuxiliar; // Ajusta el namespace si es necesario

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionesAuxiliar extends Model
{
    use HasFactory;

    protected $table = 'AccionesAuxiliar2';

    protected $fillable = [
        'accion_id',
        'nombre',
        'direccion_id',
        'coordinacion_sala_id',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'comuna_id',
        'comunidad_id',
        'jefecomunidad_id',
        'territorio_id',
        'direccion',
        'state',
        'cantidad',
        'avancePorcentual',
        'vocero',
        'telefono',
        'evidencia_path',
        'fechainicial',
        'fechafinal',
        'created_at',
        'updated_at',
        'evidencia_path2',
        'observacion',
    ];


}
