<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\Inventario;
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

class InventarioController extends Controller
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
        return view('Inventario.inventario',compact('count_notification','tipo_alert','array_color'));
    }
    public function index2(){        
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
        return view('Inventario.inventario2',compact('count_notification','tipo_alert','array_color'));
    }
    public function create(){
        $titulo_modulo = trans('message.users_action.new_user');
        $count_notification = (new User)->count_noficaciones_user();
        $roles = (new Rol)->datos_roles();
        $array_color = (new Colores)->getColores();
        $categoria = (new Categoria)->getCategoria();
        $producto = (new Producto)->getProducto();
        $almacen = (new Almacen)->getAlmacen();
        $tipoentrada = array('COMPRA' => 'COMPRA', 'DONACION' => 'DONACION');

        return view('Inventario.inventario_create', compact('count_notification', 'titulo_modulo','categoria','producto', 'almacen','tipoentrada','roles', 'array_color'));
    }
    public function getInventario(Request $request){
        //
        try {
            if ($request->ajax()) {
                $data = (new Inventario)->getInventario2();
                return datatables()->of($data)
                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('inventario.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('inventario.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        }
                        return $edit;
                    })
                    ->addColumn('view', function ($data) {
                        return '<a style="background-color: #5333ed;" href="' . route('inventario.view', $data->id) . '" id="view_' . $data->id . '" class="btn btn-xs btn-primary"><b><i class="fa fa-eye"></i>&nbsp;' . trans('message.botones.view') . '</b></a>';
                    })

                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }
   
public function store(Request $request){
    
    $count_notification = (new User)->count_noficaciones_user();
   
    $invetario = new Inventario([                            
                    'producto_id' =>$request->producto_id,
                    'almacen_id'=> $request->almacen_id,
                    'cantidad'=> $request->cantidad,
                    'cantidad_entrada'  => $request->cantidad,
                    'fecha'=> \Carbon\Carbon::now(),
                    'tipoentrada'=> $request->tipoentrada,
                    'numerofactura'=> $request->numerofactura,
                    'numerodonacion'=> $request->numerodonacion,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                    'fechavencimiento' => $request->fechavencimiento,
                ]);
    $invetario->save();        
    $tipo_alert = "Create";
    $array_color = (new Colores)->getColores();
    return view('Inventario.inventario',compact('count_notification','tipo_alert','array_color'));
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
