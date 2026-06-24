<?php

namespace App\Http\Controllers\Almacen;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\Security\Rol;
use App\Models\Almacen\Almacen;
use App\Http\Controllers\User\Colores;
class AlmacenController extends Controller
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
        return view('Inventario.Almacen.almacen',compact('count_notification','tipo_alert','array_color'));
    }
    public function create(){
        $titulo_modulo = trans('message.users_action.new_user');
        $count_notification = (new User)->count_noficaciones_user();
        $roles = (new Rol)->datos_roles();
        $array_color = (new Colores)->getColores();
       


        return view('Inventario.Almacen.almacen_create', compact('count_notification', 'titulo_modulo', 'roles', 'array_color'));
    }
    public function getAlmacen(Request $request){
        //
        try {
            if ($request->ajax()) {
                $data = (new Almacen)->getAlmacen();
                return datatables()->of($data)
                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('almacen.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('almacen.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        }
                        return $edit;
                    })
                    ->addColumn('view', function ($data) {
                        return '<a style="background-color: #5333ed;" href="' . route('almacen.view', $data->id) . '" id="view_' . $data->id . '" class="btn btn-xs btn-primary"><b><i class="fa fa-eye"></i>&nbsp;' . trans('message.botones.view') . '</b></a>';
                    })

                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }
   
public function store(Request $request){
    $count_notification = (new User)->count_noficaciones_user();
    $almacen = new Almacen([                            
                    'nombre' =>$request->nombre,
                    'ubicacion' => $request->ubicacion,
                    'created_at' => \Carbon\Carbon::now('America/Caracas'),
                    'updated_at' => \Carbon\Carbon::now('America/Caracas'),
                ]);
    $almacen->save();        
    $tipo_alert = "Create";
    $array_color = (new Colores)->getColores();
    return view('Inventario.Almacen.almacen',compact('count_notification','tipo_alert','array_color'));
}
public function edit($id){
    $almacen = Almacen::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.edit_modulo');
    $array_color = (new Colores)->getColores();
    return view('Inventario.Almacen.almacen_edit',compact('count_notification','titulo_modulo','almacen','array_color'));
}
public function update(Request $request, $id){
    $almacen_Update = Almacen::find($id);
    $almacen_Update->nombre = $request->nombre;
    $almacen_Update->ubicacion = $request->ubicacion;
    $almacen_Update->updated_at = \Carbon\Carbon::now();
    $almacen_Update->save();
    session(['update' => true]);
    //alert()->success(trans('message.mensajes_alert.modulo_update'),trans('message.mensajes_alert.msg_modulo_01').$modulo_Update->name. trans('message.mensajes_alert.msg_02'));
    return redirect('/almacen');
}
public function show($id){
    $almacen = Almacen::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.show_modulo');
    $array_color = (new Colores)->getColores();
    return view('Inventario.Almacen.almacen_show',compact('count_notification','titulo_modulo','almacen','array_color'));
}
}