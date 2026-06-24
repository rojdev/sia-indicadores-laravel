<?php

namespace App\Http\Controllers\Fecha;

use App\Models\Inventario\Inventario;
use App\Models\Fecha\Fecha;
use App\Models\Ajuste\Ajuste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User\User;
use App\Models\Security\Rol;
use App\Models\Producto\Producto;
use App\Models\Almacen\Almacen;
use App\Models\Categoria\Categoria;
use App\Http\Controllers\User\Colores;

class FechaController extends Controller
{
    public function index(){
        $count_notification = (new User)->count_noficaciones_user();

        $tipo_alert = "";
        if(session('delete') == true){
            $tipo_alert = "Delete";
            session(['delete' => false]);
        }
        if(session('update') == true ){

            $tipo_alert = "Update";
            session(['update' => false]);
        }
        $array_color = (new Colores)->getColores();
        return view('Fecha.Fecha',compact('count_notification','tipo_alert','array_color'));
    }



public function store(Request $request){

    $count_notification = (new User)->count_noficaciones_user();
   $input=$request->all();

    $ultimoRegistro = Fecha::where('state', 'ACTIVO')->latest()->first();
  if($ultimoRegistro){
    $ultimoRegistro->state ='VENCIDA';
    $ultimoRegistro->save();
  }

    $fecha = new Fecha([
                    'fecha' =>$request->fecha,
                    'state'=> 'ACTIVO',
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
    $fecha->save();
    $tipo_alert = "Create";
    $array_color = (new Colores)->getColores();
    return view('Fecha.Fecha',compact('count_notification','tipo_alert','array_color'));
}
public function edit($id){
    $inventario = Inventario::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.edit_modulo');
    $array_color = (new Colores)->getColores();
    $categoria = (new Categoria)->getCategoria();
    $producto = (new Producto)->getProducto();
    $almacen = (new Almacen)->getAlmacen();
    $tipoentrada = array('COMPRA' => 'COMPRA', 'DONACION' => 'DONACION');
    return view('Inventario.inventario_edit',compact('count_notification','titulo_modulo','categoria','producto','inventario','almacen','tipoentrada','array_color'));
}
public function update(Request $request, $id){
 //var_dump($request->all());
   //exit();
   $user_id = auth()->user()->id;
    $inventario_Update = Inventario::find($id);
    $viejaexistencia = $inventario_Update->cantidad;
    $inventario_Update->fechavencimiento = $request->fechavencimiento;
    $inventario_Update->cantidad = $request->cantidad;
    $inventario_Update->fecha = \Carbon\Carbon::now();
    $inventario_Update->updated_at = \Carbon\Carbon::now();
    $inventario_Update->save();
    $ajuste = new Ajuste([
        'inventario_id' =>$id,
        'user_id'=> $user_id,
        'fecha'=> \Carbon\Carbon::now(),
        'viejaexistecia'=>$viejaexistencia,
        'nuevaexistencia'=> $request->cantidad,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ]);
$ajuste->save();
    session(['update' => true]);
    //alert()->success(trans('message.mensajes_alert.modulo_update'),trans('message.mensajes_alert.msg_modulo_01').$modulo_Update->name. trans('message.mensajes_alert.msg_02'));
    return redirect('/inventario');
}
public function show($id){
    $inventario = Inventario::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.show_modulo');
    $array_color = (new Colores)->getColores();
    $categoria = (new Categoria)->getCategoria();
    $producto = (new Producto)->getProducto();
    $almacen = (new Almacen)->getAlmacen();
    $tipoentrada = array('COMPRA' => 'COMPRA', 'DONACION' => 'DONACION');
    return view('Inventario.inventario_show',compact('count_notification','tipoentrada','titulo_modulo','almacen','inventario','producto','categoria','array_color'));
}
}
