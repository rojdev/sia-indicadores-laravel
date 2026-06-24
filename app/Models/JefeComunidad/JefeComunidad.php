<?php

namespace App\Models\JefeComunidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JefeComunidad extends Model
{
    protected $table = 'jefecomunidad2';
    protected $fillable = [
        'comunidad_id',
        'nombre_consejo_comunal',
        'nombre_jefe_comunidad',
        'telefono_jefe_comunidad',
        'nombre_jefe_ubch',
        'telefono_jefe_ubch',
        'nombre_ubch',
    ];
    public function getJefe($comunidad_id){
        $resultados = DB::table('jefecomunidad2')
        ->leftJoin('comunidad', 'jefecomunidad2.comunidad_id', '=', 'comunidad.id')
        ->where('jefecomunidad2.comunidad_id', '=', $comunidad_id)
        ->select(
            'jefecomunidad2.nombre_jefe_comunidad AS Nombre_Jefe_Comunidad',
            'jefecomunidad2.id AS id',
            'jefecomunidad2.telefono_jefe_comunidad AS Telefono_Jefe_Comunidad',
            'jefecomunidad2.nombre_ubch AS Nombre_Ubch',
            'jefecomunidad2.nombre_jefe_ubch AS Nombre_Jefe_Ubch',
            'jefecomunidad2.telefono_jefe_ubch AS Telefono_Jefe_Ubch',)
        ->get();
        return $resultados;
        }
        public function getJefe2($jefecomunidadID){
            $resultados = DB::table('jefecomunidad2')
            ->where('jefecomunidad2.id', '=', $jefecomunidadID)
            ->select(
                'jefecomunidad2.id AS id',
                'jefecomunidad2.nombre_jefe_comunidad AS Nombre_Jefe_Comunidad',
                'jefecomunidad2.telefono_jefe_comunidad AS Telefono_Jefe_Comunidad',
                'jefecomunidad2.nombre_ubch AS Nombre_Ubch',
                'jefecomunidad2.nombre_jefe_ubch AS Nombre_Jefe_Ubch',
                'jefecomunidad2.telefono_jefe_ubch AS Telefono_Jefe_Ubch',)
            ->get();
            return $resultados;
    }
    use HasFactory;
}
