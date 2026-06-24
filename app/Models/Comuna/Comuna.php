<?php

namespace App\Models\Comuna;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Comuna extends Model
{
    use HasFactory;
    protected $table = 'comuna';
    protected $fillable = [
        'nombre',

    ];
    public function datos_comuna($parroquia){
        try {
            $comuna = DB::table('comuna')
            ->leftJoin('parroquia', 'comuna.parroquia_id', '=', 'parroquia.id')
            ->select('comuna.id','comuna.codigo')
            ->where('comuna.parroquia_id', '=',$parroquia)
            ->get();
            return $comuna;
        }catch(Throwable $e){
            $comuna = [];
            return $comuna;
        }

    }
    public function getComunas(){
        $comunas = DB::table('comuna')
        ->select('comuna.id','comuna.codigo')
        ->get();
        return $comunas;
    }
    public function getComunasFilter(){
        $comunas = DB::table('comuna')
        ->select('comuna.id','comuna.codigo')
        ->orderBy('comuna.id')
        ->pluck( 'comuna.codigo', 'comuna.id')
        ->toArray();
        return $comunas;
    }
}

