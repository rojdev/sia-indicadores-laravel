<?php

namespace App\Http\Controllers\Producto;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User\User;
use App\Models\Security\Rol;
use App\Models\Producto\Producto;
use App\Models\Categoria\Categoria;
use App\Http\Controllers\User\Colores;
class ProductoController extends Controller
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
        return view('Inventario.Producto.producto',compact('count_notification','tipo_alert','array_color'));
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
        return view('Inventario.Producto.producto2',compact('count_notification','tipo_alert','array_color'));
    }
    public function create(){
        $titulo_modulo = trans('message.users_action.new_user');
        $count_notification = (new User)->count_noficaciones_user();
        $roles = (new Rol)->datos_roles();
        $array_color = (new Colores)->getColores();
        $categoria = (new Categoria)->getCategoria();

        return view('Inventario.Producto.producto_create', compact('count_notification', 'titulo_modulo','categoria', 'roles', 'array_color'));
    }
    public function getProducto(Request $request){
        //
        try {
            if ($request->ajax()) {
                $data = (new Producto)->getProducto();
                return datatables()->of($data)
                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('producto.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('producto.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        }
                        return $edit;
                    })
                    ->addColumn('view', function ($data) {
                        return '<a style="background-color: #5333ed;" href="' . route('producto.view', $data->id) . '" id="view_' . $data->id . '" class="btn btn-xs btn-primary"><b><i class="fa fa-eye"></i>&nbsp;' . trans('message.botones.view') . '</b></a>';
                    })

                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }
   
public function store(Request $request){
    $input  = $request->all();
   
    $count_notification = (new User)->count_noficaciones_user();
    $producto = new Producto([                            
                    'nombre' =>$request->nombre,
                    'descripcion'=> $request->descripcion,
                    'cantidad'=> $request->cantidad,
                    'precio'=> $request->precio,
                    'categoria_id' =>$request->categoria_id,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
    $producto->save();        
    $tipo_alert = "Create";
    $array_color = (new Colores)->getColores();
    return view('Inventario.Producto.producto',compact('count_notification','tipo_alert','array_color'));
}
public function edit($id){
    $producto = Producto::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.edit_modulo');
    $array_color = (new Colores)->getColores();
    $categoria = (new Categoria)->getCategoria();
    return view('Inventario.Producto.producto_edit',compact('count_notification','titulo_modulo','categoria','producto','array_color'));
}
public function update(Request $request, $id){
  // var_dump($request->all());
   //exit();
    $producto_Update = Producto::find($id);
    $producto_Update->nombre = $request->nombre;
    $producto_Update->descripcion = $request->descripcion;
    $producto_Update->cantidad = $request->cantidad;
    $producto_Update->precio = $request->precio;
    $producto_Update->categoria_id = $request->categoria_id;
    $producto_Update->updated_at = \Carbon\Carbon::now();
    $producto_Update->save();
    session(['update' => true]);
    //alert()->success(trans('message.mensajes_alert.modulo_update'),trans('message.mensajes_alert.msg_modulo_01').$modulo_Update->name. trans('message.mensajes_alert.msg_02'));
    return redirect('/producto');
}
public function show($id){
    $producto = Producto::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.show_modulo');
    $array_color = (new Colores)->getColores();
    $categoria = (new Categoria)->getCategoria();
    return view('Inventario.Producto.producto_show',compact('count_notification','titulo_modulo','producto','categoria','array_color'));
}
}

