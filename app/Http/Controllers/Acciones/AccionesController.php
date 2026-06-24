<?php

namespace App\Http\Controllers\Acciones;
use App\Http\Controllers\SendMail\SendMail;
use App\Http\Controllers\Controller;
use App\Models\SalaUnidad\SalaUnidad;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\Solicitud\Solicitud;
use App\Http\Requests\User\StoreUser;
use App\Http\Requests\User\UpdateUser;
use App\Models\Security\Rol;
use App\Models\Estados\Estados;
use App\Models\Acciones\Acciones;
use App\Models\Municipio\Municipio;
use App\Models\Parroquia\Parroquia;
use App\Models\Comuna\Comuna;
use App\Models\Comunidad\Comunidad;
use App\Models\Direccion\Direccion;
use App\Models\Enter\Enter;
use App\Models\Coordinacion\Coordinacion;
use App\Models\Tipo_Solicitud\Tipo_Solicitud;
use Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Notifications\WelcomeUser;
use App\Notifications\RegisterConfirm;
use App\Notifications\NotificarEventos;
use Carbon\Carbon;
use App\Http\Controllers\User\Colores;
use App\Models\JefeComunidad\JefeComunidad;
use App\Models\Subtiposolicitud\subtiposolicitud;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

use DB;
use App\Models\Seguimiento\Seguimiento;


class AccionesController extends Controller
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
        return view('Acciones.Accion_list',compact('count_notification','tipo_alert','array_color'));
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
        $user_unidad_id = Auth::user()->sala_unidad_id;
        // Obtener el último ID
        $ultimoId = (new Acciones)->CorrelativoAccionGobierno($user_unidad_id);

        $correlativoATC = $this->ObtenerCorrelativo($user_unidad_id);

        $sala_unidad_id = Auth::user()->sala_unidad_id;
        $sala_unidad = (new SalaUnidad)->getUnidadbyId($sala_unidad_id);
        $sala_unidad =$sala_unidad[0]->nombre;

        return view('Acciones.Accion_new', compact('count_notification', 'titulo_modulo','sala_unidad_id','sala_unidad','tipo', 'roles','correlativoATC', 'municipio', 'comuna', 'comunidad','jefecomunidad', 'direcciones', 'parroquia', 'estado', 'coordinacion', 'enter', 'tipo_solicitud','subtiposolicitud', 'array_color'));
    }



    public function totales(Request $request){
        $totales = (new Acciones)->totales();
        return $totales;
    }

    public function totalesFiltrados(Request $request)
    {
        // Obtener los parámetros de filtro de la solicitud.  Usa null coalescing operator (??) para valores por defecto.
        $fechaDesde = $request->input('fecha_desde') ?? null;
        $fechaHasta = $request->input('fecha_hasta') ?? null;
        $comunaId = $request->input('comuna') ?? null;
        $comunidadId = $request->input('comunidad') ?? null;
        $direccionId = $request->input('direcciones') ?? null;

        // Llama al método filtrado en el modelo.  Pasa los parámetros.
        $resultados = (new Acciones)->totalesFiltrados($fechaDesde, $fechaHasta, $comunaId, $comunidadId, $direccionId);

        // El modelo ya devuelve JSON, así que simplemente retornamos el resultado.
        return $resultados;
    }
    public function getdataacciones(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Acciones)->getAccionesNew($request->fecha_desde, $request->fecha_hasta,$request->comuna, $request->comunidad, $request->direcciones);

                return datatables()->of($data)

                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('acciones.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('acciones.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        }
                        return $edit;
                    })
                    ->addColumn('view', function ($data) {
                        return '<a style="background-color: #5333ed;" href="' . route('seguimiento.view', $data->id) . '" id="view_' . $data->id . '" class="btn btn-xs btn-primary"><b><i class="fa fa-eye"></i>&nbsp;' . trans('message.botones.view') . '</b></a>';
                    })
                    ->addColumn('del', function ($data) {


                            $del ='<a href="javascript:void(0)" action="'.route('acciones.destroy', $data->id).'" onclick="deleteData(this)"><button type="submit" class="btn btn-danger btn-xs" data-toggle="tooltip" data-title="Eliminar" data-container="body" style="background-color: #900C3F;"><b><i class="fa fa-trash"></i>&nbsp;' .trans('message.botones.delete').'</b>';

                        return $del;
                    })

                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }

    public function store(Request $request)
    {
        $tipo = $request->get('tipo');
        $titulo_modulo = trans('message.users_action.new_user');
        $count_notification = (new User)->count_noficaciones_user();
        $array_color = (new Colores)->getColores();
        $tipo_alert = 'Create';
        $input = $request->all();

        $imagen = $request->file('image');

        $imagen2 = $request->file('image2');

        $correlativoATC = $this->ObtenerCorrelativo($input['sala_unidad_id']);

        if ($imagen) {
            $nombreImagen = uniqid() . '.' . $imagen->getClientOriginalExtension();
            $ruta = $imagen->move('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public/Evidencias', $nombreImagen);
        } else {
            $ruta = null;
        }

        if ($imagen2) {
            $nombreImagen2 = uniqid() . '.' . $imagen2->getClientOriginalExtension();
            $ruta2 = $imagen2->move('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public/Evidencias', $nombreImagen2);
        } else {
            $ruta2 = null;
        }

        $jefecomunidad = JefeComunidad::where('comunidad_id', $input['comunidad_id'])->first();
        $jefecomunidad = $jefecomunidad->id;

        if ($ruta) {
            $fullPath = str_replace('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public', '', (string) $ruta);
        } else {
            $fullPath = null;
        }

        if ($ruta2) {
            $fullPath2 = str_replace('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public', '', (string) $ruta2);
        } else {
            $fullPath2 = null;
        }
        $acciones = new Acciones([
            'users_id' => isset($used_id) ? $used_id : NULL,
            'nombre' => isset($input['nombre']) ? $input['nombre'] : null,
            'accion_id' => $correlativoATC, // Asignar el correlativo generado
            'direccion_id' => isset($input['sala_unidad_id']) ? $input['sala_unidad_id'] : null,
            'coordinacion_sala_id' => isset($input['coordinacion_unidad_id']) ? $input['coordinacion_unidad_id'] : null,
            'estado_id' => isset($input['estado_id']) ? $input['estado_id'] : NULL,
            'municipio_id' => isset($input['municipio_id']) ? $input['municipio_id'] : NULL,
            'parroquia_id' => isset($input['parroquia_id']) ? $input['parroquia_id'] : NULL,
            'comuna_id' => isset($input['comuna_id']) ? $input['comuna_id'] : NULL,
            'comunidad_id' => isset($input['comunidad_id']) ? $input['comunidad_id'] : NULL,
            'jefecomunidad_id' => isset($jefecomunidad) ? $jefecomunidad : NULL,
            'direccion' => isset($input['direccionhab']) ? $input['direccionhab'] : null,
            'state' => 'REGISTRADA',
            'vocero' => isset($input['vocero']) ? $input['vocero'] : null,
            'telefono' => isset($input['telefono']) ? $input['telefono'] : null,
            'evidencia_path' => $fullPath,
            'evidencia_path2' => $fullPath2,
            'fechainicial' => isset($input['fechainicial']) ? $input['fechainicial'] : null,
            'fechafinal' => isset($input['fechafinal']) ? $input['fechafinal'] : null,
            'created_at' => \Carbon\Carbon::now('America/Caracas'),
            'updated_at' => \Carbon\Carbon::now('America/Caracas'),
        ]);

        $acciones->save();

        return view('Acciones.Accion_list', compact('count_notification', 'tipo', 'tipo_alert', 'array_color'));
    }

    public function ObtenerCorrelativo($id) {
        // Obtener el último correlativo
        $ultimoCorrelativo = (new Acciones)->CorrelativoAccionGobierno($id);


        // Obtener la nomenclatura del correlativo
        $nomenclaturacorrelativo = SalaUnidad::where('id', $id)->value('correlativo');

        $nomenclaturacorrelativo = str_replace('ANNIO', date('y'), $nomenclaturacorrelativo);

        $semestre = date('m');
        if ($semestre >= 1 && $semestre <= 6) {
            $nomenclaturacorrelativo = str_replace('SEMESTRE', 'SI', $nomenclaturacorrelativo);
        } elseif ($semestre >= 7 && $semestre <= 12) {
            $nomenclaturacorrelativo = str_replace('SEMESTRE', 'SII', $nomenclaturacorrelativo);
        }

        if ($ultimoCorrelativo) {
            // Extraer el número correlativo del último ID
            $correlativo = (int)substr($ultimoCorrelativo, strlen($nomenclaturacorrelativo)) + 1;
            // Reconstruir el nuevo ID con el formato correcto
            $correlativoATC = $nomenclaturacorrelativo . sprintf("%04d", $correlativo);
        } else {
            // Si no hay resultados previos, iniciar el correlativo
            $correlativoATC = $nomenclaturacorrelativo . "0001";
        }
        // agregar la accion en la tabla acciones_new para luego poder calcular el siguiente correlativo

        return $correlativoATC;
    }

    // public function ObtenerCorrelativoImportar($id) {
    //     return $ultimoCorrelativo = (new Acciones)->CorrelativoAccionGobiernoImport($id);
    // }

    public function ObtenerCorrelativoImport($id, $accion,$anno) {

        $ultimoCorrelativo = (new Acciones)->CorrelativoAccionGobiernoImport($id,$anno);
        $nomenclaturacorrelativo = SalaUnidad::where('id', $id)->value('correlativo');
        if ($anno == '2024') {
            $nomenclaturacorrelativo = str_replace('ANNIO', '24', $nomenclaturacorrelativo);
        } else {
            $nomenclaturacorrelativo = str_replace('ANNIO', '25', $nomenclaturacorrelativo);
        }
      //  $nomenclaturacorrelativo = str_replace('ANNIO', date('y'), $nomenclaturacorrelativo);

        $semestre = date('m');
        if ($anno == '2024') {
            $nomenclaturacorrelativo = str_replace('SEMESTRE', 'SII', $nomenclaturacorrelativo);
        }
        else {
            if ($semestre >= 1 && $semestre <= 6) {
                $nomenclaturacorrelativo = str_replace('SEMESTRE', 'SI', $nomenclaturacorrelativo);
            } elseif ($semestre >= 7 && $semestre <= 12) {
                $nomenclaturacorrelativo = str_replace('SEMESTRE', 'SII', $nomenclaturacorrelativo);
            }
        }


        if ($ultimoCorrelativo != null) {
            $correlativo = (int)substr($ultimoCorrelativo, -4) + 1;
            $correlativoATC = $nomenclaturacorrelativo . sprintf("%04d", $correlativo);
        } else {
            $correlativoATC = $nomenclaturacorrelativo . "0001";
        }
       // var_dump($correlativoATC);
        // var_dump($accion);
        // exit();
            $acciones = new Acciones([
            'users_id' => isset($used_id) ? $used_id : NULL,
            'nombre' => $accion->nombre ? $accion->nombre : null,
            'accion_id' => $correlativoATC, // Asignar el correlativo generado
            'direccion_id' => $accion->direccion_id ? $accion->direccion_id : null,
            'coordinacion_sala_id' => $accion->coordinacion_unidad_id ? $accion->coordinacion_unidad_id : null,
            'estado_id' => $accion->estado_id ? $accion->estado_id : NULL,
            'municipio_id' => $accion->municipio_id ? $accion->municipio_id : NULL,
            'parroquia_id' => $accion->parroquia_id ? $accion->parroquia_id : NULL,
            'comuna_id' => $accion->comuna_id ? $accion->comuna_id : NULL,
            'comunidad_id' => $accion->comunidad_id ? $accion->comunidad_id : NULL,
            'jefecomunidad_id' => $accion->jefecomunidad_id   ? $accion->jefecomunidad_id : NULL,
            'direccion' => $accion->direccion ? $accion->direccion : null,
            'state' => $accion->state ? $accion->state : null,
            'vocero' => $accion->vocero ? $accion->vocero : null,
            'telefono' => $accion->telefono ? $accion->telefono : null,
            'evidencia_path' => '',
            'evidencia_path2' => '',
            'fechainicial' => $accion->fechainicial ? $accion->fechainicial : null,
            'fechafinal' => $accion->fechafinal ? $accion->fechafinal : null,
            'created_at' => \Carbon\Carbon::now('America/Caracas'),
            'updated_at' => \Carbon\Carbon::now('America/Caracas'),
        ]);

        $acciones->save();

        return $correlativoATC;
    }


    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Mensaje de Bienvenida HORUS')
                    ->line('Estimado(a). '.$notifiable->name)
                    ->line('Bienvenido a HORUS Venezuela,')
                    ->line('Esperamos que sea de su agrado la presente aplicación')
                    ->line('y que pueda ahorrar tiempo en su trabajo de desarrollo.')
                    ->line('Gracias por utilizar la aplicación HORUS')
                    ->line('Att, Tarsicio Carrizales telecom.com.ve@gmail.com')
                    ->line('PRUEBA');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {

        $solicitud_edit = Solicitud::find($id);
        $valores = $solicitud_edit->all();
        $denuncia = NULL;
        $quejas = NULL;
        $reclamo = NULL;
        $asesoria = NULL;
        $sugerecia = NULL;
        $beneficiario = NULL;
        if (!(is_null($solicitud_edit->denuncia))) {
            $denuncia = $solicitud_edit->denuncia;
            $denuncia = json_decode($denuncia, true);
        }

        if (!(is_null($solicitud_edit->quejas))) {
            $quejas = $solicitud_edit->quejas;
            $quejas = json_decode($quejas, true);

        }
        if (!(is_null($solicitud_edit->reclamo))) {
            $reclamo = $solicitud_edit->reclamo;
            $reclamo = json_decode($reclamo, true);

        }
        if (!(is_null($solicitud_edit->sugerecia))) {
            $sugerecia = $solicitud_edit->sugerecia;
            $sugerecia = json_decode($sugerecia, true);

        }
        if (!(is_null($solicitud_edit->asesoria))) {
            $asesoria = $solicitud_edit->asesoria;
            $asesoria = json_decode($asesoria, true);

        }
        if (!(is_null($solicitud_edit->beneficiario))) {
            $beneficiario = $solicitud_edit->beneficiario;
            $beneficiario = json_decode($beneficiario, true);

        }
        $denunciado = $solicitud_edit->denunciado;
        $denunciado = json_decode($denunciado, true);

        $recaudos = $solicitud_edit->recaudos;
        $recaudos = json_decode($recaudos, true);

        $titulo_modulo = trans('message.users_action.edit_user');
        $count_notification = (new User)->count_noficaciones_user();
        $array_color = (new Colores)->getColores();
        $estado = (new Estados)->datos_estados();
        $municipio = (new Municipio)->datos_municipio();
        $parroquia = (new Parroquia)->datos_parroquia();
        $array_color = (new Colores)->getColores();
        $tipo_solicitud = (new Tipo_Solicitud)->datos_tipo_solicitud();
        $direcciones = (new Direccion)->datos_direccion();
        $enter = (new Enter)->datos_enter();
        $comunidad = [];
        $asignacion = array('DIRECCION' => 'DIRECCION', 'ENTER' => 'ENTER');
        $sexo = array('MASCULINO' => 'MASCULINO','MASCULINO MAYOR' => 'MASCULINO MAYOR', 'ADOLESCENTE MASCULINO' => 'ADOLESCENTE MASCULINO', 'FEMENINO' => 'FEMENINO','FEMENINO MAYOR' => 'FEMENINO MAYOR', 'ADOLESCENTE FEMENINO' => 'ADOLESCENTE FEMENINO');
        $trabajador = array('NO' => 'NO', 'EMPLEADO' => 'EMPLEADO', 'OBRERO' => 'OBRERO', 'JUBILADO' => 'JUBILADO', 'PENSIONADO' => 'PENSIONADO','PENSIONADO SOBREVIVIETE ALPAEZ =>' => 'PENSIONADO SOBREVIVIETE ALPAEZ =>');
        $edocivil = array('SOLTERO' => 'SOLTERO', 'CASADO' => 'CASADO', 'VIUDO' => 'VIUDO', 'DIVORCIADO' => 'DIVORCIADO');
        $nivelestudio = array('PRIMARIA' => 'PRIMARIA', 'SECUNDARIA' => 'SECUNDARIA', 'BACHILLERATO' => 'BACHILLERATO', 'UNIVERSITARIO' => 'UNIVERSITARIO', 'ESPECIALIZACION' => 'ESPECIALIZACION');
        $profesion = array('OBREBRO' => 'OBREBRO','JUBILADO' => 'JUBILADO','PENSIONADO' => 'PENSIONADO','OFICIOS DEL HOGAR' => 'OFICIOS DEL HOGAR','OTRO' => 'OTRO','TECNICO MEDIO' => 'TECNICO MEDIO', 'TECNICO SUPERIOR' => 'TECNICO SUPERIOR', 'INGENIERO' => 'INGENIERO', 'ABOGADO' => 'ABOGADO', 'MEDICO CIRUJANO' => 'MEDICO CIRUJANO', 'HISTORIADOR' => 'HISTORIADOR', 'PALEONTOLOGO' => 'PALEONTOLOGO', 'GEOGRAFO' => 'GEOGRAFO', 'BIOLOGO' => 'BIOLOGO', 'PSICOLOGO' => 'PSICOLOGO', 'MATEMATICO' => 'MATEMATICO', 'ARQUITECTO' => 'ARQUITECTO', 'COMPUTISTA' => 'COMPUTISTA', 'PROFESOR' => 'PROFESOR', 'PERIODISTA' => 'PERIODISTA', 'BOTANICO' => 'BOTANICO', 'FISICO' => 'FISICO', 'SOCIOLOGO' => 'SOCIOLOGO', 'FARMACOLOGO' => 'FARMACOLOGO', 'QUIMICO' => 'QUIMICO', 'POLITOLOGO' => 'POLITOLOGO', 'ENFERMERO' => 'ENFERMERO', 'ELECTRICISTA' => 'ELECTRICISTA', 'BIBLIOTECOLOGO' => 'BIBLIOTECOLOGO', 'PARAMEDICO' => 'PARAMEDICO', 'TECNICO DE SONIDO' => 'TECNICO DE SONIDO', 'ARCHIVOLOGO' => 'ARCHIVOLOGO', 'MUSICO' => 'MUSICO', 'FILOSOFO' => 'FILOSOFO', 'SECRETARIA' => 'SECRETARIA', 'TRADUCTOR' => 'TRADUCTOR', 'ANTROPOLOGO' => 'ANTROPOLOGO', 'TECNICO TURISMO' => 'TECNICO TURISMO', 'ECONOMISTA' => 'ECONOMISTA', 'ADMINISTRADOR' => 'ADMINISTRADOR', 'CARPITERO' => 'CARPITERO', 'RADIOLOGO' => 'RADIOLOGO', 'COMERCIANTE' => 'COMERCIANTE', 'CERRAJERO' => 'CERRAJERO', 'COCINERO' => 'COCINERO', 'ALBAÑIL' => 'ALBAÑIL', 'PLOMERO' => 'PLOMERO', 'TORNERO' => 'TORNERO', 'EDITOR' => 'EDITOR', 'ESCULTOR' => 'ESCULTOR', 'ESCRITOR' => 'ESCRITOR', 'BARBERO' => 'BARBERO');

        $comuna = (new Comuna)->datos_comuna($solicitud_edit->parroquia_id);
        $state = array('REGISTRADO' => 'REGISTRADO', 'APROBADO' => 'APROBADO', 'RECHAZADO' => 'RECHAZADO');
        $comunidad = (new Comunidad)->datos_comunidad($solicitud_edit->comuna_id);
        $coordinacion = (new Coordinacion)->datos_coordinacion($solicitud_edit->direccion_id);
        $jefecomunidad = (new JefeComunidad)->getJefe($solicitud_edit->comuna_id);
        $subtiposolicitud = (new subtiposolicitud)->getSubtiposolicitudbyID($solicitud_edit->tipo_subsolicitud_id);
        $correlativoSALUD = (new Solicitud)->BuscarNumeroSolicitudSalud($id);
        $correlativoATC = (new Solicitud)->BuscarNumeroSolicitudATC($id);

        $opcionesPresentada = [
            'SELECCIONE UNA OPCION' => 'SELECCIONE UNA OPCION',
            'SI' => 'SI',
            'NO' => 'NO'
        ];

        return view('Solicitud.show', compact('count_notification', 'titulo_modulo','opcionesPresentada', 'solicitud_edit','correlativoSALUD', 'correlativoATC','trabajador','estado', 'municipio', 'parroquia', 'asignacion', 'comuna', 'comunidad','jefecomunidad', 'tipo_solicitud','subtiposolicitud', 'direcciones', 'enter', 'state', 'sexo', 'edocivil', 'nivelestudio', 'coordinacion', 'denuncia', 'beneficiario', 'quejas', 'sugerecia', 'asesoria', 'reclamo', 'profesion', 'recaudos', 'denunciado', 'array_color'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $solicitud_edit = Acciones::find($id);

        $direcciones = (new SalaUnidad)->getallUnidades();
        $coordinaciones =(new Coordinacion())->getcoordxdireccion($solicitud_edit->direccion_id);
        $solicitud_edit->fechainicial = Carbon::parse($solicitud_edit->fechainicial)->toDateString();
        $solicitud_edit->fechafinal = Carbon::parse($solicitud_edit->fechafinal)->toDateString(); // Con Carbon
        $denuncia = NULL;
        $quejas = NULL;
        $reclamo = NULL;
        $asesoria = NULL;
        $sugerecia = NULL;
        $beneficiario = NULL;
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
       // $correlativoSALUD = (new Solicitud)->BuscarNumeroSolicitudSalud($id);
       // $correlativoATC = (new Solicitud)->BuscarNumeroSolicitudATC($id);
        // var_dump('direccion_id: '.$solicitud_edit->direccion_id);
        // var_dump('direcciones: ',$direcciones);
        // exit();
        return view('Acciones.Accion_edit', compact('count_notification','coordinaciones','direcciones', 'titulo_modulo', 'solicitud_edit','trabajador','estado', 'municipio', 'parroquia', 'asignacion', 'comuna', 'comunidad','jefecomunidad','jefecomunidad2', 'tipo_solicitud','subtiposolicitud', 'direcciones', 'enter', 'sexo', 'edocivil', 'nivelestudio', 'coordinacion', 'denuncia', 'beneficiario', 'quejas', 'sugerecia', 'state','asesoria', 'reclamo', 'profesion',  'array_color'));
    }
    public function getComunas(Request $request)
    {

        $comuna = (new Comuna)->datos_comuna($request['parroquia']);

        return $comuna;

    }

    public function getComunidad(Request $request)
    {

        $comunidad = (new Comunidad)->datos_comunidad($request['comuna']);

        return $comunidad;

    }
    public function getCoodinacion(Request $request)
    {

        $coordinacion = (new Coordinacion)->datos_coordinacion($request['direccion']);

        return $coordinacion;

    }
    public function accionesano(Request $request)
    {

        $coordinacion = (new Acciones)->accionesano();

        return $coordinacion;

    }

    public function accionesano2(Request $request)
    {

        $coordinacion = (new Acciones)->accionesano2();

        return $coordinacion;

    }
    public function accionesano3(Request $request)
    {

        $coordinacion = (new Acciones)->accionesano3();

        return $coordinacion;

    }

    public function totalescomunidadxcomuna(Request $request){
        try {

            if ($request->ajax()) {

                $data = (new Acciones)->totalescomunidadxcomuna($request['comuna'],$request['fecha_desde'],$request['fecha_hasta']);

//var_dump($data);
//exit();

                return datatables()->of($data)




                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }
    public function totalescomunidadxcomuna2(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Acciones)->totalescomunidadxcomuna2($request['comuna'],$request['fecha_desde'],$request['fecha_hasta']);
                return datatables()->of($data)
                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }


    public function totalestomos(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Acciones)->totalestomos($request['direccion'],$request['fecha_desde'],$request['fecha_hasta']);
                return datatables()->of($data)
                    ->rawColumns(['edit', 'view', 'del'])->toJson();
            }
        } catch (Throwable $e) {
            echo "Captured Throwable: " . $e->getMessage(), "\n";
        }
    }

    public function totalaccionesyactividades(){
        $total = (new Acciones)->totalaccionesyactividades();
        return $total;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // $count_notification = (new User)->count_noficaciones_user();
        $input = $request->all();
        // var_dump($input);
        // exit();
        $solicitud_Update = Acciones::find($id);
        $solicitud_Update->direccion_id=$input['direccion_id'];
        $solicitud_Update->coordinacion_sala_id=$input['coordinacion_id'];
        $solicitud_Update->parroquia_id=$input['parroquia_id'];
        $solicitud_Update->comuna_id=$input['comuna_id'];
        $solicitud_Update->jefecomunidad_id=$input['jefecomunidad_id'];
        $solicitud_Update->nombre=$input['nombre'];
        $solicitud_Update->direccion=$input['direccion'];
        $solicitud_Update->fechainicial=$input['fechainicial'];
        $solicitud_Update->fechafinal=$input['fechafinal'];
        $solicitud_Update->vocero=$input['vocero'];
        $solicitud_Update->telefono=$input['telefono'];
        if ((Auth::user()->rols_id === 14) || (Auth::user()->rols_id === 13) || (Auth::user()->rols_id === 1)) {
        $solicitud_Update->observacion=$input['observacion'];
        $solicitud_Update->state=$input['state'];
        }

        $solicitud_Update->created_at=\Carbon\Carbon::now('America/Caracas');
        $solicitud_Update->updated_at=\Carbon\Carbon::now('America/Caracas');
        $imagen = isset($input["image"]) ? $input["image"] : null;
        $imagen2 = isset($input["image2"]) ? $input["image2"] : null;
        if($imagen === null){
            $variable = "no hacemos nada";
            var_dump($variable);
        }else{
            $nombreImagen = uniqid() . '.' . $imagen->getClientOriginalExtension();
            $ruta = $imagen->move('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public/Evidencias/', $nombreImagen);
            $fullPath = str_replace('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public', '', (string) $ruta);
            $solicitud_Update->evidencia_path= $fullPath;
        }
        if($imagen2 === null){
            $variable = "no hacemos nada";
            var_dump($variable);
        }else{
            $nombreImagen = uniqid() . '.' . $imagen2->getClientOriginalExtension();
            $ruta2 = $imagen2->move('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public/Evidencias/', $nombreImagen);
            $fullPath2 = str_replace('/home/innovacion/'.env('PATH_API_3001').'/EncuestasAPI/public', '', (string) $ruta2);
            $solicitud_Update->evidencia_path2= $fullPath2;
        }

        $solicitud_Update->save();

        return redirect('/acciones/list');
    }

    private function update_image($request, $avatar_viejo, &$user_Update)
    {
        /** Se actualizan todos los datos solicitados por el Cliente
         *  y eliminamos del Storage/avatars, el archivo indicado.
         */
        if ($request->hasFile('avatar')) {
            $esta = file_exists(public_path('/storage/avatars/' . $avatar_viejo));
            if ($avatar_viejo != 'default.jpg' && $esta) {
                unlink(public_path('/storage/avatars/' . $avatar_viejo));
            }
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            \Image::make($avatar)->resize(300, 300)
                ->save(public_path('/storage/avatars/' . $filename));
            $user_Update->avatar = $filename;
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $user_delete = Acciones::find($id);
        $user_delete->state = 'ELIMINADO';
        $user_delete->save();

        return redirect('/acciones/list');
    }

    public function usuarioRol(Request $request)
    {
        if ($request->ajax()) {
            $countUserRol = (new User)->count_User_Rol();
            return response()->json($countUserRol);
        }
    }

    public function notificationsUser(Request $request)
    {
        if ($request->ajax()) {
            $countNotificationsUsers = (new User)->count_User_notifications();
            return response()->json($countNotificationsUsers);
        }
    }
    public function solicitudTipo(Request $request)
    {
        if ($request->ajax()) {
            $countSolicitud = (new Solicitud)->count_solictud();

            return response()->json($countSolicitud);
        }
    }
    public function solicitudTipo2(Request $request)
    {

            $countSolicitud = (new Solicitud)->count_solictud2();

            return response()->json($countSolicitud);

    }
    public function solicitudTipo2PorFecha(Request $request)
        {
            $input = $request->all();
            $fechaDesde = $input['fecha_desde'];
            $fechaHasta = $input['fecha_hasta'];

            $countSolicitud = (new Solicitud)->count_solictud2PorFecha($fechaDesde, $fechaHasta);

            return response()->json($countSolicitud);

    }

    public function solicitudTipo3(Request $request)
    {

            $countSolicitud = (new Solicitud)->count_solictud3();

            return response()->json($countSolicitud);

    }

    public function solicitudTipo4(Request $request)
    {

            $countSolicitud = (new Solicitud)->count_solictud4();

            return response()->json($countSolicitud);

    }

    public function solicitudTipo4PorFecha(Request $request)
    {
            $input = $request->all();
            $fechaDesde = $input['fecha_desde'];
            $fechaHasta = $input['fecha_hasta'];
            $countSolicitud = (new Solicitud)->count_solictud4PorFecha($fechaDesde, $fechaHasta);

            return response()->json($countSolicitud);

    }
    public function solicitudTipo5(Request $request)
    {

            $countSolicitud = (new Solicitud)->count_solicitud5();
            $array = [$countSolicitud];
            return $array;

    }

    public function solicitudTipo5PorFecha(Request $request)
    {
            $input = $request->all();
            $fechaDesde = $input['fecha_desde'];
            $fechaHasta = $input['fecha_hasta'];
            $countSolicitud = (new Solicitud)->count_solicitud5PorFecha($fechaDesde, $fechaHasta);
            $array = [$countSolicitud];
            return $array;

    }
        public function solicitudTotalTipo(Request $request)
    {
        if ($request->ajax()) {
            $countTotalSolicitud = (new Solicitud)->count_total_solictud();
            return response($countTotalSolicitud);
        }
    }

    public function colorView()
    {
        $titulo_modulo = trans('message.users_action.cambiar_colores');
        $count_notification = (new User)->count_noficaciones_user();
        $array_color = (new Colores)->getColores();
        return view('User.color_view', compact('count_notification', 'titulo_modulo', 'array_color'));
    }

    public function colorChange(Request $request)
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        $colores = $user->colores;
        if ($request->dafault_color_01 == 'NO') {
            $colores['encabezado'] = $request->encabezado_user;
            $colores['menu'] = $request->menu_user;
            $colores['group_button'] = $request->group_button;
            $colores['back_button'] = $request->back_button;
            $user->colores = $colores;
            $user->save();
            session(['menu_color' => $request->menu_user]);
            session(['encabezado_color' => $request->encabezado_user]);
            session(['group_button_color' => $request->group_button]);
            session(['back_button_color' => $request->back_button]);
        } elseif ($request->dafault_color_01 == 'YES') {
            $colores['encabezado'] = '#5333ed';
            $colores['menu'] = '#0B0E66';
            $colores['group_button'] = '#5333ed';
            $colores['back_button'] = '#5333ed';
            $user->colores = $colores;
            $user->save();
            session(['menu_color' => '#0B0E66']);
            session(['encabezado_color' => '#5333ed']);
            session(['group_button_color' => '#5333ed']);
            session(['back_button_color' => '#5333ed']);
        } elseif ($request->dafault_color_01 == 'BLUE') {
            $colores['encabezado'] = '#81898f';
            $colores['menu'] = '#3e5f8a';
            $colores['group_button'] = '#474b4e';
            $colores['back_button'] = '#474b4e';
            $user->colores = $colores;
            $user->save();
            session(['menu_color' => '#3e5f8a']);
            session(['encabezado_color' => '#81898f']);
            session(['group_button_color' => '#474b4e']);
            session(['back_button_color' => '#474b4e']);
        } elseif ($request->dafault_color_01 == 'GREEN') {
            $colores['encabezado'] = '#0b9a93';
            $colores['menu'] = '#198c86';
            $colores['group_button'] = '#008080';
            $colores['back_button'] = '#008080';
            $user->colores = $colores;
            $user->save();
            session(['menu_color' => '#198c86']);
            session(['encabezado_color' => '#0b9a93']);
            session(['group_button_color' => '#008080']);
            session(['back_button_color' => '#008080']);
        } else {
            $colores['encabezado'] = '#000000';
            $colores['menu'] = '#000000';
            $colores['group_button'] = '#000000';
            $colores['back_button'] = '#000000';
            $user->colores = $colores;
            $user->save();
            session(['menu_color' => '#000000']);
            session(['encabezado_color' => '#000000']);
            session(['group_button_color' => '#000000']);
            session(['back_button_color' => '#000000']);
        }
        return redirect('/dashboard');
    }

    public function solicitudPrint(Request $request){
        return back();
    }

    public function getFinalizadas(Request $request){
    $solfinalizadas = (new Solicitud)->reportetotalcasosatendidosSALUD();
    return $solfinalizadas;
    }
    public function getFinalizadasConFecha(Request $request){
        $input = $request->all();
        $fechaDesde = $input['fecha_desde'];
        $fechaHasta = $input['fecha_hasta'];
        $solfinalizadas = (new Solicitud)->reportetotalcasosatendidosSALUDConFecha($fechaDesde, $fechaHasta);
        return $solfinalizadas;
        }

    public function getSolicitudesWAN(Request $request){
        $input = $request->all();
        $fechaDesde = isset($input['fechaDesde']) ? $input['fechaDesde'] : NULL;
        $fechaHasta = isset($input['fechaHasta']) ? $input['fechaHasta'] : NULL;
        $status = isset($input['status']) ? $input['status'] : NULL;
        $comuna = isset($input['comuna_id']) ? $input['comuna_id'] : NULL;
        $solicitudes = (new Solicitud)->solicitudesWAN($fechaDesde, $fechaHasta, $status, $comuna);
        return $solicitudes;
        }

    public function imprimirWAN(Request $request){
        $input = $request->all();
        $fechaDesde = isset($input['fechaDesde']) ? $input['fechaDesde'] : NULL;
        $fechaHasta = isset($input['fechaHasta']) ? $input['fechaHasta'] : NULL;
        $status = isset($input['status']) ? $input['status'] : NULL;
        $comuna = isset($input['comuna_id']) ? $input['comuna_id'] : NULL;
        $solicitudes = (new Solicitud)->solicitudesWAN($fechaDesde, $fechaHasta, $status, $comuna);

        $html =
        <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
            </style>
        </head>
        <body>
        <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">

        <h3 style="text-align:left;">Direccion Politicas Sociales</h3>
        <div>

        <table>
            <tr>
                <th>Correlativo</th>
            </tr>
            <td>
            <h4 style="text-align:left;">$solicitudes</h4>
            </td>
        </table>
        </div>

        </body>
        </html>
        HTML;
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('legal', 'portrait');
        $dompdf->render();

        // Nombre del archivo
        $filename = 'reporte_' . time() . '.pdf';

        // Guardar el PDF en el almacenamiento
        $path = Storage::disk('local')->put('PDF/' . $filename, $dompdf->output());

        // Obtener la ruta completa del archivo
        $fullPath = storage_path('app/' . $path);

        return $fullPath;

    }
    public function imprimir(Request $request)
    {
        $activardenuncia = "";
        $activarqueja = "";
        $activarsugerencia = "";
        $activarasesoria = "";
        $activarreclamos = "";
        $activarpeticiones = "";
        $activarrecaudoCedula = "";
        $activarrecaudoMotivo = "";
        $activarrecaudoVideo = "";
        $activarrecaudoFoto = "";
        $activarrecaudoGrabacion = "";
        $activarrecaudoCedulaTestigo = "";
        $activarrecaudoCartaResidencia = "";
        $activarrecaudoInforme = "";

        $activarrecaudoRecipe = "";
        $activarrecaudoInforme = "";
        $activarrecaudoBeneficiario = "";
        $activarrecaudoPresupuesto = "";
        $activarevidenciafotobeneficiario = "";
        $activarpermisoinhumacion = "";
        $activarcertificadodefuncion = "";
        $activarordenExamen = "";
        $activarordenEstudio = "";

        $quejasRelato = NULL;
        $quejasObservacion = NULL;
        $quejasExpliquePresentada = NULL;
        $quejasExpliqueCompetencia = NULL;

        $dompdf = new DOMPDF();
        $request = $request->all();
        $idsolicitud = $request["idsolicitud"];
        $solicitud = Solicitud::find($idsolicitud);
        $numeroSolicitud = isset($solicitud_salud_id) ? $solicitud_salud_id : $solicitud->solicitud_atc_id;

        $user = User::find($solicitud->users_id);
        $nombreUsuario = $user->name;

        $direccionid = $solicitud->direccion_id;
        if($direccionid != NULL){
            $direccionAsignada = (new Direccion)->datos_direccion()[$direccionid];
        }else{
            $direccionAsignada = NULL;
        }

        $idestado = isset($solicitud["estado_id"]) ? $solicitud["estado_id"] : NULL;
        $idmunicipio = isset($solicitud["municipio_id"]) ? $solicitud["municipio_id"] : NULL;
        $idparroquia = isset($solicitud["parroquia_id"]) ? $solicitud["parroquia_id"] : NULL;
        $idcomuna = isset($solicitud["comuna_id"]) ? $solicitud["comuna_id"] : NULL;
        $idcomunidad = isset($solicitud["comunidad_id"]) ? $solicitud["comunidad_id"] : NULL;

        $fecha = date('d-m-Y', strtotime($solicitud->fecha));
        $dia = date('d', strtotime($solicitud->fecha));
        $mes = date('m', strtotime($solicitud->fecha));
        $anno = date('Y', strtotime($solicitud->fecha));
        $hora = date('h:i A', strtotime($solicitud->fecha));

        $nombreDenunciado = "";
        $testigoDenunciado = "";
        $quejasRelato = "";
        $quejasObservacion = "";
        $quejasExpliquePresentada = "";
        $quejasExpliqueCompetencia = "";
        $cedulaDenunciado = "";

        $municipioID = $solicitud->municipio_id;
        $jefecomunidadID = $solicitud->jefecomunidad_id;
        $jefecomunidad = (new JefeComunidad)->getJefe2($jefecomunidadID);
        $jefe = $jefecomunidad->first();

        $estado = (new Solicitud)->nombreestado($idestado, $idmunicipio, $idparroquia, $idcomuna, $idcomunidad);
        foreach($estado as $estado2)

        if($municipioID == 2){
            $estadoSolicitud = NULL;
            $municipio = NULL;
            $parroquia = NULL;
            $comuna = NULL;
            $comunidad = NULL;
        }else{
            $estadoSolicitud = $estado2->estado2 ?? NULL;
            $municipio = $estado2->municipio ?? NULL;
            $parroquia = $estado2->parroquia ?? NULL;
            $comuna = $estado2->comuna ?? NULL;
            $comunidad = $estado2->comunidad ?? NULL;
            }

        if($jefe == NULL){
            $nombreJefeCom = 'N/A';
            $telJefeCom = 'N/A';
            $nombreUbch = 'N/A';
            $nombreJefUbch = 'N/A';
            $telefonoJefeUbch = 'N/A';
        }

        if($municipioID == 1 && $jefe != NULL){
            $nombreJefeCom = $jefe->Nombre_Jefe_Comunidad;
            $telJefeCom = $jefe->Telefono_Jefe_Comunidad;
            $nombreUbch = $jefe->Nombre_Ubch;
            $nombreJefUbch = $jefe->Nombre_Jefe_Ubch;
            $telefonoJefeUbch = $jefe->Telefono_Jefe_Ubch;
        }

        if($municipioID == 2){
            $estadoSolicitud = 'PAEZ';
            $municipio = 'FORANEO';
            $parroquia = 'N/A';
            $comuna = 'N/A';
            $comunidad = 'N/A';
            $nombreJefeCom = 'N/A';
            $telJefeCom = 'N/A';
            $nombreUbch = 'N/A';
            $nombreJefUbch = 'N/A';
            $telefonoJefeUbch = 'N/A';
        }

        $cwd = getcwd();
        $valor = "";
        $htmlsolicitud = "";


        if ($solicitud["tipo_solicitud_id"] === 1) {
            $activardenuncia = "checked";
            $valor = "Denuncia";
            $recaudos = $solicitud->recaudos;
            $recaudos = json_decode($recaudos, true);

            $fotoRecaudos = $recaudos[0]["foto"];
            $videoRecaudos = $recaudos[0]["video"];
            $motivoRecaudos = $recaudos[0]["motivo"];
            $testigoRecaudos = $recaudos[0]["testigo"];
            $grabacionRecaudos = $recaudos[0]["grabacion"];
            $cedulaRecaudos = $recaudos[0]["cedula"];
            $residenciaRecaudos = $recaudos[0]["residencia"];

            $denunciado = $solicitud->denunciado;
            $denunciado = json_decode($denunciado, true);

            $cedulaDenunciado = $denunciado[0]["cedula"];
            $testigoDenunciado = $denunciado[0]["testigo"];
            $nombreDenunciado = $denunciado[0]["nombre"];

            $denuncia = $solicitud->denuncia;
            $denuncia = json_decode($denuncia, true);
            $quejasRelato = $denuncia[0]["relato"];
            $quejasObservacion = $denuncia[0]["observacion"];
            $quejasExpliquePresentada = $denuncia[0]["expliquepresentada"];
            $quejasExpliqueCompetencia = $denuncia[0]["explique competencia"];

            if($fotoRecaudos === "on"){
                $activarrecaudoFoto = "checked";
            }
            if($videoRecaudos === "on"){
                $activarrecaudoVideo = "checked";
            }
            if($motivoRecaudos === "on"){
                $activarrecaudoMotivo = "checked";
            }
            if($testigoRecaudos === "on"){
                $activarrecaudoCedulaTestigo = "checked";
            }
            if($grabacionRecaudos === "on"){
                $activarrecaudoGrabacion = "checked";
            }
            if($cedulaRecaudos === "on"){
                $activarrecaudoCedulaTestigo = "checked";
            }
            if($residenciaRecaudos === "on"){
                $activarrecaudoCartaResidencia = "checked";
            }
            $htmlsolicitud = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Planilla</title>
                <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
            <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">
                <table>
                    <tr>
                        <th>Numero de Registro $numeroSolicitud</th>
                        <th>Oficina de Atencion Ciudadana</th>
                        <th>Planilla de Solicitud</th>
                        <th>Dia: $dia</th>
                        <th>Mes: $mes</th>
                        <th>Año: $anno</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Denuncia</th>
                        <th>Queja</th>
                        <th>Sugerencia</th>
                        <th>Asesoria</th>
                        <th>Reclamos</th>
                        <th>Peticion</th>
                        <tr>
                        <td><input type="checkbox" $activardenuncia></td>
                        <td><input type="checkbox" $activarqueja></td>
                        <td><input type="checkbox" $activarsugerencia></td>
                        <td><input type="checkbox" $activarasesoria></td>
                        <td><input type="checkbox" $activarreclamos></td>
                        <td><input type="checkbox" $activarpeticiones></td>
                        </tr>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Datos del ciudadano(a) Solicitante</th>
                    </tr>
                </table>
                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="text-primary" >Nombre Y Apellido</th>
                        <th>Cedula</th>
                        <th>Telefono</th>
                        <th>Telefono Casa</th>
                        <th>Correo Electronico</th>
                        <th>Sexo</th>
                        <th>Estado Civil</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Ocupacion O/U Oficio</th>
                    </tr>
                    <tr>
                        <td>$solicitud->nombre</td>
                        <td>$solicitud->cedula</td>
                        <td>$solicitud->telefono</td>
                        <td>$solicitud->telefono2</td>
                        <td>$solicitud->email</td>
                        <td>$solicitud->sexo</td>
                        <td>$solicitud->edocivil</td>
                        <td>$solicitud->fechaNacimiento</td>
                        <td>$solicitud->nivelestudio</td>
                        <td>$solicitud->profesion</td>
                    </tr>
            </table>

            <table>
            <tr >
                        <th class="text-primary" >Estado</th>
                        <th>Municipio</th>
                        <th>Parroquia</th>
                        <th>Comuna</th>
                        <th>Comunidad</th>
                        <th>Direccion Habitacion</th>
                        <th>Tipo de Solicitud</th>
                    </tr>
                    <tr>
                        <td>$estadoSolicitud</td>
                        <td>$municipio</td>
                        <td>$parroquia</td>
                        <td>$comuna</td>
                        <td>$comunidad</td>
                        <td>$solicitud->direccion</td>
                        <td>$valor</td>
                    </tr>
                        </table>
                        <table>
                    <tr>
                        <th>Recaudos de la Peticion</th>
                    </tr>
            </table>

                <table>
                    <tr>
                        <!-- <th>Asignacion</th> -->
                        <th>Direccion Asignada</th>
                        <!-- <th>Coordinacion Asignada</th> -->
                    </tr>
                    <tr>
                        <!-- <td>$solicitud->asignacion</td> -->
                        <td>$direccionAsignada</td>
                        <!-- <td></td> -->
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Datos de Denuncia, Reclamo o Queja</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Cedula de Denunciado</th>
                        <!-- <th>Tipo de Registro</th> -->
                        <th>Nombre del Denunciado</th>
                        <th>Testigos</th>
                        <!-- <th>Edad</th>
                        <th>Estado Civil</th>
                        <th>Fecha de nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Profesion</th>
                        <th>Parentesco</th> -->
                    </tr>
                    <tr>
                        <td>$cedulaDenunciado</td>
                        <!-- <td></td> -->
                        <td>$nombreDenunciado</td>
                        <td>$testigoDenunciado</td>
                        <!-- <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> -->
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de Hechos</th>
                        <th>Observacion</th>
                        <th>Denuncia Presentada</th>
                        <th>Explique</th>
                    </tr>
                    <tr>
                        <td>$quejasRelato</td>
                        <td>$quejasObservacion</td>
                        <td>$quejasExpliquePresentada</td>
                        <td>$quejasExpliqueCompetencia</td>
                    </tr>
                </table>
                    <table>
                        <tr><th>
                            Recaudos de la Solicitud
                        </th></tr>
                    </table>

                    <table>
                    <tr>
                        <th>Copia de Cedula</th>
                        <th>Carta Exposicion de Motivo</th>
                        <th>Video</th>
                        <th>Foto</th>
                        <th>Grabacion</th>
                        <th>Cedula Testigo</th>
                        <th>Carta de Residencia</th>
                    </tr>
                    <tr>
                        <td><input type='checkbox' $activarrecaudoCedula></td>
                        <td><input type='checkbox' $activarrecaudoMotivo></td>
                        <td><input type='checkbox' $activarrecaudoVideo></td>
                        <td><input type='checkbox' $activarrecaudoFoto></td>
                        <td><input type='checkbox' $activarrecaudoGrabacion></td>
                        <td><input type='checkbox' $activarrecaudoCedulaTestigo></td>
                        <td><input type='checkbox' $activarrecaudoCartaResidencia></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th style="padding:1rem;">Declaro que los datos suministrados son fidedignos y estoy en conocimiento que cualquier falta o falsedad, en los mismos involucra sanciones o a la no aceptacion de la solicitud.</th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Solicitante</th>
                        <th>Huella Dactilar</th>
                    </tr>
                    <tr>
                        <td style="padding:2rem;"></td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Solo para ser llenado por la Unidad Receptora</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Prioridad del tramite</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td>Alta<input type='checkbox'></td>
                        <td>Media<input type='checkbox'></td>
                        <td>Baja<input type='checkbox'></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de el tramite</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Nombre del Funcionario Receptor</th>
                        <!-- <th>Cedula de Identidad</th> -->
                        <th>Sello y Firma</th>
                    </tr>
                    <tr>
                        <td style="padding: 15px">$nombreUsuario</td>
                        <!-- <td></td> -->
                        <td></td>
                    </tr>
                </table>

                <table style="margin-top: 20px">
                    <tr>
                        <th>Oficina de Atencion Ciudadana <span style="text-align: right">Numero de Registro $numeroSolicitud</span></th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>
                            Planilla de Solicitud:
                        </th>
                    </tr>
                </table>

                    <table>
                        <tr>
                            <th>Denuncia</th>
                            <th>Queja</th>
                            <th>Sugerencia</th>
                            <th>Asesoria</th>
                            <th>Reclamos</th>
                            <th>Peticion</th>
                            <tr>
                            <td><input type="checkbox" $activardenuncia></td>
                            <td><input type="checkbox" $activarqueja></td>
                            <td><input type="checkbox" $activarsugerencia></td>
                            <td><input type="checkbox" $activarasesoria></td>
                            <td><input type="checkbox" $activarreclamos></td>
                            <td><input type="checkbox" $activarpeticiones></td>
                            </tr>
                        </tr>
                    </table>
                </table>
                <table>
                        <tr>
                            <th>Fecha de Solicitud</th>
                            <th>Hora</th>
                        <th>Nombre y Apellido del Ciudadano Solicitante</th>
                        <th>Nombre del Funcionario Receptor</th>
                    </tr>
                    <tr>
                        <td>$fecha</td>
                        <td>$hora</td>
                        <td>$solicitud->nombre</td>
                        <td>$nombreUsuario</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Funcionario Receptor</th>
                        <th>Sello</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                        <td></td>
                    </tr>
                </table>
                <h5 style="text-align: center">Usted podra solicitar informacion sobre su solicitud en la Oficina de Atencion Ciudadana a traves de los telefonos (0414 1572028) (0412 5526701)</h5>
                <h5 style="text-align: center">Todos los tramites realizados ante esta oficina son absolutamente gratuitos</h5>
                    </body>
                    </html>
            HTML;
        }

        if ($solicitud["tipo_solicitud_id"] === 2) {
            $activarqueja = "checked";
            $valor = "Queja";
            $recaudos = $solicitud->recaudos;
            $recaudos = json_decode($recaudos, true);
            $denunciado = $solicitud->denunciado;
            $denunciado = json_decode($denunciado, true);

            $denunciado = $solicitud->denunciado;
            $denunciado = json_decode($denunciado, true);

            $cedulaDenunciado = $denunciado[0]["cedula"];
            $testigoDenunciado = $denunciado[0]["testigo"];
            $nombreDenunciado = $denunciado[0]["nombre"];

            $quejas = $solicitud->quejas;
            $quejas = json_decode($quejas, true);
            $quejasRelato = $quejas[0]["relato"];
            $quejasObservacion = $quejas[0]["observacion"];
            $quejasExpliquePresentada = $quejas[0]["expliquepresentada"];
            $quejasExpliqueCompetencia = $quejas[0]["explique competencia"];

            $fotoRecaudos = $recaudos[0]["foto"];
            $videoRecaudos = $recaudos[0]["video"];
            $motivoRecaudos = $recaudos[0]["motivo"];
            $testigoRecaudos = $recaudos[0]["testigo"];
            $grabacionRecaudos = $recaudos[0]["grabacion"];
            $cedulaRecaudos = $recaudos[0]["cedula"];
            $residenciaRecaudos = $recaudos[0]["residencia"];

            if($fotoRecaudos === "on"){
                $activarrecaudoFoto = "checked";
            }
            if($videoRecaudos === "on"){
                $activarrecaudoVideo = "checked";
            }
            if($motivoRecaudos === "on"){
                $activarrecaudoMotivo = "checked";
            }
            if($testigoRecaudos === "on"){
                $activarrecaudoCedulaTestigo = "checked";
            }
            if($grabacionRecaudos === "on"){
                $activarrecaudoGrabacion = "checked";
            }
            if($cedulaRecaudos === "on"){
                $activarrecaudoCedula = "checked";
            }
            if($residenciaRecaudos === "on"){
                $activarrecaudoCartaResidencia = "checked";
            }
            $htmlsolicitud = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Planilla</title>
                <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
            <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">
                <table>
                    <tr>
                        <th>Numero de Registro $numeroSolicitud</th>
                        <th>Oficina de Atencion Ciudadana</th>
                        <th>Planilla de Solicitud</th>
                        <th>Dia: $dia</th>
                        <th>Mes: $mes</th>
                        <th>Año: $anno</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Denuncia</th>
                        <th>Queja</th>
                        <th>Sugerencia</th>
                        <th>Asesoria</th>
                        <th>Reclamos</th>
                        <th>Peticion</th>
                        <tr>
                        <td><input type="checkbox" $activardenuncia></td>
                        <td><input type="checkbox" $activarqueja></td>
                        <td><input type="checkbox" $activarsugerencia></td>
                        <td><input type="checkbox" $activarasesoria></td>
                        <td><input type="checkbox" $activarreclamos></td>
                        <td><input type="checkbox" $activarpeticiones></td>
                        </tr>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Datos del ciudadano(a) Solicitante</th>
                    </tr>
                </table>
                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="text-primary" >Nombre Y Apellido</th>
                        <th>Cedula</th>
                        <th>Telefono</th>
                        <th>Telefono Casa</th>
                        <th>Correo Electronico</th>
                        <th>Sexo</th>
                        <th>Estado Civil</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Ocupacion O/U Oficio</th>
                    </tr>
                    <tr>
                        <td>$solicitud->nombre</td>
                        <td>$solicitud->cedula</td>
                        <td>$solicitud->telefono</td>
                        <td>$solicitud->telefono2</td>
                        <td>$solicitud->email</td>
                        <td>$solicitud->sexo</td>
                        <td>$solicitud->edocivil</td>
                        <td>$solicitud->fechaNacimiento</td>
                        <td>$solicitud->nivelestudio</td>
                        <td>$solicitud->profesion</td>
                    </tr>
            </table>

            <table>
            <tr >
                        <th class="text-primary" >Estado</th>
                        <th>Municipio</th>
                        <th>Parroquia</th>
                        <th>Comuna</th>
                        <th>Comunidad</th>
                        <th>Direccion Habitacion</th>
                        <th>Tipo de Solicitud</th>
                    </tr>
                    <tr>
                        <td>$estadoSolicitud</td>
                        <td>$municipio</td>
                        <td>$parroquia</td>
                        <td>$comuna</td>
                        <td>$comunidad</td>
                        <td>$solicitud->direccion</td>
                        <td>$valor</td>
                    </tr>
                        </table>
                        <table>
                    <tr>
                        <th>Recaudos de la Peticion</th>
                    </tr>
            </table>

                <table>
                    <tr>
                        <!-- <th>Asignacion</th> -->
                        <th>Direccion Asignada</th>
                        <!-- <th>Coordinacion Asignada</th> -->
                    </tr>
                    <tr>
                        <!-- <td>$solicitud->asignacion</td> -->
                        <td>$direccionAsignada</td>
                        <!-- <td></td> -->
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Datos de Denuncia, Reclamo o Queja</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Cedula de Denunciado</th>
                        <!-- <th>Tipo de Registro</th> -->
                        <th>Nombre del Denunciado</th>
                        <th>Testigos</th>
                        <!-- <th>Edad</th>
                        <th>Estado Civil</th>
                        <th>Fecha de nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Profesion</th>
                        <th>Parentesco</th> -->
                    </tr>
                    <tr>
                        <td>$cedulaDenunciado</td>
                        <!-- <td></td> -->
                        <td>$nombreDenunciado</td>
                        <td>$testigoDenunciado</td>
                        <!-- <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> -->
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de Hechos</th>
                        <th>Observacion</th>
                        <th>Denuncia Presentada</th>
                        <th>Explique</th>
                    </tr>
                    <tr>
                        <td>$quejasRelato</td>
                        <td>$quejasObservacion</td>
                        <td>$quejasExpliquePresentada</td>
                        <td>$quejasExpliqueCompetencia</td>
                    </tr>
                </table>
                    <table>
                        <tr><th>
                            Recaudos de la Solicitud
                        </th></tr>
                    </table>

                    <table>
                    <tr>
                        <th>Copia de Cedula</th>
                        <th>Carta Exposicion de Motivo</th>
                        <th>Video</th>
                        <th>Foto</th>
                        <th>Grabacion</th>
                        <th>Cedula Testigo</th>
                        <th>Carta de Residencia</th>
                    </tr>
                    <tr>
                        <td><input type='checkbox' $activarrecaudoCedula></td>
                        <td><input type='checkbox' $activarrecaudoMotivo></td>
                        <td><input type='checkbox' $activarrecaudoVideo></td>
                        <td><input type='checkbox' $activarrecaudoFoto></td>
                        <td><input type='checkbox' $activarrecaudoGrabacion></td>
                        <td><input type='checkbox' $activarrecaudoCedulaTestigo></td>
                        <td><input type='checkbox' $activarrecaudoCartaResidencia></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th style="padding:1rem;">Declaro que los datos suministrados son fidedignos y estoy en conocimiento que cualquier falta o falsedad, en los mismos involucra sanciones o a la no aceptacion de la solicitud.</th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Solicitante</th>
                        <th>Huella Dactilar</th>
                    </tr>
                    <tr>
                        <td style="padding:2rem;"></td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Solo para ser llenado por la Unidad Receptora</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Prioridad del tramite</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td>Alta<input type='checkbox'></td>
                        <td>Media<input type='checkbox'></td>
                        <td>Baja<input type='checkbox'></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de el tramite</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Nombre del Funcionario Receptor</th>
                        <!-- <th>Cedula de Identidad</th> -->
                        <th>Sello y Firma</th>
                    </tr>
                    <tr>
                        <td style="padding: 15px">$nombreUsuario</td>
                        <!-- <td></td> -->
                        <td></td>
                    </tr>
                </table>
                <p>----------------------------------------------------------------------------------------------------------------------------------------</p>
                <table style="margin-top: 20px">
                    <tr>
                        <th>Oficina de Atencion Ciudadana <span style="text-align: right">Numero de Registro $numeroSolicitud</span></th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>
                            Planilla de Solicitud:
                        </th>
                    </tr>
                </table>

                    <table>
                        <tr>
                            <th>Denuncia</th>
                            <th>Queja</th>
                            <th>Sugerencia</th>
                            <th>Asesoria</th>
                            <th>Reclamos</th>
                            <th>Peticion</th>
                            <tr>
                            <td><input type="checkbox" $activardenuncia></td>
                            <td><input type="checkbox" $activarqueja></td>
                            <td><input type="checkbox" $activarsugerencia></td>
                            <td><input type="checkbox" $activarasesoria></td>
                            <td><input type="checkbox" $activarreclamos></td>
                            <td><input type="checkbox" $activarpeticiones></td>
                            </tr>
                        </tr>
                    </table>
                </table>
                <table>
                        <tr>
                            <th>Fecha de Solicitud</th>
                            <th>Hora</th>
                        <th>Nombre y Apellido del Ciudadano Solicitante</th>
                        <th>Nombre del Funcionario Receptor</th>
                    </tr>
                    <tr>
                        <td>$fecha</td>
                        <td>$hora</td>
                        <td>$solicitud->nombre</td>
                        <td>$nombreUsuario</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Funcionario Receptor</th>
                        <th>Sello</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                        <td></td>
                    </tr>
                </table>
                <h5 style="text-align: center">Usted podra solicitar informacion sobre su solicitud en la Oficina de Atencion Ciudadana a traves de los telefonos (0414 1572028) (0412 5526701)</h5>
                <h5 style="text-align: center">Todos los tramites realizados ante esta oficina son absolutamente gratuitos</h5>
                    </body>
                    </html>
            HTML;
        }

        if ($solicitud["tipo_solicitud_id"] === 3) {
            $activarreclamos = "checked";
            $valor = "Reclamos";

            $recaudos = $solicitud->recaudos;
            $recaudos = json_decode($recaudos, true);
            $denunciado = $solicitud->denunciado;
            $denunciado = json_decode($denunciado, true);

            $cedulaDenunciado = $denunciado[0]["cedula"];
            $nombreDenunciado = $denunciado[0]["nombre"];
            $testigoDenunciado = $denunciado[0]["testigo"];

            $quejas = $solicitud->reclamo;
            $quejas = json_decode($quejas, true);
            $quejasRelato = $quejas[0]["relato"];
            $quejasObservacion = $quejas[0]["observacion"];
            $quejasExpliquePresentada = $quejas[0]["expliquepresentada"];
            $quejasExpliqueCompetencia = $quejas[0]["explique competencia"];

            $fotoRecaudos = $recaudos[0]["foto"];
            $videoRecaudos = $recaudos[0]["video"];
            $motivoRecaudos = $recaudos[0]["motivo"];
            $testigoRecaudos = $recaudos[0]["testigo"];
            $grabacionRecaudos = $recaudos[0]["grabacion"];
            $cedulaRecaudos = $recaudos[0]["cedula"];
            $residenciaRecaudos = $recaudos[0]["residencia"];

            if($fotoRecaudos === "on"){
                $activarrecaudoFoto = "checked";
            }
            if($videoRecaudos === "on"){
                $activarrecaudoVideo = "checked";
            }
            if($motivoRecaudos === "on"){
                $activarrecaudoMotivo = "checked";
            }
            if($testigoRecaudos === "on"){
                $activarrecaudoCedulaTestigo = "checked";
            }
            if($grabacionRecaudos === "on"){
                $activarrecaudoGrabacion = "checked";
            }
            if($cedulaRecaudos === "on"){
                $activarrecaudoCedula = "checked";
            }
            if($residenciaRecaudos === "on"){
                $activarrecaudoCartaResidencia = "checked";
            }
            $htmlsolicitud = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Planilla</title>
                <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
            <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">
                <table>
                    <tr>
                        <th>Numero de Registro $numeroSolicitud</th>
                        <th>Oficina de Atencion Ciudadana</th>
                        <th>Planilla de Solicitud</th>
                        <th>Dia: $dia</th>
                        <th>Mes: $mes</th>
                        <th>Año: $anno</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Denuncia</th>
                        <th>Queja</th>
                        <th>Sugerencia</th>
                        <th>Asesoria</th>
                        <th>Reclamos</th>
                        <th>Peticion</th>
                        <tr>
                        <td><input type="checkbox" $activardenuncia></td>
                        <td><input type="checkbox" $activarqueja></td>
                        <td><input type="checkbox" $activarsugerencia></td>
                        <td><input type="checkbox" $activarasesoria></td>
                        <td><input type="checkbox" $activarreclamos></td>
                        <td><input type="checkbox" $activarpeticiones></td>
                        </tr>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Datos del ciudadano(a) Solicitante</th>
                    </tr>
                </table>
                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="text-primary" >Nombre Y Apellido</th>
                        <th>Cedula</th>
                        <th>Telefono</th>
                        <th>Telefono Casa</th>
                        <th>Correo Electronico</th>
                        <th>Sexo</th>
                        <th>Estado Civil</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Ocupacion O/U Oficio</th>
                    </tr>
                    <tr>
                        <td>$solicitud->nombre</td>
                        <td>$solicitud->cedula</td>
                        <td>$solicitud->telefono</td>
                        <td>$solicitud->telefono2</td>
                        <td>$solicitud->email</td>
                        <td>$solicitud->sexo</td>
                        <td>$solicitud->edocivil</td>
                        <td>$solicitud->fechaNacimiento</td>
                        <td>$solicitud->nivelestudio</td>
                        <td>$solicitud->profesion</td>
                    </tr>
            </table>

            <table>
            <tr >
                        <th class="text-primary" >Estado</th>
                        <th>Municipio</th>
                        <th>Parroquia</th>
                        <th>Comuna</th>
                        <th>Comunidad</th>
                        <th>Direccion Habitacion</th>
                        <th>Tipo de Solicitud</th>
                    </tr>
                    <tr>
                        <td>$estadoSolicitud</td>
                        <td>$municipio</td>
                        <td>$parroquia</td>
                        <td>$comuna</td>
                        <td>$comunidad</td>
                        <td>$solicitud->direccion</td>
                        <td>$valor</td>
                    </tr>
                        </table>
                        <table>
                    <tr>
                        <th>Recaudos de la Peticion</th>
                    </tr>
            </table>

                <table>
                    <tr>
                        <!-- <th>Asignacion</th> -->
                        <th>Direccion Asignada</th>
                        <!-- <th>Coordinacion Asignada</th> -->
                    </tr>
                    <tr>
                        <!-- <td>$solicitud->asignacion</td> -->
                        <td>$direccionAsignada</td>
                        <!-- <td></td> -->
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Datos de Denuncia, Reclamo o Queja</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Cedula de Denunciado</th>
                        <!-- <th>Tipo de Registro</th> -->
                        <th>Nombre del Denunciado</th>
                        <th>Testigos</th>
                        <!-- <th>Edad</th>
                        <th>Estado Civil</th>
                        <th>Fecha de nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Profesion</th>
                        <th>Parentesco</th> -->
                    </tr>
                    <tr>
                        <td>$cedulaDenunciado</td>
                        <!-- <td></td> -->
                        <td>$nombreDenunciado</td>
                        <td>$testigoDenunciado</td>
                        <!-- <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> -->
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de Hechos</th>
                        <th>Observacion</th>
                        <th>Denuncia Presentada</th>
                        <th>Explique</th>
                    </tr>
                    <tr>
                        <td>$quejasRelato</td>
                        <td>$quejasObservacion</td>
                        <td>$quejasExpliquePresentada</td>
                        <td>$quejasExpliqueCompetencia</td>
                    </tr>
                </table>
                    <table>
                        <tr><th>
                            Recaudos de la Solicitud
                        </th></tr>
                    </table>

                    <table>
                    <tr>
                        <th>Copia de Cedula</th>
                        <th>Carta Exposicion de Motivo</th>
                        <th>Video</th>
                        <th>Foto</th>
                        <th>Grabacion</th>
                        <th>Cedula Testigo</th>
                        <th>Carta de Residencia</th>
                    </tr>
                    <tr>
                        <td><input type='checkbox' $activarrecaudoCedula></td>
                        <td><input type='checkbox' $activarrecaudoMotivo></td>
                        <td><input type='checkbox' $activarrecaudoVideo></td>
                        <td><input type='checkbox' $activarrecaudoFoto></td>
                        <td><input type='checkbox' $activarrecaudoGrabacion></td>
                        <td><input type='checkbox' $activarrecaudoCedulaTestigo></td>
                        <td><input type='checkbox' $activarrecaudoCartaResidencia></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th style="padding:1rem;">Declaro que los datos suministrados son fidedignos y estoy en conocimiento que cualquier falta o falsedad, en los mismos involucra sanciones o a la no aceptacion de la solicitud.</th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Solicitante</th>
                        <th>Huella Dactilar</th>
                    </tr>
                    <tr>
                        <td style="padding:2rem;"></td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Solo para ser llenado por la Unidad Receptora</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Prioridad del tramite</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td>Alta<input type='checkbox'></td>
                        <td>Media<input type='checkbox'></td>
                        <td>Baja<input type='checkbox'></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de el tramite</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Nombre del Funcionario Receptor</th>
                        <!-- <th>Cedula de Identidad</th> -->
                        <th>Sello y Firma</th>
                    </tr>
                    <tr>
                        <td style="padding: 10px">$nombreUsuario</td>
                        <!-- <td></td> -->
                        <td></td>
                    </tr>
                </table>
                <p>----------------------------------------------------------------------------------------------------------------------------------------</p>
                <table style="margin-top: 10px">
                    <tr>
                        <th>Oficina de Atencion Ciudadana <span style="text-align: right">Numero de Registro $numeroSolicitud</span></th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>
                            Planilla de Solicitud:
                        </th>
                    </tr>
                </table>

                    <table>
                        <tr>
                            <th>Denuncia</th>
                            <th>Queja</th>
                            <th>Sugerencia</th>
                            <th>Asesoria</th>
                            <th>Reclamos</th>
                            <th>Peticion</th>
                            <tr>
                            <td><input type="checkbox" $activardenuncia></td>
                            <td><input type="checkbox" $activarqueja></td>
                            <td><input type="checkbox" $activarsugerencia></td>
                            <td><input type="checkbox" $activarasesoria></td>
                            <td><input type="checkbox" $activarreclamos></td>
                            <td><input type="checkbox" $activarpeticiones></td>
                            </tr>
                        </tr>
                    </table>
                </table>
                <table>
                        <tr>
                            <th>Fecha de Solicitud</th>
                            <th>Hora</th>
                        <th>Nombre y Apellido del Ciudadano Solicitante</th>
                        <th>Nombre del Funcionario Receptor</th>
                    </tr>
                    <tr>
                        <td>$fecha</td>
                        <td>$hora</td>
                        <td>$solicitud->nombre</td>
                        <td>$nombreUsuario</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Funcionario Receptor</th>
                        <th>Sello</th>
                    </tr>
                    <tr>
                        <td style="padding: 1rem"></td>
                        <td></td>
                    </tr>
                </table>
                <h5 style="text-align: center">Usted podra solicitar informacion sobre su solicitud en la Oficina de Atencion Ciudadana a traves de los telefonos (0414 1572028) (0412 5526701)</h5>
                <h5 style="text-align: center">Todos los tramites realizados ante esta oficina son absolutamente gratuitos</h5>
                    </body>
                    </html>
            HTML;
        }

        if ($solicitud["tipo_solicitud_id"] === 4) {
            $observacion = $solicitud->sugerecia;
            $observacion = json_decode($observacion, true);
            $observacionAsesoria = $observacion[0]["observacion"];
            $activarsugerencia = "checked";
            $valor = "Sugerencia";
            $htmlsolicitud = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Planilla</title>
                <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
            <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">
                <table>
                    <tr>
                        <th>Numero de Registro $numeroSolicitud</th>
                        <th>Oficina de Atencion Ciudadana</th>
                        <th>Planilla de Solicitud</th>
                        <th>Dia: $dia</th>
                        <th>Mes: $mes</th>
                        <th>Año: $anno</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Denuncia</th>
                        <th>Queja</th>
                        <th>Sugerencia</th>
                        <th>Asesoria</th>
                        <th>Reclamos</th>
                        <th>Peticion</th>
                        <tr>
                        <td><input type="checkbox" $activardenuncia></td>
                        <td><input type="checkbox" $activarqueja></td>
                        <td><input type="checkbox" $activarsugerencia></td>
                        <td><input type="checkbox" $activarasesoria></td>
                        <td><input type="checkbox" $activarreclamos></td>
                        <td><input type="checkbox" $activarpeticiones></td>
                        </tr>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Datos del ciudadano(a) Solicitante</th>
                    </tr>
                </table>
                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="text-primary" >Nombre Y Apellido</th>
                        <th>Cedula</th>
                        <th>Telefono</th>
                        <th>Telefono Casa</th>
                        <th>Correo Electronico</th>
                        <th>Sexo</th>
                        <th>Estado Civil</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Ocupacion O/U Oficio</th>
                    </tr>
                    <tr>
                        <td>$solicitud->nombre</td>
                        <td>$solicitud->cedula</td>
                        <td>$solicitud->telefono</td>
                        <td>$solicitud->telefono2</td>
                        <td>$solicitud->email</td>
                        <td>$solicitud->sexo</td>
                        <td>$solicitud->edocivil</td>
                        <td>$solicitud->fechaNacimiento</td>
                        <td>$solicitud->nivelestudio</td>
                        <td>$solicitud->profesion</td>
                    </tr>
            </table>

            <table>
            <tr >
                        <th class="text-primary" >Estado</th>
                        <th>Municipio</th>
                        <th>Parroquia</th>
                        <th>Comuna</th>
                        <th>Comunidad</th>
                        <th>Direccion Habitacion</th>
                        <th>Tipo de Solicitud</th>
                    </tr>
                    <tr>
                        <td>$estadoSolicitud</td>
                        <td>$municipio</td>
                        <td>$parroquia</td>
                        <td>$comuna</td>
                        <td>$comunidad</td>
                        <td>$solicitud->direccion</td>
                        <td>$valor</td>
                    </tr>
                        </table>
                        <table>
                    <tr>
                        <th>Recaudos de la Peticion</th>
                    </tr>
            </table>

                <table>
                    <tr>
                        <!-- <th>Asignacion</th> -->
                        <th>Direccion Asignada</th>
                        <!-- <th>Coordinacion Asignada</th> -->
                    </tr>
                    <tr>
                        <!-- <td>$solicitud->asignacion</td> -->
                        <td>$direccionAsignada</td>
                        <!-- <td></td> -->
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Sugerencia o Asesoria</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Observacion</th>
                    </tr>
                    <tr>
                        <td>$observacionAsesoria</td>
                    </tr>
                </table>

                    <table>
                        <tr>
                            <th>Documentos que anexa</th>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <th>Carta Exposicion de Motivo</th>
                        </tr>
                        <tr>
                            <td><input type='checkbox' $activarrecaudoMotivo></td>
                        </tr>
                    </table>
                <table>
                    <tr>
                        <th style="padding:1rem;">Declaro que los datos suministrados son fidedignos y estoy en conocimiento que cualquier falta o falsedad, en los mismos involucra sanciones o a la no aceptacion de la solicitud.</th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Solicitante</th>
                        <th>Huella Dactilar</th>
                    </tr>
                    <tr>
                        <td style="padding:2rem;"></td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Solo para ser llenado por la Unidad Receptora</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Prioridad del tramite</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td>Alta<input type='checkbox'></td>
                        <td>Media<input type='checkbox'></td>
                        <td>Baja<input type='checkbox'></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de el tramite</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Nombre del Funcionario Receptor</th>
                        <!-- <th>Cedula de Identidad</th> -->
                        <th>Sello y Firma</th>
                    </tr>
                    <tr>
                        <td style="padding: 15px">$nombreUsuario</td>
                        <!-- <td></td> -->
                        <td></td>
                    </tr>
                </table>
                <p>----------------------------------------------------------------------------------------------------------------------------------------</p>
                <table style="margin-top: 20px">
                    <tr>
                        <th>Oficina de Atencion Ciudadana <span style="text-align: right">Numero de Registro $numeroSolicitud</span></th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>
                            Planilla de Solicitud:
                        </th>
                    </tr>
                </table>

                    <table>
                        <tr>
                            <th>Denuncia</th>
                            <th>Queja</th>
                            <th>Sugerencia</th>
                            <th>Asesoria</th>
                            <th>Reclamos</th>
                            <th>Peticion</th>
                            <tr>
                            <td><input type="checkbox" $activardenuncia></td>
                            <td><input type="checkbox" $activarqueja></td>
                            <td><input type="checkbox" $activarsugerencia></td>
                            <td><input type="checkbox" $activarasesoria></td>
                            <td><input type="checkbox" $activarreclamos></td>
                            <td><input type="checkbox" $activarpeticiones></td>
                            </tr>
                        </tr>
                    </table>
                </table>
                <table>
                        <tr>
                        <th>Fecha de Solicitud</th>
                        <th>Hora</th>
                        <th>Nombre y Apellido del Ciudadano Solicitante</th>
                        <th>Nombre del Funcionario Receptor</th>
                    </tr>
                    <tr>
                        <td>$fecha</td>
                        <td>$hora</td>
                        <td>$solicitud->nombre</td>
                        <td>$nombreUsuario</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Funcionario Receptor</th>
                        <th>Sello</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                        <td></td>
                    </tr>
                </table>
                <h5 style="text-align: center">Usted podra solicitar informacion sobre su solicitud en la Oficina de Atencion Ciudadana a traves de los telefonos (0414 1572028) (0412 5526701)</h5>
                <h5 style="text-align: center">Todos los tramites realizados ante esta oficina son absolutamente gratuitos</h5>
                    </body>
                    </html>
            HTML;
        }

        if ($solicitud["tipo_solicitud_id"] === 5) {
            $observacion = $solicitud->asesoria;
            $observacion = json_decode($observacion, true);
            $observacionAsesoria = $observacion[0]["observacion"];
            $activarasesoria = "checked";
            $valor = "Asesoria";

            $htmlsolicitud = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Planilla</title>
                <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
            <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">
                <table>
                    <tr>
                        <th>Numero de Registro $numeroSolicitud</th>
                        <th>Oficina de Atencion Ciudadana</th>
                        <th>Planilla de Solicitud</th>
                        <th>Dia: $dia</th>
                        <th>Mes: $mes</th>
                        <th>Año: $anno</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Denuncia</th>
                        <th>Queja</th>
                        <th>Sugerencia</th>
                        <th>Asesoria</th>
                        <th>Reclamos</th>
                        <th>Peticion</th>
                        <tr>
                        <td><input type="checkbox" $activardenuncia></td>
                        <td><input type="checkbox" $activarqueja></td>
                        <td><input type="checkbox" $activarsugerencia></td>
                        <td><input type="checkbox" $activarasesoria></td>
                        <td><input type="checkbox" $activarreclamos></td>
                        <td><input type="checkbox" $activarpeticiones></td>
                        </tr>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Datos del ciudadano(a) Solicitante</th>
                    </tr>
                </table>
                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="text-primary" >Nombre Y Apellido</th>
                        <th>Cedula</th>
                        <th>Telefono</th>
                        <th>Telefono Casa</th>
                        <th>Correo Electronico</th>
                        <th>Sexo</th>
                        <th>Estado Civil</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Ocupacion O/U Oficio</th>
                    </tr>
                    <tr>
                        <td>$solicitud->nombre</td>
                        <td>$solicitud->cedula</td>
                        <td>$solicitud->telefono</td>
                        <td>$solicitud->telefono2</td>
                        <td>$solicitud->email</td>
                        <td>$solicitud->sexo</td>
                        <td>$solicitud->edocivil</td>
                        <td>$solicitud->fechaNacimiento</td>
                        <td>$solicitud->nivelestudio</td>
                        <td>$solicitud->profesion</td>
                    </tr>
            </table>

            <table>
            <tr >
                        <th class="text-primary" >Estado</th>
                        <th>Municipio</th>
                        <th>Parroquia</th>
                        <th>Comuna</th>
                        <th>Comunidad</th>
                        <th>Direccion Habitacion</th>
                        <th>Tipo de Solicitud</th>
                    </tr>
                    <tr>
                        <td>$estadoSolicitud</td>
                        <td>$municipio</td>
                        <td>$parroquia</td>
                        <td>$comuna</td>
                        <td>$comunidad</td>
                        <td>$solicitud->direccion</td>
                        <td>$valor</td>
                    </tr>
                        </table>
                        <table>
                    <tr>
                        <th>Recaudos de la Peticion</th>
                    </tr>
            </table>

                <table>
                    <tr>
                        <!-- <th>Asignacion</th> -->
                        <th>Direccion Asignada</th>
                        <!-- <th>Coordinacion Asignada</th> -->
                    </tr>
                    <tr>
                        <!-- <td>$solicitud->asignacion</td> -->
                        <td>$direccionAsignada</td>
                        <!-- <td></td> -->
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Sugerencia o Asesoria</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Observacion</th>
                    </tr>
                    <tr>
                        <td>$observacionAsesoria</td>
                    </tr>
                </table>

                    <table>
                        <tr>
                            <th>Documentos que anexa</th>
                        </tr>
                        <tr>
                            <td>
                                <div style="display:flex; justify-content: space-between;">
                                    Carta Exposicion de Motivo
                                    <input type='checkbox' $activarrecaudoMotivo>
                                </div>
                            </td>
                        </tr>
                    </table>
                <table>
                    <tr>
                        <th style="padding:1rem;">Declaro que los datos suministrados son fidedignos y estoy en conocimiento que cualquier falta o falsedad, en los mismos involucra sanciones o a la no aceptacion de la solicitud.</th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Solicitante</th>
                        <th>Huella Dactilar</th>
                    </tr>
                    <tr>
                        <td style="padding:2rem;"></td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Solo para ser llenado por la Unidad Receptora</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Prioridad del tramite</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td>Alta<input type='checkbox'></td>
                        <td>Media<input type='checkbox'></td>
                        <td>Baja<input type='checkbox'></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Descripcion de el tramite</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Nombre del Funcionario Receptor</th>
                        <!-- <th>Cedula de Identidad</th> -->
                        <th>Sello y Firma</th>
                    </tr>
                    <tr>
                        <td style="padding: 15px">$nombreUsuario</td>
                        <!-- <td></td> -->
                        <td></td>
                    </tr>
                </table>
                <p>----------------------------------------------------------------------------------------------------------------------------------------</p>

                <table style="margin-top: 20px">
                    <tr>
                        <th>Oficina de Atencion Ciudadana <span style="text-align: right">Numero de Registro $numeroSolicitud</span></th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>
                            Planilla de Solicitud:
                        </th>
                    </tr>
                </table>

                    <table>
                        <tr>
                            <th>Denuncia</th>
                            <th>Queja</th>
                            <th>Sugerencia</th>
                            <th>Asesoria</th>
                            <th>Reclamos</th>
                            <th>Peticion</th>
                            <tr>
                            <td><input type="checkbox" $activardenuncia></td>
                            <td><input type="checkbox" $activarqueja></td>
                            <td><input type="checkbox" $activarsugerencia></td>
                            <td><input type="checkbox" $activarasesoria></td>
                            <td><input type="checkbox" $activarreclamos></td>
                            <td><input type="checkbox" $activarpeticiones></td>
                            </tr>
                        </tr>
                    </table>
                </table>
                <table>
                        <tr>
                            <th>Fecha de Solicitud</th>
                            <th>Hora</th>
                        <th>Nombre y Apellido del Ciudadano Solicitante</th>
                        <th>Nombre del Funcionario Receptor</th>
                    </tr>
                    <tr>
                        <td>$fecha</td>
                        <td>$hora</td>
                        <td>$solicitud->nombre</td>
                        <td>$nombreUsuario</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Funcionario Receptor</th>
                        <th>Sello</th>
                    </tr>
                    <tr>
                        <td style="padding: 2rem"></td>
                        <td></td>
                    </tr>
                </table>
                <h5 style="text-align: center">Usted podra solicitar informacion sobre su solicitud en la Oficina de Atencion Ciudadana a traves de los telefonos (0414 1572028) (0412 5526701)</h5>
                <h5 style="text-align: center">Todos los tramites realizados ante esta oficina son absolutamente gratuitos</h5>
                    </body>
                    </html>
            HTML;
        }

        if ($solicitud["tipo_solicitud_id"] === 6) {
            $urlActual = $_SERVER['HTTP_HOST'];
            $beneficiario = $solicitud->beneficiario;
            $beneficiario = json_decode($beneficiario, true);
            $cedulabeneficiario = $beneficiario[0]["cedula"];
            $edadbeneficiario = isset($beneficiario[0]["edad"]) ? $beneficiario[0]["edad"]: 'N/A';
            $nombrebeneficiario = $beneficiario[0]["nombre"];
            $direccionbeneficiario = $beneficiario[0]["direccion"];
            $codigovenapp = isset($beneficiario[0]["venApp"]) ? $beneficiario[0]["venApp"]: 'N/A';

            $solicita = isset($beneficiario[0]["solicita"]) ? $beneficiario[0]["solicita"]: 'N/A';
            $recaudos = $solicitud->recaudos;
            $recaudos = json_decode($recaudos, true);

            $cedularecaudos = isset($recaudos[0]["cedula"]) ? $recaudos[0]["cedula"] : "off";
            $motivorecaudos = isset($recaudos[0]["motivo"]) ? $recaudos[0]["motivo"] : "off";
            $reciperecaudos = isset($recaudos[0]["recipe"]) ? $recaudos[0]["recipe"] : "off";
            $informerecaudos = isset($recaudos[0]["informe"]) ? $recaudos[0]["informe"] : "off";
            $beneficiariorecaudos = isset($recaudos[0]["beneficiario"]) ? $recaudos[0]["beneficiario"] : "off";
            $presupuestorecaudos = isset($recaudos[0]["checkpresupuesto"]) ? $recaudos[0]["checkpresupuesto"] : "off";
            $evidenciafotobeneficiario = isset($recaudos[0]["evifotobeneficiario"]) ? $recaudos[0]["evifotobeneficiario"] : "off";
            $permisoinhumacion = isset($recaudos[0]["permisoinhumacion"]) ? $recaudos[0]["permisoinhumacion"] : "off";
            $certificadodefuncion = isset($recaudos[0]["certificadodefuncion"]) ? $recaudos[0]["certificadodefuncion"] : "off";
            $ordenexamen = isset($recaudos[0]["ordenexamen"]) ? $recaudos[0]["ordenexamen"] : "off";
            $ordenestudio = isset($recaudos[0]["ordenestudio"]) ? $recaudos[0]["ordenestudio"] : "off";


            $trabajadorAlcaldia = $solicitud->trabajador;
            $verificaTrabajador = isset($trabajadorAlcaldia) ? $trabajadorAlcaldia : 'NO';

            $solicitud_salud_id = isset($solicitud->solicitud_salud_id) ? $solicitud->solicitud_salud_id : $solicitud->solicitud_atc_id;
            $subtiposolicitud = (new subtiposolicitud)->getSubtiposolicitudbyID($solicitud->tipo_subsolicitud_id);

            $tiposolicitud = $subtiposolicitud->nombre;
            /* A ESTA SOLICITUD NO SE LE AGREGO ESTADO POR ENDE DA ERROR SI NO SE LE ADJUNTA ESE VALOR */
            if($solicitud_salud_id == 3928){
                $estadoSolicitud = 'N/A';
            }

            if($cedularecaudos === "on"){
                $activarrecaudoCedula = "checked";
            }
            if($motivorecaudos === "on"){
                $activarrecaudoMotivo = "checked";
            }
            if($reciperecaudos === "on"){
                $activarrecaudoRecipe = "checked";
            }
            if($informerecaudos === "on"){
                $activarrecaudoInforme = "checked";
            }
            if($beneficiariorecaudos === "on"){
                $activarrecaudoBeneficiario = "checked";
            }
            if($presupuestorecaudos === "on"){
                $activarrecaudoPresupuesto = "checked";
            }
            if($evidenciafotobeneficiario === "on"){
                $activarevidenciafotobeneficiario = "checked";
            }
            if($permisoinhumacion === "on"){
                $activarpermisoinhumacion = "checked";
            }
            if($certificadodefuncion === "on"){
                $activarcertificadodefuncion = "checked";
            }
            if($ordenexamen === "on"){
                $activarordenExamen = "checked";
            }
            if($ordenestudio === "on"){
                $activarordenEstudio = "checked";
            }
            $activarpeticiones = "checked";
            $valor = "SALUD";

            $htmlsolicitud = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Solicitud Direccion Politicas Sociales</title>
                <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
            <img src="https://i.imgur.com/AoVIaLt.jpeg" alt="" srcset="" width="100%">
                <table>
                    <tr>
                        <th>Numero de Registro $solicitud_salud_id</th>
                        <th>Oficina de Atencion Ciudadana</th>
                        <th>Planilla de Solicitud</th>
                        <th>Dia: $dia</th>
                        <th>Mes: $mes</th>
                        <th>Año: $anno</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Datos del ciudadano(a) Solicitante</th>
                    </tr>
                </table>
                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="text-primary" >Nombre Y Apellido</th>
                        <th>Cedula</th>
                        <th>Telefono</th>
                        <th>Sexo</th>
                        <th>Edad</th>
                        <!-- <th>Ocupacion O/U Oficio</th> -->
                    </tr>
                    <tr>
                        <td>$solicitud->nombre</td>
                        <td>$solicitud->cedula</td>
                        <td>$solicitud->telefono</td>
                        <td>$solicitud->sexo</td>
                        <td>$solicitud->fechaNacimiento</td>
                        <!-- <td>$solicitud->profesion</td>  -->
                    </tr>
            </table>

            <table>
            <tr >
                        <th class="text-primary" >Estado</th>
                        <th>Municipio</th>
                        <th>Parroquia</th>
                        <th>Comuna</th>
                        <th>Comunidad</th>
                        <th>Direccion Habitacion</th>
                        <th>Tipo de Solicitud</th>
                    </tr>
                    <tr>
                        <td>$estadoSolicitud</td>
                        <td>$municipio</td>
                        <td>$parroquia</td>
                        <td>$comuna</td>
                        <td>$comunidad</td>
                        <td>$solicitud->direccion</td>
                        <td>$tiposolicitud</td>
                    </tr>
                        </table>
                        <table>
                    <tr>
                    </tr>
            </table>
            <table>
                    <tr>
                        <th>Nombre Jefe Comunidad</th>
                        <th>Telefono Jefe de Comunidad</th>
                        <th>Nombre de UBCH</th>
                        <th>Nombre de Jefe de UBCH</th>
                        <th>Telefono de Jefe de UBCH</th>
                        <tr>
                        <td>$nombreJefeCom</td>
                        <td>$telJefeCom</td>
                        <td>$nombreUbch</td>
                        <td>$nombreJefUbch</td>
                        <td>$telefonoJefeUbch</td>
                        </tr>
                    </tr>
                </table>

                <table>
                    <tr>
                        <!-- <th>Asignacion</th> -->
                        <th>Direccion Asignada</th>
                        <!-- <th>Coordinacion Asignada</th> -->
                    </tr>
                    <tr>
                        <!-- <td>$solicitud->asignacion</td> -->
                        <td>$direccionAsignada</td>
                        <!-- <td></td> -->
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Datos del Ciudadano Beneficiario(a)/Comunidad</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Cedula de Identidad</th>
                        <th>Edad</th>
                        <!-- <th>Tipo de Registro</th> -->
                        <th>Apellido y Nombre</th>
                        <th>Trabajador de la alcaldia</th>
                        <th>Direccion de Benificiario</th>
                        <th>Solicita</th>
                        <th>Codigo VenApp</th>
                        <!-- <th>Estado Civil</th>
                        <th>Fecha de nacimiento</th>
                        <th>Nivel Educativo</th>
                        <th>Profesion</th>
                        <th>Parentesco</th> -->
                    </tr>
                    <tr>
                        <td>$cedulabeneficiario</td>
                        <td>$edadbeneficiario</td>
                        <!-- <td></td> -->
                        <td>$nombrebeneficiario</td>
                        <td>$verificaTrabajador</td>
                        <td>$direccionbeneficiario</td>
                        <td>$solicita</td>
                        <td>$codigovenapp</td>
                        <!-- <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> -->
                    </tr>
                </table>

                    <table>
                        <tr><th>
                            Documentos que anexa
                        </th></tr>
                    </table>

                    <table>
                    <tr>
                        <th>Copia de Cedula</th>
                        <th>Carta Exposicion de Motivo</th>
                        <th>Recipe Medico</th>
                        <th>Orden Examen Medico</th>
                        <th>Orden Estudio Medico</th>
                        <th>Informe Medico</th>
                        <th>Copia de Cedula de Beneficiario</th>
                        <th>Presupuesto (BS)</th>
                        <th>Evidencia Fotografica</th>
                        <th>Permiso de Inhumacion</th>
                        <th>Certificado de Defuncion</th>
                    </tr>
                    <tr>
                        <td><input type='checkbox' $activarrecaudoCedula></td>
                        <td><input type='checkbox' $activarrecaudoMotivo></td>
                        <td><input type='checkbox' $activarrecaudoRecipe></td>
                        <td><input type="checkbox" $activarordenExamen></td>
                        <td><input type="checkbox" $activarordenEstudio></td>
                        <td><input type='checkbox' $activarrecaudoInforme></td>
                        <td><input type='checkbox' $activarrecaudoBeneficiario></td>
                        <td><input type='checkbox' $activarrecaudoPresupuesto></td>
                        <td><input type='checkbox' $activarevidenciafotobeneficiario></td>
                        <td><input type='checkbox' $activarpermisoinhumacion></td>
                        <td><input type="checkbox" $activarcertificadodefuncion></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th style="padding:1rem;">Declaro que los datos suministrados son fidedignos y estoy en conocimiento que cualquier falta o falsedad, en los mismos involucra sanciones o a la no aceptacion de la solicitud.</th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Solicitante</th>
                        <th>Huella Dactilar</th>
                    </tr>
                    <tr>
                        <td style="padding:1.7rem;"></td>
                        <td></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Solo para ser llenado por la Unidad Receptora</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th>Descripcion de el tramite</th>
                    </tr>
                    <tr>
                        <td style="padding: 1.7rem"></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Nombre del Funcionario Receptor</th>
                        <!-- <th>Cedula de Identidad</th> -->
                        <th>Sello y Firma</th>
                    </tr>
                    <tr>
                        <td style="padding: 15px">$nombreUsuario</td>
                        <!-- <td></td> -->
                        <td></td>
                    </tr>
                </table>
                <p>----------------------------------------------------------------------------------------------------------------------------------------</p>
                <table style="margin-top: 20px">
                    <tr>
                        <th>Oficina de Politicas Sociales <span style="text-align: right">Numero de Registro $solicitud_salud_id</span></th>
                    </tr>
                </table>




                </table>
                <table>
                        <tr>
                            <th>Fecha de Solicitud</th>
                            <th>Hora</th>
                        <th>Nombre y Apellido del Ciudadano Solicitante</th>
                        <th>Nombre y Apellido del Ciudadano Beneficiado</th>
                        <th>Solicita</th>
                        <th>Nombre del Funcionario Receptor</th>
                    </tr>
                    <tr>
                        <td>$fecha</td>
                        <td>$hora</td>
                        <td>$solicitud->nombre</td>
                        <td>$nombrebeneficiario</td>
                        <td>$solicita</td>
                        <td>$nombreUsuario</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th>Firma del Funcionario Receptor</th>
                        <th>Sello</th>
                    </tr>
                    <tr>
                        <td style="padding: 1.7rem"></td>
                        <td></td>
                    </tr>
                </table>
                <h5 style="text-align: center">Usted podra solicitar informacion sobre su solicitud en la Direccion Politicas Sociales y Poder Popular a traves del Correo Electronico politicassocialesycomunitarias@alcaldiapaez.gob.ve</h5>
                <h5 style="text-align: center">Todos los tramites realizados ante esta oficina son absolutamente gratuitos</h5>
                    </body>
                    </html>
            HTML;
        }

        $html = $htmlsolicitud;
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('legal', 'portrait');
        $dompdf->render();
        $dompdf->stream("Solicitud Numero $numeroSolicitud Direccion Politicas Sociales.pdf", array("Attachment" => 1));
        return redirect()->back();
    }
    public function imprimir2(Request $request) {
        $input = $request->all();
        $fechadesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';
        $sexo = isset($input['sexo']) ? $input['sexo'] : '';
        $fechahasta = $input['fecha_hasta'];
        $diadesde = date('d', strtotime($fechadesde));
        $mesdesde = date('m', strtotime($fechadesde));
        $anodesde = date('Y', strtotime($fechadesde));
        $diahasta = date('d', strtotime($fechahasta));
        $meshasta = date('m', strtotime($fechahasta));
        $anohasta = date('Y', strtotime($fechahasta));
        $data = (new Seguimiento)->getSolicitudList_Finalizadas($fechadesde, $fechahasta,$comuna, $direcciones, $sexo);
        $solicitudestotales = count($data);
        $participantesTotal = "";

        foreach ($data as $participante) {
            $participantes =<<<HTML
                    <tr>
                        <td>$participante->atcID</td>
                        <td>$participante->usuario</td>
                        <td>$participante->solicitante</td>
                        <td>$participante->cedula</td>
                    </tr>
            HTML;
            $participantesTotal .= $participantes;
        }

        $html =
        <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <style>
                    body {
                        font-family: sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        text-align:center;
                        border: 1px solid #ddd;
                    }

                    th {
                        font-size: 12px;
                        background-color: #f0f0f0;
                    }
                    td{
                        font-size: 12px;
                    }
            </style>
        </head>
        <body>
        <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">

        <h3 style="text-align:left;">Ofinica de Atencion al Ciudadano</h3>
        <h4 style="text-align:left;">Total de solicitudes finalizadas en el periodo seleccionado: $solicitudestotales</h4>
        <h5 style="text-align:center;">Reporte de solicitudes finalizadas desde el $diadesde-$mesdesde-$anodesde hasta el $diahasta-$meshasta-$anohasta</h5>
        <div>

        <table>
            <tr>
                <th>Correlativo</th>
                <th>Funcionario Receptor</th>
                <th>Solicitante</th>
                <th>Cedula Solicitante</th>
            </tr>
            $participantesTotal
        </table>
        </div>

        </body>
        </html>
        HTML;
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('legal', 'portrait');
        $dompdf->render();
        $dompdf->stream("Reporte total de solicitudes finalizadas durante el periodo $diadesde-$mesdesde-$anodesde al $diahasta-$meshasta-$anohasta.pdf", array("Attachment"=>1));

        return redirect()->back();
    }

public function imprimir3(Request $request) {
    $input = $request->all();
    $fechadesde = $input['fecha_desde'];
    $fechahasta = $input['fecha_hasta'];
    $comuna = $input['comuna'];
    $direcciones = $input['direcciones'];
    $sexo = $input['sexo'];
    $diadesde = date('d', strtotime($fechadesde));
    $mesdesde = date('m', strtotime($fechadesde));
    $anodesde = date('Y', strtotime($fechadesde));
    $diahasta = date('d', strtotime($fechahasta));
    $meshasta = date('m', strtotime($fechahasta));
    $anohasta = date('Y', strtotime($fechahasta));

    /* Obtiene las solicitudes excepto las que estan en estado finalizadas */
    $solregistradas = (new Seguimiento)->getSolicitudList_Finalizadas2($fechadesde, $fechahasta,$comuna, $direcciones, $sexo);
    $totalregistradas = count($solregistradas);
    /* Obtiene las solicitudes que estan en estado finalizadas */
    $solfinalizadas = (new Seguimiento)->getSolicitudList_Finalizadas($fechadesde, $fechahasta,$comuna, $direcciones, $sexo);
    $totalfinalizadas = count($solfinalizadas);

    $solicitudestotales = count($solregistradas) + count($solfinalizadas);

    $printSolicitudRegistradas = "";
    $printSolicitudFinalizadas = "";

    foreach ($solregistradas as $participante) {
        $participantes =<<<HTML
                <tr>
                    <td>$participante->atcID</td>
                    <td>$participante->usuario</td>
                    <td>$participante->solicitante</td>
                    <td>$participante->cedula</td>
                </tr>
        HTML;
        $printSolicitudRegistradas .= $participantes;
    }

    foreach ($solfinalizadas as $finalizada) {
        $finalizadas =<<<HTML
                <tr>
                    <td>$finalizada->atcID</td>
                    <td>$finalizada->usuario</td>
                    <td>$finalizada->solicitante</td>
                    <td>$finalizada->cedula</td>
                </tr>
        HTML;
        $printSolicitudFinalizadas .= $finalizadas;
    }

    $html =
    <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
                body {
                    font-family: sans-serif;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th, td {
                    text-align:center;
                    border: 1px solid #ddd;
                }

                th {
                    font-size: 12px;
                    background-color: #f0f0f0;
                }
                td{
                    font-size: 12px;
                }
        </style>
    </head>
    <body>
    <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">

    <h3 style="text-align:left;">Oficina de Atencion al Ciudadano</h3>
    <h5 style="text-align:left;">Total de solicitudes Registradas y en Analisis en el periodo seleccionado: $totalregistradas</h5>
    <h5 style="text-align:left;">Total de solicitudes Finalizadas durante el periodo seleccionado: $totalfinalizadas</h5>
    <h5 style="text-align:left;">Total de solicitudes Totales en el periodo seleccionado: $solicitudestotales</h5>
    <h5 style="text-align:center;">Listado de solicitudes Totales desde el $diadesde-$mesdesde-$anodesde hasta el $diahasta-$meshasta-$anohasta</h5>
    <div>
        <h5 style="text-align:center;">Solicitudes Finalizadas</h5>
    <table>
        <tr>
            <th>Correlativo</th>
            <th>Funcionario Receptor</th>
            <th>Solicitante</th>
            <th>Cedula Solicitante</th>
        </tr>
        $printSolicitudFinalizadas
    </table>
    <h5 style="text-align:center;">Solicitudes Registradas y en Analisis</h5>
    <table>
        <tr>
            <th>Correlativo</th>
            <th>Funcionario Receptor</th>
            <th>Solicitante</th>
            <th>Cedula Solicitante</th>
        </tr>
        $printSolicitudRegistradas
    </table>
    </div>
    </body>
    </html>
    HTML;
    $options = new Options;
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('legal', 'portrait');
    $dompdf->render();
    $dompdf->stream("Reporte total de solicitudes finalizadas durante el periodo $diadesde-$mesdesde-$anodesde al $diahasta-$meshasta-$anohasta.pdf", array("Attachment"=>1));

    return redirect()->back();
}

public function imprimir4() {

    $solfinalizadas = (new Solicitud)->reportetotalcasosatendidosATC();
    $html =
    <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
                body {
                    font-family: sans-serif;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th, td {
                    text-align:left;
                    border: 0px;
                }

                th {
                    font-size: 12px;
                    background-color: #f0f0f0;
                }
                td{
                    font-size: 12px;
                }
        </style>
    </head>
    <body>
    <img src="https://prensa.alcaldiapaez.gob.ve/wp-content/uploads/sites/2/2024/06/cintillo.png" alt="" srcset="" width="100%">

    <h3 style="text-align:left;">Oficina de Atencion al Ciudadano</h3>
    <h5 style="text-align:center;">Listado de solicitudes Totales Terminadas</h5>
    <div>
        <h5 style="text-align:center;">Solicitudes Finalizadas</h5>
    <table>
    <tr>
        <td>SOLICITUDES TOTALES</td>
        <td>$solfinalizadas->TOTAL_SOLICITUD</td>
    </tr>
    <tr>
        <td>MASCULINO</td>
        <td>$solfinalizadas->MASCULINO</td>
    </tr>
    <tr>
        <td>MASCULINO MAYOR</td>
        <td>$solfinalizadas->MASCULINO_MAYOR</td>
    </tr>
    <tr>
        <td>ADOLESCENTE MASCULINO</td>
        <td>$solfinalizadas->ADOLESCENTE_MASCULINO</td>
    </tr>
    <tr>
        <td>FEMENINO</td>
        <td>$solfinalizadas->FEMENINO</td>
    </tr>
    <tr>
        <td>FEMENINO MAYOR</td>
        <td>$solfinalizadas->FEMENINO_MAYOR</td>
    </tr>
    <tr>
        <td>ADOLESCENTE FEMENINO</td>
        <td>$solfinalizadas->ADOLESCENTE_FEMENINO</td>
    </tr>
    </table>
    </div>
    </body>
    </html>
    HTML;
    $options = new Options;
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('latter', 'portrait');
    $dompdf->render();
    $dompdf->stream("Reporte total de solicitudes finalizadas.pdf", array("Attachment"=>1));

    return redirect()->back();
}
}
