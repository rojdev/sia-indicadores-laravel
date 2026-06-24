<?php

namespace App\Http\Controllers\Actividades;
use App\Http\Controllers\Controller;
use App\Models\SalaUnidad\SalaUnidad;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\Security\Rol;
use App\Models\Estados\Estados;
use App\Models\Actividades\Actividades;
use App\Models\Municipio\Municipio;
use App\Models\Parroquia\Parroquia;
use App\Models\Enter\Enter;
use App\Models\Coordinacion\Coordinacion;
use App\Models\Tipo_Solicitud\Tipo_Solicitud;
use Auth;
use Carbon\Carbon;
use App\Http\Controllers\User\Colores;
use App\Models\JefeComunidad\JefeComunidad;
use App\Models\Subtiposolicitud\subtiposolicitud;
use App\Models\Direccion\Direccion;
use App\Models\Comunidad\Comunidad;
use App\Models\Comuna\Comuna;



class ActividadesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @author Tarsicio Carrizales telecom.com.ve@gmail.com
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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
        return view('Actividades.Actividades_list',compact('count_notification','tipo_alert','array_color'));
    }
    public function create(Request $request)
    {
        $tipo = $request->get('tipo');
        $titulo_modulo = trans('message.users_action.new_user');
        $count_notification = (new User)->count_noficaciones_user();
        $array_color = (new Colores)->getColores();
        $roles = (new Rol)->datos_roles();
        $estado = (new Estados)->datos_estados();
        $municipio = (new Municipio)->datos_municipio();
        $parroquia = (new Parroquia)->datos_parroquia();
        $tipo_solicitud = (new Tipo_Solicitud)->datos_tipo_solicitud_Tipo($tipo);
        $subtiposolicitud = (new Subtiposolicitud)->getSubtiposolicitud();
        $enter = (new Enter)->datos_enter();
        $direcciones = (new SalaUnidad)->getallUnidades();
        $comuna = [];
        $coordinacion = [];
        $comunidad = [];
        $jefecomunidad = [];

        // Obtener el último ID
        $ultimoId = (new Actividades)->CorrelativoActividadesGobierno();

        if ($ultimoId) {
            // Extraer el número correlativo del último ID
            $correlativo = (int)substr($ultimoId, 7) + 1;
            // Reconstruir el nuevo ID con el formato correcto
            $correlativoATC = sprintf("ACT-%s/%04d", date('y'), $correlativo);
        } else {
            // Si no hay resultados previos, iniciar el correlativo
            $correlativoATC = "ACT-" . date('y') . "/0001";
        }

        $sala_unidad_id = Auth::user()->sala_unidad_id;
        $sala_unidad = (new SalaUnidad)->getUnidadbyId($sala_unidad_id);
        $sala_unidad =$sala_unidad[0]->nombre;

        return view('Actividades.Actividades_new', compact('count_notification', 'titulo_modulo','sala_unidad_id','sala_unidad','tipo', 'roles','correlativoATC', 'municipio', 'comuna', 'comunidad','jefecomunidad', 'direcciones', 'parroquia', 'estado', 'coordinacion', 'enter', 'tipo_solicitud','subtiposolicitud', 'array_color'));
    }

    public function getdataactividades(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Actividades)->getActividadesNew($request->fecha_desde, $request->fecha_hasta,$request->comuna, $request->comunidad, $request->direcciones);

                return datatables()->of($data)

                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('actividades.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('actividades.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        }
                        return $edit;
                    })
                    ->addColumn('view', function ($data) {
                        return '<a style="background-color: #5333ed;" href="' . route('seguimiento.view', $data->id) . '" id="view_' . $data->id . '" class="btn btn-xs btn-primary"><b><i class="fa fa-eye"></i>&nbsp;' . trans('message.botones.view') . '</b></a>';
                    })

                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }
    public function store(Request $request)
    {
        $titulo_modulo = trans('message.users_action.new_user');
        $count_notification = (new User)->count_noficaciones_user();
        $array_color = (new Colores)->getColores();
        $tipo_alert = 'Create';
        $input = $request->all();

        // Obtener el último ID
        $ultimoId = (new Actividades)->CorrelativoActividadesGobierno();

        if ($ultimoId) {
            // Extraer el número correlativo del último ID
            $correlativo = (int)substr($ultimoId, 7) + 1;
            // Reconstruir el nuevo ID con el formato correcto
            $correlativoATC = sprintf("ACT-%s/%04d", date('y'), $correlativo);
        } else {
            // Si no hay resultados previos, iniciar el correlativo
            $correlativoATC = "ACT-" . date('y') . "/0001";
        }

        $actividades = new Actividades([
            'actividad_id' => $correlativoATC, // Asignar el correlativo generado
            'direccion_id' => isset($input['sala_unidad_id']) ? $input['sala_unidad_id'] : NULL,
            'coordinacion_id' => isset($input['coordinacion_unidad_id']) ? $input['coordinacion_unidad_id'] : NULL,
            'descripcion' => isset($input['descripcion']) ? $input['descripcion'] : NULL,
            'fecha' => isset($input['fecha']) ? $input['fecha'] : null,
            'state' => 'REGISTRADO',
            'cantidad_semanales' => isset($input['cantidad_semanales']) ? $input['cantidad_semanales'] : null,
            'created_at' => \Carbon\Carbon::now('America/Caracas'),
            'updated_at' => \Carbon\Carbon::now('America/Caracas'),
        ]);

        $actividades->save();

        return view('Actividades.Actividades_list', compact('count_notification', 'tipo_alert', 'array_color'));
    }



    public function edit($id)
    {
        $solicitud_edit = Actividades::find($id);

        $direcciones = (new SalaUnidad)->getallUnidades();
        $coordinaciones =(new Coordinacion())->getcoordxdireccion($solicitud_edit->direccion_id);
        $solicitud_edit->fechainicial = Carbon::parse($solicitud_edit->fechainicial)->toDateString();

        $titulo_modulo = trans('message.users_action.edit_user');
        $count_notification = (new User)->count_noficaciones_user();
        $array_color = (new Colores)->getColores();
        $estado = (new Estados)->datos_estados();
        $municipio = (new Municipio)->datos_municipio();
        $parroquia = (new Parroquia)->datos_parroquia();
        $array_color = (new Colores)->getColores();
        $tipo_solicitud = (new Tipo_Solicitud)->datos_tipo_solicitud();
        $enter = (new Enter)->datos_enter();
        $comunidad = [];
        $asignacion = array('DIRECCION' => 'DIRECCION');
        $sexo = array('MASCULINO' => 'MASCULINO','MASCULINO MAYOR' => 'MASCULINO MAYOR', 'ADOLESCENTE MASCULINO' => 'ADOLESCENTE MASCULINO', 'FEMENINO' => 'FEMENINO','FEMENINO MAYOR' => 'FEMENINO MAYOR', 'ADOLESCENTE FEMENINO' => 'ADOLESCENTE FEMENINO');
        $trabajador = array('NO' => 'NO', 'EMPLEADO' => 'EMPLEADO', 'OBRERO' => 'OBRERO', 'JUBILADO' => 'JUBILADO', 'PENSIONADO' => 'PENSIONADO','PENSIONADO SOBREVIVIETE ALPAEZ =>' => 'PENSIONADO SOBREVIVIETE ALPAEZ =>');
        $edocivil = array('SOLTERO' => 'SOLTERO', 'CASADO' => 'CASADO', 'VIUDO' => 'VIUDO', 'DIVORCIADO' => 'DIVORCIADO');
        $nivelestudio = array('PRIMARIA' => 'PRIMARIA', 'SECUNDARIA' => 'SECUNDARIA', 'BACHILLERATO' => 'BACHILLERATO', 'UNIVERSITARIO' => 'UNIVERSITARIO', 'ESPECIALIZACION' => 'ESPECIALIZACION');
        $profesion = array('OBREBRO' => 'OBREBRO','JUBILADO' => 'JUBILADO','PENSIONADO' => 'PENSIONADO','OFICIOS DEL HOGAR' => 'OFICIOS DEL HOGAR','OTRO' => 'OTRO','TECNICO MEDIO' => 'TECNICO MEDIO', 'TECNICO SUPERIOR' => 'TECNICO SUPERIOR', 'INGENIERO' => 'INGENIERO', 'ABOGADO' => 'ABOGADO', 'MEDICO CIRUJANO' => 'MEDICO CIRUJANO', 'HISTORIADOR' => 'HISTORIADOR', 'PALEONTOLOGO' => 'PALEONTOLOGO', 'GEOGRAFO' => 'GEOGRAFO', 'BIOLOGO' => 'BIOLOGO', 'PSICOLOGO' => 'PSICOLOGO', 'MATEMATICO' => 'MATEMATICO', 'ARQUITECTO' => 'ARQUITECTO', 'COMPUTISTA' => 'COMPUTISTA', 'PROFESOR' => 'PROFESOR', 'PERIODISTA' => 'PERIODISTA', 'BOTANICO' => 'BOTANICO', 'FISICO' => 'FISICO', 'SOCIOLOGO' => 'SOCIOLOGO', 'FARMACOLOGO' => 'FARMACOLOGO', 'QUIMICO' => 'QUIMICO', 'POLITOLOGO' => 'POLITOLOGO', 'ENFERMERO' => 'ENFERMERO', 'ELECTRICISTA' => 'ELECTRICISTA', 'BIBLIOTECOLOGO' => 'BIBLIOTECOLOGO', 'PARAMEDICO' => 'PARAMEDICO', 'TECNICO DE SONIDO' => 'TECNICO DE SONIDO', 'ARCHIVOLOGO' => 'ARCHIVOLOGO', 'MUSICO' => 'MUSICO', 'FILOSOFO' => 'FILOSOFO', 'SECRETARIA' => 'SECRETARIA', 'TRADUCTOR' => 'TRADUCTOR', 'ANTROPOLOGO' => 'ANTROPOLOGO', 'TECNICO TURISMO' => 'TECNICO TURISMO', 'ECONOMISTA' => 'ECONOMISTA', 'ADMINISTRADOR' => 'ADMINISTRADOR', 'CARPITERO' => 'CARPITERO', 'RADIOLOGO' => 'RADIOLOGO', 'COMERCIANTE' => 'COMERCIANTE', 'CERRAJERO' => 'CERRAJERO', 'COCINERO' => 'COCINERO', 'ALBAÑIL' => 'ALBAÑIL', 'PLOMERO' => 'PLOMERO', 'TORNERO' => 'TORNERO', 'EDITOR' => 'EDITOR', 'ESCULTOR' => 'ESCULTOR', 'ESCRITOR' => 'ESCRITOR', 'BARBERO' => 'BARBERO');

        $comuna = (new Comuna)->datos_comuna($solicitud_edit->parroquia_id);
        $state = array('REGISTRADO' => 'REGISTRADO', 'APROBADO' => 'APROBADO', 'RECHAZADO' => 'RECHAZADO');
        $comunidad = (new Comunidad)->datos_comunidad($solicitud_edit->comuna_id);
        $coordinacion = (new Coordinacion)->datos_coordinacion($solicitud_edit->direccion_id);
        $jefecomunidad2 = (new JefeComunidad)->getJefe($solicitud_edit->comuna_id);
        $jefecomunidad = (new JefeComunidad)->getJefe2($solicitud_edit->jefecomunidad_id);

        $subtiposolicitud = (new Subtiposolicitud)->getSubtiposolicitud();

        return view('Actividades.Actividades_edit', compact('count_notification','coordinaciones','direcciones', 'titulo_modulo', 'solicitud_edit','trabajador','state','estado', 'municipio', 'parroquia', 'asignacion', 'comuna', 'comunidad','jefecomunidad','jefecomunidad2', 'tipo_solicitud','subtiposolicitud', 'direcciones', 'enter', 'sexo', 'edocivil', 'nivelestudio',  'profesion',  'array_color'));
    }
    public function update(Request $request, $id)
    {

        // $count_notification = (new User)->count_noficaciones_user();
        $input = $request->all();
        $solicitud_Update = Actividades::find($id);
        $solicitud_Update->direccion_id=$input['direccion_id'];
        $solicitud_Update->coordinacion_id=$input['coordinacion_id'];
        $solicitud_Update->descripcion=$input['nombre'];
        $solicitud_Update->fecha=$input['fechainicial'];
        if ((Auth::user()->rols_id === 14) || (Auth::user()->rols_id === 13) || (Auth::user()->rols_id === 1)) {
        $solicitud_Update->observacion=$input['observacion'];
        $solicitud_Update->state=$input['state'];
        }
        $solicitud_Update->cantidad_semanales=$input['cantidad'];
        $solicitud_Update->save();

        return redirect('/acciones/list');
    }

    public function actividadesano3(){
        $actividades = (new Actividades)->actividadesano3();
        return $actividades;
    }


}
