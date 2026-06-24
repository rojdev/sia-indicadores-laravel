<?php

namespace App\Models\SolicitudMovimiento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class SolicitudMovimiento extends Model
{
    use HasFactory;
    protected $table = 'solicitudmovimiento';
    protected $fillable = [     
        'solicitud_id',
        'producto_id',
        'inventario_id',  
        'cantidad',
        'fecha',
        'servicio_id',
        'created_at',
        'updated_at',
    ];

  public function getSolicitudMovimiento(){
    return DB::table('solicitudmovimiento')->get();
  }
  public function getMovimiento($solicitudid){
    return DB::table('solicitudmovimiento')
    ->leftjoin('producto', 'producto.id', '=', 'solicitudmovimiento.producto_id')
    ->leftjoin('servicio', 'servicio.id', '=', 'solicitudmovimiento.servicio_id')
    ->select('solicitudmovimiento.id','solicitudmovimiento.servicio_id AS servicio_id','solicitudmovimiento.producto_id as producto_id','servicio.nombre AS servicio','producto.nombre AS producto','solicitudmovimiento.cantidad','solicitudmovimiento.fecha')
    ->where('solicitud_id', $solicitudid)->get();
  }
}