<?php

namespace App\Models\Acciones;

use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Actividades\Actividades;
use App\Models\SalaUnidad\SalaUnidad;
use Auth;
class Acciones extends Model
{
    use HasFactory;
    protected $table = 'Acciones_new';

    protected $fillable = [
        'accion_id',
        'nombre',
        'direccion_id',
        'coordinacion_sala_id',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'comuna_id',
        'comunidad_id',
        'jefecomunidad_id',
        'territorio_id',
        'direccion',
        'state',
        'cantidad',
        'avancePorcentual',
        'vocero',
        'telefono',
        'evidencia_path',
        'fechainicial',
        'fechafinal',
        'created_at',
        'updated_at',
        'evidencia_path2',
        'observacion',
    ];

    public function CorrelativoAccionGobierno($id){
        $annoactual = date('Y');
        $inicioanno = $annoactual . '-01-01 00:00:00';
        $ultimoResultado = DB::table('Acciones_new')
                            ->select('Acciones_new.accion_id as atc_id')
                            ->whereNotNull('Acciones_new.accion_id')
                            ->latest('Acciones_new.accion_id')
                            ->where('Acciones_new.direccion_id', $id)
                            ->where('Acciones_new.fechainicial', '>=', $inicioanno)
                            ->orderBy('id', 'desc')
                            ->limit(1)
                            ->first();

        return $ultimoResultado ? $ultimoResultado->atc_id : null;
    }

    public function CorrelativoAccionGobiernoImport($id, $anno) {


            if ($anno =='2024') {
                $fechainicio  = '2024-01-01 00:00:00';
                $fechafinal   = '2024-12-31 23:59:59';
                     }else {
                        $fechainicio  = '2025-01-01 00:00:00';
                        $fechafinal   = '2025-12-31 23:59:59';
                            }

                            $ultimoResultado = DB::table('Acciones_new')
                            ->select('Acciones_new.accion_id as atc_id')
                            ->where('direccion_id', 2)
                            ->where('fechafinal', '>=', $fechainicio )
                            ->where('fechafinal', '<=', $fechafinal)
                            ->orderBy('id', 'desc')
                            ->limit(1)
                            ->first();


        return $ultimoResultado ? $ultimoResultado->atc_id : null;
    }
    public function accionesano(){
        $resultados = DB::table('acciong_lines')
        ->join('sala_unidad', 'acciong_lines.unidad_id', '=', 'sala_unidad.id')
        ->join('comuna', 'acciong_lines.comuna_id', '=', 'comuna.id')
        ->join('comunidad', 'acciong_lines.comunidad_id', '=', 'comunidad.id')
        ->select(
            DB::raw('SUM(CASE WHEN acciong_lines.clasificacion IN (1, 2) THEN 1 ELSE 0 END) AS TOTAL_ACCIONES'),
            DB::raw('COUNT(CASE WHEN acciong_lines.clasificacion = 1 THEN 1 END) AS TOTAL_ACCIONES2'),
            DB::raw('COUNT(CASE WHEN acciong_lines.clasificacion = 2 THEN 1 END) AS TOTAL_ACTIVIDADES')
        )
        ->first();

    return $resultados;
    }
    public function totalescomunidadxcomuna($comuna, $fecha_desde, $fecha_hasta) {

        $fecha_desde = isset($fecha_desde) ? $fecha_desde : '';
        $fecha_hasta = isset($fecha_hasta) ? $fecha_hasta : '';
        $comuna = isset($comuna) ? $comuna : '';

        if (empty($fecha_desde) && empty($fecha_hasta) && empty($comuna)) {
            return []; // Return empty array if no criteria
        }

        $resultados = DB::table('Acciones_new')
            ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
            ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
            ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
            ->where('Acciones_new.state', 'APROBADO')
            ->when(!empty($fecha_desde), function ($query) use ($fecha_desde) {
                $query->where('Acciones_new.fechainicial', '>=', $fecha_desde);
            })
            ->when(!empty($fecha_hasta), function ($query) use ($fecha_hasta) {
                $query->where('Acciones_new.fechainicial', '<=', $fecha_hasta);
            })
            ->when(!empty($comuna), function ($query) use ($comuna) {
                $query->where('Acciones_new.comuna_id', '=', $comuna);
            })
            ->select(
                'comunidad.nombre as comunidad',
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 2 THEN 1 ELSE 0 END) AS politicas'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 3 THEN 1 ELSE 0 END) AS insfraestructura'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 16 THEN 1 ELSE 0 END) AS catastro'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 4 THEN 1 ELSE 0 END) AS servicios'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 27 THEN 1 ELSE 0 END) AS civil'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id IN (2, 3, 16, 4, 27) THEN 1 ELSE 0 END) AS total_comuna')
            )
            ->groupBy('comunidad.nombre')
            ->having('politicas', '>', 0)
            ->orHaving('insfraestructura', '>', 0)
            ->orHaving('catastro', '>', 0)
            ->orHaving('servicios', '>', 0)
            ->orHaving('civil', '>', 0)
            ->orHaving('total_comuna', '>', 0)
            ->orderBy('comunidad.nombre')
            ->get();

        $totalesGenerales = [

            'comunidad' => 'ZTotales Generales',
            'politicas' => $resultados->sum('politicas'),
            'insfraestructura' => $resultados->sum('insfraestructura'),
            'catastro' => $resultados->sum('catastro'),
            'servicios' => $resultados->sum('servicios'),
            'civil' => $resultados->sum('civil'),
            'total_comuna' => $resultados->sum('total_comuna'),
        ];

        $resultadosArray = $resultados->toArray();
        $resultadosArray[] = $totalesGenerales;

        return $resultadosArray;
    }
    public function totalescomunidadxcomuna2($comuna, $fecha_desde, $fecha_hasta) {

        $fecha_desde = isset($fecha_desde) ? $fecha_desde : '';
        $fecha_hasta = isset($fecha_hasta) ? $fecha_hasta : '';
        $comuna = isset($comuna) ? $comuna : '';

        if (empty($fecha_desde) && empty($fecha_hasta) && empty($comuna)) {
            return []; // Retorna un array vacío si no hay criterios
        }

        $resultados = DB::table('Acciones_new')
            ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
            ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
            ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
            ->where('Acciones_new.state', 'APROBADO')
            ->when(!empty($fecha_desde), function ($query) use ($fecha_desde) {
                $query->where('Acciones_new.fechainicial', '>=', $fecha_desde);
            })
            ->when(!empty($fecha_hasta), function ($query) use ($fecha_hasta) {
                $query->where('Acciones_new.fechainicial', '<=', $fecha_hasta);
            })
            ->when(!empty($comuna), function ($query) use ($comuna) {
                $query->where('Acciones_new.comuna_id', '=', $comuna);
            })
            ->select(
                'comunidad.nombre as comunidad',
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 5 THEN 1 ELSE 0 END) AS fundacion'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 7 THEN 1 ELSE 0 END) AS impaez'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 24 THEN 1 ELSE 0 END) AS imdep'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id = 6 THEN 1 ELSE 0 END) AS inmujer'),
                DB::raw('SUM(CASE WHEN Acciones_new.direccion_id IN (5, 7, 24, 6) THEN 1 ELSE 0 END) AS total_comuna')
            )
            ->groupBy('comunidad.nombre')
            ->having('fundacion', '>', 0)
            ->orHaving('impaez', '>', 0)
            ->orHaving('imdep', '>', 0)
            ->orHaving('inmujer', '>', 0)
            ->orHaving('total_comuna', '>', 0)
            ->orderBy('comunidad.nombre')
            ->get();

        $totalesGenerales = [
            'comunidad' => 'ZTotales Generales',
            'fundacion' => $resultados->sum('fundacion'),
            'impaez' => $resultados->sum('impaez'),
            'imdep' => $resultados->sum('imdep'),
            'inmujer' => $resultados->sum('inmujer'),
            'total_comuna' => $resultados->sum('total_comuna'),
        ];

        $resultadosArray = $resultados->toArray();
        $resultadosArray[] = $totalesGenerales;

        return $resultadosArray;
    }

    public function totalestomos($direccion, $fechadesde, $fechahasta) {
        if($fechadesde == null){
            $fechadesde = '';
        }
        if($fechahasta == null){
            $fechahasta = '';
        }
        if($direccion == null){
            $direccion = '';
        }
        $tomo = DB::table('Acciones_new')
            ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
            ->join('estado', 'Acciones_new.estado_id', '=', 'estado.id')
            ->join('municipio', 'Acciones_new.municipio_id', '=', 'municipio.id')
            ->join('parroquia', 'Acciones_new.parroquia_id', '=', 'parroquia.id')
            ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
            ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
            ->join('jefecomunidad', 'Acciones_new.jefecomunidad_id', '=', 'jefecomunidad.id')
            ->leftJoin('coordinacion_sala', 'Acciones_new.coordinacion_sala_id', '=', 'coordinacion_sala.id')
            ->select(
                'Acciones_new.id',
                'Acciones_new.accion_id',
                'Acciones_new.nombre',
                'estado.nombre as Estado',
                'municipio.nombre as Municipio',
                'parroquia.nombre as Parroquia',
                'comuna.codigo as Comuna',
                'comunidad.nombre as Comunidad',
                'jefecomunidad.nombre_jefe_comunidad as NombreJefeComunidad',
                'jefecomunidad.telefono_jefe_comunidad as TelefonoJefeComunidad',
                'jefecomunidad.nombre_jefe_ubch as NombreJefeUBCH',
                'jefecomunidad.telefono_jefe_ubch as TelefonoJefeUBCH',
                'jefecomunidad.nombre_ubch as NombreUBCH',
                'sala_unidad.nombre as Direccion',
                'coordinacion_sala.nombre as Coordinacion',
                'Acciones_new.vocero',
                'Acciones_new.telefono',
                'Acciones_new.direccion as direccionhab',
                'Acciones_new.cantidad',
                'Acciones_new.state',
                'Acciones_new.nombre as accion',
                'Acciones_new.fechainicial',
                'Acciones_new.fechafinal',
                'Acciones_new.created_at',
                'Acciones_new.evidencia_path',
                'Acciones_new.evidencia_path2'

            )
            ->where('Acciones_new.state', 'APROBADO')
            ->where(function ($query) use ($fechadesde, $fechahasta, $direccion) {
                if (!empty($fechadesde)) {
                    $query->where('Acciones_new.fechainicial', '>=', $fechadesde);
                }
                if (!empty($fechahasta)) {
                    $query->where('Acciones_new.fechafinal', '<=', $fechahasta);
                }
                if (!empty($direccion)) {
                    $query->where('Acciones_new.direccion_id', $direccion);
                }
            });
        return $tomo->get();
    }
    public function totales() {
        $resultados = DB::table('Acciones_new')
            ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
            ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
            ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
            ->select(
                'comuna.id as comuna_id',
                'comuna.codigo as comuna',
                'comunidad.id as comunidad_id',
                'comunidad.nombre as comunidad',
                'sala_unidad.id as direccion_id',
                'sala_unidad.nombre as direccion',
                 DB::raw('COUNT(*) as total_por_comunidad') //Contador para cada grupo
            )
            ->groupBy('comuna.id', 'comunidad.id', 'sala_unidad.id') // Agrupa por comuna, comunidad, y sala_unidad
            ->orderBy('comuna.id')
            ->get(); // Obtiene los resultados agrupados

        // Estructura de datos para el JSON final
        $resultados_json = [];

        foreach ($resultados as $resultado) {
            //Obtener los detalles de las acciones *para cada grupo*.
            $detalles_acciones = DB::table('Acciones_new')
                ->where('comuna_id', $resultado->comuna_id)
                ->where('comunidad_id', $resultado->comunidad_id)
                ->where('direccion_id', $resultado->direccion_id)
                ->select('id as accion_id', 'nombre as accion_nombre') // Selecciona id y nombre de la accion
                ->get();


            $resultados_json[] = [
                'comuna_id' => $resultado->comuna_id,
                'comuna' => $resultado->comuna,
                'comunidad_id' => $resultado->comunidad_id,
                'comunidad' => $resultado->comunidad,
                'direccion_id' => $resultado->direccion_id,
                'direccion' => $resultado->direccion,
                'total_por_comunidad' => $resultado->total_por_comunidad,
                'acciones' => $detalles_acciones  // Agrega el array de acciones
            ];
        }

        return response()->json($resultados_json); //Devuelve la respuesta en formato JSON.
    }

    public function totalesFiltrados($fechaDesde, $fechaHasta, $comunaId, $comunidadId, $direccionId)
    {
        $query = DB::table('Acciones_new')
            ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
            ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
            ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
            ->select(
                'comuna.id as comuna_id',
                'comuna.codigo as comuna',
                'comunidad.id as comunidad_id',
                'comunidad.nombre as comunidad',
                'sala_unidad.id as direccion_id',
                'sala_unidad.nombre as direccion',
                DB::raw('COUNT(*) as total_por_comunidad')
            )
            ->groupBy('comuna.id', 'comunidad.id', 'sala_unidad.id')
            ->orderBy('comuna.id');

        // Aplicar filtros condicionalmente.  Esto es *clave* para la eficiencia.
        if ($fechaDesde) {
            $query->whereDate('Acciones_new.created_at', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('Acciones_new.created_at', '<=', $fechaHasta);
        }
        if ($comunaId) {
            $query->where('Acciones_new.comuna_id', $comunaId); // Filtra por comuna
        }
        if ($comunidadId) {
            $query->where('Acciones_new.comunidad_id', $comunidadId); // Filtra por comunidad
        }
        if ($direccionId) {
            $query->where('Acciones_new.direccion_id', $direccionId); //Filtra por direccion
        }

        $resultados = $query->get();

         // Estructura de datos para el JSON final
         $resultados_json = [];

         foreach ($resultados as $resultado) {
             //Obtener los detalles de las acciones *para cada grupo*.
             $detalles_acciones = DB::table('Acciones_new')
                 ->where('comuna_id', $resultado->comuna_id)
                 ->where('comunidad_id', $resultado->comunidad_id)
                 ->where('direccion_id', $resultado->direccion_id)
                 ->select('id as accion_id', 'nombre as accion_nombre') // Selecciona id y nombre de la accion
                 ->get();

             $resultados_json[] = [
                 'comuna_id' => $resultado->comuna_id,
                 'comuna' => $resultado->comuna,
                 'comunidad_id' => $resultado->comunidad_id,
                 'comunidad' => $resultado->comunidad,
                 'direccion_id' => $resultado->direccion_id,
                 'direccion' => $resultado->direccion,
                 'total_por_comunidad' => $resultado->total_por_comunidad,
                 'acciones' => $detalles_acciones  // Agrega el array de acciones
             ];
         }

         return response()->json($resultados_json); //Devuelve la respuesta en formato JSON.
    }

    public function accionesano2(){
        $resultados = DB::table('acciong_lines')
            ->join('sala_unidad', 'acciong_lines.unidad_id', '=', 'sala_unidad.id')
            ->join('comuna', 'acciong_lines.comuna_id', '=', 'comuna.id')
            ->join('comunidad', 'acciong_lines.comunidad_id', '=', 'comunidad.id')
            ->select(
                DB::raw('YEAR(acciong_lines.write_date) as year'),
                DB::raw('COUNT(CASE WHEN acciong_lines.clasificacion = 1 THEN 1 END) AS TOTAL_ACCIONES2'),
                DB::raw('COUNT(CASE WHEN acciong_lines.clasificacion = 2 THEN 1 END) AS TOTAL_ACTIVIDADES')
            )
            ->whereIn(DB::raw('YEAR(acciong_lines.write_date)'), [2022, 2023])
            ->groupBy('year')
            ->get();

        $formattedResults = [];
        foreach ($resultados as $resultado) {
            $formattedResults[] = $resultado->year . " TOTAL_ACCIONES2 " . $resultado->TOTAL_ACCIONES2 . " TOTAL_ACTIVIDADES " . $resultado->TOTAL_ACTIVIDADES;
        }

        return $formattedResults;
    }
    public function accionesano3()
    {
        $resultados = DB::select(DB::raw("
            SELECT anio, COALESCE(total_acciones_registradas, 0) as total_acciones_registradas
            FROM (
                SELECT 2024 as anio UNION ALL
                SELECT 2025
            ) as anios_buscados
            LEFT JOIN (
                SELECT
                    YEAR(Acciones_new.fechafinal) as anio_accion,
                    SUM(CASE WHEN Acciones_new.state = 'APROBADO' THEN 1 ELSE 0 END) AS total_acciones_registradas
                FROM Acciones_new
                INNER JOIN sala_unidad ON Acciones_new.direccion_id = sala_unidad.id
                WHERE YEAR(Acciones_new.fechafinal) IN (2024, 2025)
                GROUP BY anio_accion
            ) as resultados_acciones ON anios_buscados.anio = resultados_acciones.anio_accion
            ORDER BY anios_buscados.anio ASC
        "));

        $formattedResults = [];
        foreach ($resultados as $resultado) {
            $formattedResults[$resultado->anio] = $resultado->total_acciones_registradas;
        }

        return $formattedResults;
    }

    public function totalaccionesyactividades()
    {
        $actividades = (new Actividades)->actividadesano3();
        $acciones = (new Acciones)->accionesano3();

        $data = [];
        foreach (array_keys($actividades) as $anio) {
            $data[$anio] = [
                'Actividades' => (int)$actividades[$anio],
                'Acciones' => (int)$acciones[$anio],
            ];
        }

        return response()->json($data); // Devolver directamente la respuesta JSON
    }


    public function getAccionesNew($fechaDesde = null, $fechaHasta = null,$comuna = null,$comunidad = null, $direcciones = null)
    {
        try {
           // var_dump(Auth::user()->rols_id);
           // exit();
           $rols_id = Auth::user()->rols_id;
            if (Auth::user()->rols_id === 13 || Auth::user()->rols_id === 14 || Auth::user()->rols_id === 1) {
                $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
                $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
                $comuna =isset($comuna) ? $comuna : $comuna = '';
                $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
                $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';
                $query = DB::table('Acciones_new')
                    ->select(
                        'Acciones_new.id',
                        'Acciones_new.accion_id as accion_id',
                        'Acciones_new.nombre as accion',
                        'comuna.codigo as Comuna',
                        'comunidad.nombre as Comunidad',
                        'sala_unidad.nombre as Direccion',
                        'Acciones_new.cantidad',
                        'Acciones_new.state',
                        'Acciones_new.fechafinal as write_date'
                    )
                    ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
                    ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
                    ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
                    ->where('Acciones_new.state','!=' ,'ELIMINADO')
                    ->where(function ($query) use ($fechaDesde, $fechaHasta, $comuna, $comunidad) {
                        if (!empty($fechaDesde)) {
                            $query->where('Acciones_new.fecha fechainicial', '>=', $fechaDesde);
                        }
                        if (!empty($fechaHasta)) {
                            $query->where('Acciones_new.fechafinal', '<=', $fechaHasta);
                        }

                        if (!empty($comuna)) {
                            $query->where('Acciones_new.comuna_id', '=', $comuna);
                        }
                        if (!empty($comunidad)) {
                            $query->where('Acciones_new.comunidad_id', '=', $comunidad);
                        }
                    })
                    ->distinct()
                    ->orderBy('id','DESC')
                    ->get();
            }else{
            $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
            $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
            $comuna =isset($comuna) ? $comuna : $comuna = '';
            $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
            $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';
            $direccion_user = Auth::user()->sala_unidad_id;
            $query = DB::table('Acciones_new')
                ->select(
                    'Acciones_new.id',
                    'Acciones_new.accion_id as accion_id',
                    'Acciones_new.nombre as accion',
                    'comuna.codigo as Comuna',
                    'comunidad.nombre as Comunidad',
                    'sala_unidad.nombre as Direccion',
                    'Acciones_new.cantidad',
                    'Acciones_new.state',
                    'Acciones_new.fechafinal as write_date'
                )
                ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
                ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
                ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
                ->where('Acciones_new.direccion_id', '=', $direccion_user)
                ->where('Acciones_new.state','!=' ,'ELIMINADO')
                ->orderBy('id','DESC')
                ->get();
            }
            return $query;
        } catch (Throwable $e) {
            // Log::error("Error en getAccionesLines: " . $e->getMessage()); // Considera usar el sistema de logging de Laravel
            return [];
        }
    }


}
