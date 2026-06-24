<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Inventario extends Model
{
    use HasFactory;
    protected $table = 'inventario';
    protected $fillable = [     
        'producto_id',
        'almacen_id',
        'cantidad',  
        'cantidad_entrada',
        'fecha',
        'tipoentrada',
        'numerofactura',
        'numerodonacion',
        'created_at',
        'updated_at',
        'fechavencimiento',
    ];
public function getInventario2(){
return DB::table('inventario')
->join('almacen', 'inventario.almacen_id', '=', 'almacen.id')
->join('producto', 'inventario.producto_id', '=', 'producto.id')
->select('inventario.id','inventario.cantidad as cantidad','inventario.cantidad_entrada as cantidad_entrada', 'producto.nombre as nombre','almacen.nombre as almacen','inventario.created_at as created_at','inventario.updated_at as updated_at')
->get();
}
  public function getInventario($producto){
    return DB::table('inventario')
    ->where('inventario.almacen_id', Auth::user()->almacen_id)
    ->where('inventario.producto_id', $producto)
    ->where ('inventario.cantidad', '>', 0)
    ->orderBy('inventario.almacen_id', 'asc')
    ->get();

  }
  public function getExistencia($producto)
{
    $usuario_id = Auth::user()->id;
    $almacen_id = DB::table('users')->where('id', $usuario_id)->value('almacen_id');

    $existencia = DB::table('inventario')
        ->where('producto_id', $producto)
        ->where('almacen_id', $almacen_id)
        ->sum('cantidad');

    return $existencia;
}

}