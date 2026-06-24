<?php

namespace App\Models\Actividades;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class Actividades extends Model
{
    use HasFactory;
    protected $table = 'Actividades_new';

    protected $fillable = [
        'actividad_id',
        'direccion_id',
        'coordinacion_id',
        'descripcion',
        'fecha',
        'cantidad_semanales',
        'created_at',
        'updated_at',
        'state',
        'observacion',
    ];

    public function CorrelativoActividadesGobierno(){
        $ultimoResultado = DB::table('Actividades_new')
                            ->select('Actividades_new.actividad_id as atc_id')
                            ->whereNotNull('Actividades_new.actividad_id')
                            ->latest('Actividades_new.actividad_id')
                            ->first();

        return $ultimoResultado ? $ultimoResultado->atc_id : null;
    }

    public function getActividadesNew($fechaDesde = null, $fechaHasta = null,$comuna = null,$comunidad = null, $direcciones = null)
    {
        try {
            if (Auth::user()->rols_id === 13 || Auth::user()->rols_id === 14 || Auth::user()->rols_id === 1) {
                $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
                $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
                $comuna =isset($comuna) ? $comuna : $comuna = '';
                $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
                $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';
                $query = DB::table('Actividades_new')
                    ->select(
                        'Actividades_new.id',
                        'Actividades_new.actividad_id as actividad_id',
                        'Actividades_new.descripcion as accion',
                        'sala_unidad.nombre as Direccion',
                        'coordinacion_sala.nombre as Coordinacion_id',
                        'Actividades_new.cantidad_semanales',
                        'Actividades_new.fecha as write_date',
                        'Actividades_new.state as state'
                    )
                    ->join('sala_unidad', 'Actividades_new.direccion_id', '=', 'sala_unidad.id')
                    ->join('coordinacion_sala', 'Actividades_new.coordinacion_id', '=', 'coordinacion_sala.id')
                    ->where(function ($query) use ($fechaDesde, $fechaHasta, $direcciones, $comuna, $comunidad) {
                        if (!empty($fechaDesde)) {
                            $query->where('Actividades_new.fecha fechainicial', '>=', $fechaDesde);
                        }
                        if (!empty($fechaHasta)) {
                            $query->where('Actividades_new.fecha', '<=', $fechaHasta);
                        }
                        if (!empty($direcciones)) {
                            $query->where('Actividades_new.direccion_id', '=', $direcciones);
                        }
                    })
                    ->distinct()
                    ->orderBy('id', 'DESC')
                    ->get();
                return $query;
            }else{
                $fechaDesde =isset($fechaDesde) ? $fechaDesde : $fechaDesde = '';
                $fechaHasta =isset($fechaHasta) ? $fechaHasta : $fechaHasta = '';
                $comuna =isset($comuna) ? $comuna : $comuna = '';
                $comunidad =isset($comunidad) ? $comunidad : $comunidad = '';
                $direcciones =isset($direcciones) ? $direcciones : $direcciones = '';
                $direccion_user = Auth::user()->sala_unidad_id;
                $query = DB::table('Actividades_new')
                    ->select(
                        'Actividades_new.id',
                        'Actividades_new.actividad_id as actividad_id',
                        'Actividades_new.descripcion as accion',
                        'sala_unidad.nombre as Direccion',
                        'coordinacion_sala.nombre as Coordinacion_id',
                        'Actividades_new.cantidad_semanales',
                        'Actividades_new.fecha as write_date',
                        'Actividades_new.state as state'
                    )
                    ->join('sala_unidad', 'Actividades_new.direccion_id', '=', 'sala_unidad.id')
                    ->join('coordinacion_sala', 'Actividades_new.coordinacion_id', '=', 'coordinacion_sala.id')
                    ->where('Actividades_new.direccion_id', '=', $direccion_user)
                    ->distinct()
                    ->orderBy('id', 'DESC')
                    ->get();
                return $query;
            }
        } catch (Throwable $e) {
            // Log::error("Error en getActividadesLines: " . $e->getMessage()); // Considera usar el sistema de logging de Laravel
            return [];
        }
    }
    public function actividadesano3()
    {
        $resultados = DB::select(DB::raw("
            SELECT anio, COALESCE(total_acciones_registradas, 0) as total_acciones_registradas
            FROM (
                SELECT 2024 as anio UNION ALL
                SELECT 2025
            ) as anios_buscados
            LEFT JOIN (
                SELECT
                    YEAR(fecha) as anio_accion,
                    SUM(CASE WHEN state = 'REGISTRADO' THEN 1 ELSE 0 END) AS total_acciones_registradas
                FROM Actividades_new
                WHERE YEAR(fecha) IN (2024, 2025)
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

}
