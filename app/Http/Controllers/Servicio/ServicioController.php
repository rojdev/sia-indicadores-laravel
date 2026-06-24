<?php

namespace App\Http\Controllers\Servicio;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\Security\Rol;
use App\Models\Servicio\Servicio;
use App\Http\Controllers\User\Colores;
class ServicioController extends Controller
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
        return view('Inventario.Servicio.servicio',compact('count_notification','tipo_alert','array_color'));
    }
    public function create(){
        $titulo_modulo = trans('message.users_action.new_user');
        $count_notification = (new User)->count_noficaciones_user();
        $roles = (new Rol)->datos_roles();
        $array_color = (new Colores)->getColores();

        return view('Inventario.Servicio.servicio_create', compact('count_notification', 'titulo_modulo', 'roles', 'array_color'));
    }
    public function getServicio(Request $request){
        //
        try {
            if ($request->ajax()) {
                $data = (new Servicio)->getServicio();
                return datatables()->of($data)
                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('servicio.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('servicio.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.edit') . '</b></a>';
                        }
                        return $edit;
                    })
                    ->addColumn('view', function ($data) {
                        return '<a style="background-color: #5333ed;" href="' . route('servicio.view', $data->id) . '" id="view_' . $data->id . '" class="btn btn-xs btn-primary"><b><i class="fa fa-eye"></i>&nbsp;' . trans('message.botones.view') . '</b></a>';
                    })

                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }
   
public function store(Request $request){
    $count_notification = (new User)->count_noficaciones_user();
    $servicio = new Servicio([                            
                    'nombre' =>$request->nombre,
                    'descripcion' => $request->descripcion,
                    'created_at' => '',
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
    $servicio->save();        
    $tipo_alert = "Create";
    $array_color = (new Colores)->getColores();
    return view('Inventario.Servicio.servicio',compact('count_notification','tipo_alert','array_color'));
}
public function edit($id){
    $servicio = Servicio::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.edit_modulo');
    $array_color = (new Colores)->getColores();
    return view('Inventario.Servicio.servicio_edit',compact('count_notification','titulo_modulo','servicio','array_color'));
}
public function update(Request $request, $id){
    $servicio_Update = Servicio::find($id);
    $servicio_Update->nombre = $request->nombre;
    $servicio_Update->descripcion = $request->descripcion;
    $servicio_Update->updated_at = \Carbon\Carbon::now();
    $servicio_Update->save();
    session(['update' => true]);
    //alert()->success(trans('message.mensajes_alert.modulo_update'),trans('message.mensajes_alert.msg_modulo_01').$modulo_Update->name. trans('message.mensajes_alert.msg_02'));
    return redirect('/servicio');
}
public function show($id){
    $almacen = Almacen::find($id);
    $count_notification = (new User)->count_noficaciones_user();
    $titulo_modulo = trans('message.modulo_action.show_modulo');
    $array_color = (new Colores)->getColores();
    return view('Inventario.Almacen.almacen_show',compact('count_notification','titulo_modulo','almacen','array_color'));
}
}