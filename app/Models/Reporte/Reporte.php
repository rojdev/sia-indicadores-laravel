<?php

namespace App\Models\Reporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Reporte extends Model
{
    use HasFactory;
    protected $table = 'acciong_lines';

    protected $fillable = [
        'accion_id',
        'unidad_id',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'comuna_id',
        'comunidad_id',
        'cantidad',
        'avance_porc',
        'observacion',
        'state',
        'write_date',
        'nombre',
        'name'

    ];
    public function getAccionesLinesbackup($fechaDesde = null, $fechaHasta = null, $direcciones = null, $comuna = null, $comunidad = null)
    {
        try {
            if($fechaDesde == null){
                $fechaDesde = '';
            }
            if($fechaHasta == null){
                $fechaHasta = '';
            }
            if($direcciones == null){
                $direcciones = '';
            }
            if($comuna == null){
                $comuna = '';
            }
            if($comunidad == null){
                $comunidad = '';
            }
            $accionesLines = DB::table('acciong_lines AS a')
                ->select(
                    'a.id',
                    'a.accion_id',
                    'b.nombre AS Direccion',
                    'a.nombre AS accion',
                    'a.cantidad',
                    'a.avance_porc',
                    'a.observacion',
                    'a.state',
                    'c.nombre AS Estado',
                    'd.nombre AS Municipio',
                    'e.nombre AS Parroquia',
                    'f.codigo AS Comuna',
                    'g.nombre AS Comunidad',
                    'a.name',
                    'a.write_date'
                )
                ->join('sala_unidad AS b', 'a.unidad_id', '=', 'b.id')
                ->join('estado AS c', 'a.estado_id', '=', 'c.id')
                ->join('municipio AS d', 'a.municipio_id', '=', 'd.id')
                ->join('parroquia AS e', 'a.parroquia_id', '=', 'e.id')
                ->join('comuna AS f', 'a.comuna_id', '=', 'f.id')
                ->join('comunidad AS g', 'a.comunidad_id', '=', 'g.id')
                ->where('a.clasificacion', 1)
                ->where(function ($query) use ($fechaDesde, $fechaHasta, $direcciones, $comuna, $comunidad) {
                    if (!empty($fechaDesde)) {
                        $query->where('a.write_date', '>=', $fechaDesde);
                    }
                    if (!empty($fechaHasta)) {
                        $query->where('a.write_date', '<=', $fechaHasta);
                    }
                    if (!empty($direcciones)) {
                        $query->where('a.unidad_id', '=', $direcciones);
                    }
                    if (!empty($comuna)) {
                        $query->where('a.comuna_id', '=', $comuna);
                    }
                    if (!empty($comunidad)) {
                        $query->where('a.comunidad_id', '=', $comunidad);
                    }
                })
                ->distinct()
                ->get();

            return $accionesLines;
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getAccionesLines($fechaDesde = null, $fechaHasta = null,$comuna = null,$comunidad = null, $direcciones = null)
    {
        try {
            $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
            $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
            $comuna =isset($comuna) ? $comuna : $comuna = '';
            $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
            $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';
            $query = DB::table('acciong_lines')
                ->select(
                    'acciong_lines.id',
                    'acciong_lines.accion_id',
                    'sala_unidad.nombre as Direccion',
                    'acciong_lines.cantidad',
                    'acciong_lines.avance_porc',
                    'acciong_lines.observacion',
                    'acciong_lines.state',
                    'acciong_lines.nombre as accion',
                    'comuna.codigo as Comuna',
                    'comunidad.nombre as Comunidad',
                    'acciong_lines.write_date'
                )
                ->join('sala_unidad', 'acciong_lines.unidad_id', '=', 'sala_unidad.id')
                ->join('comuna', 'acciong_lines.comuna_id', '=', 'comuna.id')
                ->join('comunidad', 'acciong_lines.comunidad_id', '=', 'comunidad.id')
                ->where('acciong_lines.clasificacion', 1)
                ->where(function ($query) use ($fechaDesde, $fechaHasta, $direcciones, $comuna, $comunidad) {
                    if (!empty($fechaDesde)) {
                        $query->where('acciong_lines.write_date', '>=', $fechaDesde);
                    }
                    if (!empty($fechaHasta)) {
                        $query->where('acciong_lines.write_date', '<=', $fechaHasta);
                    }
                    if (!empty($direcciones)) {
                        $query->where('acciong_lines.unidad_id', '=', $direcciones);
                    }
                    if (!empty($comuna)) {
                        $query->where('acciong_lines.comuna_id', '=', $comuna);
                    }
                    if (!empty($comunidad)) {
                        $query->where('acciong_lines.comunidad_id', '=', $comunidad);
                    }
                })
                ->distinct()
                ->get();
            return $query;
        } catch (Throwable $e) {
            // Log::error("Error en getAccionesLines: " . $e->getMessage()); // Considera usar el sistema de logging de Laravel
            return [];
        }
    }

    public function getAcciones2024($fechaDesde = null, $fechaHasta = null,$comuna = null,$comunidad = null, $direcciones = null)
    {
        try {

           // var_dump($fechaDesde, $fechaHasta,$comuna,$comunidad, $direcciones);
          //  exit();
            $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
            $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
            $comuna =isset($comuna) ? $comuna : $comuna = '';
            $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
            $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';

            $query = DB::table('Acciones_new')
                ->select(
                    'Acciones_new.id',
                    'Acciones_new.accion_id',
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
                    'Acciones_new.fechainicial as write_date',
                    'Acciones_new.fechafinal',
                    'Acciones_new.created_at',
                    'Acciones_new.evidencia_path',
                    'Acciones_new.evidencia_path2'

                )
                ->join('sala_unidad', 'Acciones_new.direccion_id', '=', 'sala_unidad.id')
                ->join('estado', 'Acciones_new.estado_id', '=', 'estado.id')
                ->join('municipio', 'Acciones_new.municipio_id', '=', 'municipio.id')
                ->join('parroquia', 'Acciones_new.parroquia_id', '=', 'parroquia.id')
                ->join('comuna', 'Acciones_new.comuna_id', '=', 'comuna.id')
                ->join('comunidad', 'Acciones_new.comunidad_id', '=', 'comunidad.id')
                ->join('jefecomunidad', 'Acciones_new.jefecomunidad_id', '=', 'jefecomunidad.id')
                ->leftJoin('coordinacion_sala', 'Acciones_new.coordinacion_sala_id', '=', 'coordinacion_sala.id')
               ->where('Acciones_new.state', '=','APROBADO')
                ->where(function ($query) use ($fechaDesde, $fechaHasta, $direcciones, $comuna, $comunidad) {
                    if (!empty($fechaDesde)) {
                        $query->where('Acciones_new.fechainicial', '>=', $fechaDesde);
                    }
                    if (!empty($fechaHasta)) {
                        $query->where('Acciones_new.fechafinal', '<=', $fechaHasta);
                    }
                    if (!empty($direcciones)) {
                        $query->where('Acciones_new.direccion_id', '=', $direcciones);
                    }
                    if (!empty($comuna)) {
                        $query->where('Acciones_new.comuna_id', '=', $comuna);
                    }
                    if (!empty($comunidad)) {
                        $query->where('Acciones_new.comunidad_id', '=', $comunidad);
                    }
                })
                ->distinct()
                ->get();
            // var_dump($query);
            // exit();
            return $query;
        } catch (Throwable $e) {
            // Log::error("Error en getAccionesLines: " . $e->getMessage()); // Considera usar el sistema de logging de Laravel
            return [];
        }
    }
    public function getdataactividadesnew($fechaDesde = null, $fechaHasta = null,$comuna = null,$comunidad = null, $direcciones = null)
    {
        try {
            $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
            $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
            $comuna =isset($comuna) ? $comuna : $comuna = '';
            $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
            $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';

            $query = DB::table('Actividades_new')
                ->select(
                    'Actividades_new.id as id',
                    'Actividades_new.actividad_id as actividad',
                    'Actividades_new.descripcion as descripcion',
                    'sala_unidad.nombre as Direccion',
                    'coordinacion_sala.nombre as Coordinacion',
                    'Actividades_new.direccion_id as direccionhab',
                    'Actividades_new.cantidad_semanales',
                    'Actividades_new.fecha as fecha',
                    'Actividades_new.state as state',
                )
                ->join('sala_unidad', 'Actividades_new.direccion_id', '=', 'sala_unidad.id')
                ->join('coordinacion_sala', 'Actividades_new.coordinacion_id', '=', 'coordinacion_sala.id')
                ->where(function ($query) use ($fechaDesde, $fechaHasta, $direcciones, $comuna, $comunidad) {
                    if (!empty($fechaDesde)) {
                        $query->where('Actividades_new.fecha', '>=', $fechaDesde);
                    }
                    if (!empty($fechaHasta)) {
                        $query->where('Actividades_new.fecha', '<=', $fechaHasta);
                    }
                    if (!empty($direcciones)) {
                        $query->where('Actividades_new.direccion_id', '=', $direcciones);
                    }

                })
                ->distinct()
                ->get();

            return $query;
        } catch (Throwable $e) {
            // Log::error("Error en getAccionesLines: " . $e->getMessage()); // Considera usar el sistema de logging de Laravel
            return [];
        }
    }

    public function getActividadesLines($fechaDesde = null, $fechaHasta = null,$comuna = null,$comunidad = null, $direcciones = null)
    {
        try {
            $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
            $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
            $comuna =isset($comuna) ? $comuna : $comuna = '';
            $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
            $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';
            $query = DB::table('acciong_lines')
                ->select(
                    'acciong_lines.id',
                    'acciong_lines.accion_id',
                    'sala_unidad.nombre as Direccion',
                    'acciong_lines.cantidad',
                    'acciong_lines.avance_porc',
                    'acciong_lines.observacion',
                    'acciong_lines.state',
                    'acciong_lines.nombre as accion',
                    'comuna.codigo as Comuna',
                    'comunidad.nombre as Comunidad',
                    'acciong_lines.write_date'
                )
                ->join('sala_unidad', 'acciong_lines.unidad_id', '=', 'sala_unidad.id')
                ->join('comuna', 'acciong_lines.comuna_id', '=', 'comuna.id')
                ->join('comunidad', 'acciong_lines.comunidad_id', '=', 'comunidad.id')
                ->where('acciong_lines.clasificacion', 2)
                ->where(function ($query) use ($fechaDesde, $fechaHasta, $direcciones, $comuna, $comunidad) {
                    if (!empty($fechaDesde)) {
                        $query->where('acciong_lines.write_date', '>=', $fechaDesde);
                    }
                    if (!empty($fechaHasta)) {
                        $query->where('acciong_lines.write_date', '<=', $fechaHasta);
                    }
                    if (!empty($direcciones)) {
                        $query->where('acciong_lines.unidad_id', '=', $direcciones);
                    }
                    if (!empty($comuna)) {
                        $query->where('acciong_lines.comuna_id', '=', $comuna);
                    }
                    if (!empty($comunidad)) {
                        $query->where('acciong_lines.comunidad_id', '=', $comunidad);
                    }
                })
                ->distinct()
                ->get();
            return $query;
        } catch (Throwable $e) {
            // Log::error("Error en getAccionesLines: " . $e->getMessage()); // Considera usar el sistema de logging de Laravel
            return [];
        }
    }
}
