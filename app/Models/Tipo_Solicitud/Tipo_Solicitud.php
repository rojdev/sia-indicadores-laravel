<?php

namespace App\Models\Tipo_Solicitud;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Tipo_Solicitud extends Model
{
    use HasFactory;
    protected $fillable = [     
        'nombre',
        
    ];
    public function datos_tipo_solicitud(){
        try {
            $tipo_solicitud = DB::table('tipo_solicitud')->where('id', '<', 7)->select('id','nombre')->orderBy('id')->pluck('nombre', 'id')->toArray();
            return $tipo_solicitud;
        }catch(Throwable $e){
            $tipo_solicitud = [];
            return $tipo_solicitud;
        }
        
    }
    public function datos_tipo_solicitud_Tipo($tipo){
        try {
            if($tipo==1){
            $tipo_solicitud = DB::table('tipo_solicitud')
            ->where('id', '<', 4)
            ->select('id','nombre')
            ->orderBy('id')
            ->pluck('nombre', 'id')
            ->toArray();
            }else{
                $tipo_solicitud = DB::table('tipo_solicitud')
                ->where('id', '>', 3)
                ->where('id', '<', 6)
                ->select('id','nombre')
                ->orderBy('id')
                ->pluck('nombre', 'id')
                ->toArray();
            }
            return $tipo_solicitud;
        }catch(Throwable $e){
            $tipo_solicitud = [];
            return $tipo_solicitud;
        }
        
    }
}
