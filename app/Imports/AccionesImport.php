<?php

namespace App\Imports;

use App\Models\Acciones\Acciones;
use App\Models\AccionesAuxiliar\AccionesAuxiliar; // Asegúrate de que el namespace sea correcto
use App\Models\Coordinacion\Coordinacion;
use App\Models\CoordinacionSala\CoordinacionSala;
use App\Models\SalaUnidad\SalaUnidad;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Parroquia\Parroquia;
use App\Models\Comuna\Comuna;
use App\Models\Comunidad\Comunidad;
use App\Models\JefeComunidad\JefeComunidad;
use App\Http\Controllers\Acciones\AccionesController;
use DB;
class AccionesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new AccionesAuxiliar([
            'accion_id' => null,
            'nombre' => $row['nombre'],
            'direccion_id' => $row['direccion_id'],
            'coordinacion_sala_id' => $row['coordinacion_sala_id'],
            'estado_id' => 1,
            'municipio_id' => 1,
            'parroquia_id' => $row['parroquia_id'],
            'comuna_id' => $row['comuna_id'],
            'comunidad_id' => $row['comunidad_id'],
            'vocero' => $row['vocero'],
            'telefono' => $row['telefono'],
            'direccion' => $row['direccion'],
            'state' => $row['state'],
            'fechainicial' => $row['fechainicial'],
            'fechafinal' => $row['fechafinal'],

            // 'evidencia_path' => $row['evidencia_path'], // Asegúrate de tener la columna correcta en el archivo
            // 'evidencia_path2' => $row['evidencia_path2'], // Asegúrate de tener la columna correcta en el archivo
            // 'observacion' => $row['observacion'], // Asegúrate de tener la columna correcta en el archivo
        ]);
    }

    public function uniqueBy()
    {
        return 'accion_id';
    }

    public function updated($anno)
    {

        $accionesAuxiliar = AccionesAuxiliar::all();
     //   $accionesnew = Acciones::all();
        $contador = 1;
        foreach ($accionesAuxiliar as $accion) {
            // Compara y actualiza parroquia_id
            $parroquia = Parroquia::where('nombre', $accion->parroquia_id)->first();
            if ($parroquia) {
                $accion->update(['parroquia_id' => $parroquia->id]);
            }

            // Compara y actualiza comuna_id (usando el campo 'codigo')
            $comuna = Comuna::where('codigo', $accion->comuna_id)->first();
            if ($comuna) {
                $accion->update(['comuna_id' => $comuna->id]);
            }

            // Compara y actualiza comunidad_id
            $comunidad = Comunidad::where('nombre', $accion->comunidad_id)->first();
            if ($comunidad) {
                $accion->update(['comunidad_id' => $comunidad->id]);
            }

            // Compara y actualiza jefecomunidad_id
            $jefeComunidad = JefeComunidad::where('comunidad_id', $comunidad->id)->first();

            if ($jefeComunidad) {
             //   var_dump($jefeComunidad->id);
             //   exit();
                $accion->update(['jefecomunidad_id' => $jefeComunidad->id]);
            }

            $direccion_sala = SalaUnidad::where('nombre', $accion->direccion_id)->first();

            if ($direccion_sala) {
                $accion->update(['direccion_id' => $direccion_sala->id]);
            }
            $coordinacion_sala = CoordinacionSala::where('nombre', $accion->coordinacion_sala_id)->first();
            if ($coordinacion_sala) {
                $accion->update(['coordinacion_sala_id' => $coordinacion_sala->id]);
            }
            $correlativo = (new AccionesController())->ObtenerCorrelativoImport($accion->direccion_id,$accion, $anno);
            $accion->update(['accion_id' => $correlativo]);


        }
        DB::table('AccionesAuxiliar2')->truncate();
    }
}
