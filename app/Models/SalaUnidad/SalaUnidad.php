<?php

namespace App\Models\SalaUnidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class SalaUnidad extends Model
{
    use HasFactory;
    protected $table = 'sala_unidad';
    protected $fillable = [
        'id',
        'nombre',
    ];

    public function getcorrnomen($id){
        $corrnomen = DB::table('sala_unidad')
            ->select('correlativo as nomcorrelativo')
            ->where('id', '=', $id)
            ->get();

        return $corrnomen;
    }

    public function getallUnidades(){
        $unidades = DB::table('sala_unidad')
            ->select('id','sala_unidad.nombre')
            ->where('id', '!=', 1)
            ->pluck('nombre', 'id');

        return $unidades;
    }
    public function getUnidadbyId($id){
        $unidades = DB::table('sala_unidad')
            ->select('id as id','sala_unidad.nombre as nombre')
            ->where('id', '=', $id)
            ->get();

        return $unidades;
    }
}
