<?php

namespace App\Models\Direccion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Direccion extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',

    ];
    public function datos_direccion(){
        try {
            $direccion = DB::table('sala_unidad')->select('id','nombre')->orderBy('id')->pluck('nombre', 'id')->toArray();
            return $direccion;
        }catch(Throwable $e){
            $direccion = [];
            return $direccion;
        }

    }
    public function getEmail($direccion){
        return DB::table('direccion')->select('correo')->where('id', $direccion)->get();
    }
}
