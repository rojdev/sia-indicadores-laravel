<?php

namespace App\Http\Controllers\Reporte;

use App\Models\Acciones\Acciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User\User;
use App\Models\Security\Rol;
use App\Models\Producto\Producto;
use App\Models\Categoria\Categoria;
use App\Http\Controllers\User\Colores;
use App\Models\Comuna\Comuna;
use App\Models\Estados\Estados;
use App\Models\Municipio\Municipio;
use App\Models\Parroquia\Parroquia;
use App\Models\Comunidad\Comunidad;
use App\Models\Direccion\Direccion;
use App\Models\Coordinacion\Coordinacion;
use App\Models\JefeComunidad\JefeComunidad;
use App\Models\Reporte\Reporte;
use App\Models\SalaUnidad\SalaUnidad;
use Dompdf\Dompdf;
use Dompdf\Options;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AccionesExport;
use App\Exports\ActividadesExport;
use App\Exports\AccionesNewExport;
use App\Exports\ActividadesNewExport;

class ReporteController extends Controller
{
    public function viewReporteAccionesdeGobierno(){
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
        $comuna = (new Comuna)->getComunasFilter();
        $comunidad = [];
        $direcciones = (new SalaUnidad)->getallUnidades();
        $array_color = (new Colores)->getColores();
        return view('Reporte.reporte_acciones',compact('count_notification','tipo_alert','array_color','comuna','comunidad','direcciones'));
    }
    public function viewReporteAccionesdeGobierno2024(){
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
        $comuna = (new Comuna)->getComunasFilter();
        $comunidad = [];
        $direcciones = (new SalaUnidad)->getallUnidades();
        $array_color = (new Colores)->getColores();
        return view('Reporte.reporte_acciones_2024',compact('count_notification','tipo_alert','array_color','comuna','comunidad','direcciones'));
    }
    public function viewReporteActividadesdeGobierno(){
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
        $comuna = (new Comuna)->getComunasFilter();
        $comunidad = [];
        $direcciones = (new SalaUnidad)->getallUnidades();
        $array_color = (new Colores)->getColores();
        return view('Reporte.reporte_actividades',compact('count_notification','tipo_alert','array_color','comuna','comunidad','direcciones'));
    }

    public function viewReporteActividadesdeGobierno2024(){
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
        $comuna = (new Comuna)->getComunasFilter();
        $comunidad = [];
        $direcciones = (new SalaUnidad)->getallUnidades();
        $array_color = (new Colores)->getColores();
        return view('Reporte.reporte_actividades_2024',compact('count_notification','tipo_alert','array_color','comuna','comunidad','direcciones'));
    }
    public function getdataacciones(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Reporte)->getAccionesLines($request->fecha_desde, $request->fecha_hasta,$request->comuna, $request->comunidad, $request->direcciones);

                return datatables()->of($data)

                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
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
    public function getdataaccionesnew(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Reporte)->getAcciones2024($request->fecha_desde, $request->fecha_hasta,$request->comuna, $request->comunidad, $request->direcciones);

                return datatables()->of($data)

                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
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
    public function totalesview(Request $request)
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
        $comuna = (new Comuna)->getComunasFilter();
        $comunidad = [];
        $direcciones = (new SalaUnidad)->getallUnidades();
        $array_color = (new Colores)->getColores();
        return view('Reporte.reporte_acciones_totales',compact('count_notification','tipo_alert','array_color','comuna','comunidad','direcciones'));
    }
    public function totalesview2(Request $request)
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
        $comuna = (new Comuna)->getComunasFilter();
        $comunidad = [];
        $direcciones = (new SalaUnidad)->getallUnidades();
        $array_color = (new Colores)->getColores();
        return view('Reporte.reporte_acciones_totales2',compact('count_notification','tipo_alert','array_color','comuna','comunidad','direcciones'));
    }

    public function tomosview(Request $request)
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
        $direcciones = (new SalaUnidad)->getallUnidades();
        $array_color = (new Colores)->getColores();
        return view('Reporte.reporte_tomo_acciones',compact('count_notification','tipo_alert','array_color','direcciones'));
    }
    public function getdataactividadesnew(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Reporte)->getdataactividadesnew($request->fecha_desde, $request->fecha_hasta,$request->comuna, $request->comunidad, $request->direcciones);

                return datatables()->of($data)

                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
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
    public function getdataactividades(Request $request){
        try {
            if ($request->ajax()) {
                $data = (new Reporte)->getActividadesLines($request->fecha_desde, $request->fecha_hasta,$request->comuna, $request->comunidad, $request->direcciones);

                return datatables()->of($data)

                    ->addColumn('edit', function ($data) {
                        $user = Auth::user();
                        if (($user->id != 1)) {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
                        } else {
                            $edit = '<a href="' . route('seguimiento.edit', $data->id) . '" id="edit_' . $data->id . '" class="btn btn-xs btn-primary" style="background-color: #2962ff;"><b><i class="fa fa-pencil"></i>&nbsp;' . trans('message.botones.go') . '</b></a>';
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

    public function imprimiracciones(Request $request) {
        set_time_limit(1200);
        $input = $request->all();
        $fechaDesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $comunidad = isset($input['comunidad']) ? $input['comunidad'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';
        $fechahasta = $input['fecha_hasta'];
        $diadesde = date('d', strtotime($fechaDesde));
        $mesdesde = date('m', strtotime($fechaDesde));
        $anodesde = date('Y', strtotime($fechaDesde));
        $diahasta = date('d', strtotime($fechahasta));
        $meshasta = date('m', strtotime($fechahasta));
        $anohasta = date('Y', strtotime($fechahasta));
        $data = (new Reporte)->getAccionesLines($fechaDesde, $fechaHasta, $comuna, $comunidad, $direcciones);
        $solicitudestotales = count($data);

        $filasParticipantes = '';
        foreach ($data as $participante) {
            $filasParticipantes .= '<tr>' .
                '<td>' . htmlspecialchars($participante->write_date) . '</td>' .
                '<td>' . htmlspecialchars($participante->Direccion) . '</td>' .
                '<td>' . htmlspecialchars($participante->accion) . '</td>' .
                '<td>' . htmlspecialchars($participante->Comuna) . '</td>' .
                '<td>' . htmlspecialchars($participante->Comunidad) . '</td>' .
                '<td>' . htmlspecialchars($participante->state) . '</td>' .
                '</tr>';
        }

        $html = <<<HTML
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
                    td {
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
                <img src="http://localhost:3000/images/cintillo" alt="" srcset="" width="100%">
                <h3 style="text-align:left;">Sala Situacional de Gestion Municipal</h3>
                <h4 style="text-align:left;">Total de Acciones Realizadas en el periodo seleccionado: {$solicitudestotales}</h4>
                <h5 style="text-align:center;">Reporte de Acciones Realizadas desde el {$diadesde}-{$mesdesde}-{$anodesde} hasta el {$diahasta}-{$meshasta}-{$anohasta}</h5>
                <div>
                    <table>
                        <tr>
                            <th>Fecha</th>
                            <th>Direccion Responsable</th>
                            <th>Accion</th>
                            <th>Comuna</th>
                            <th>Comunidad</th>
                            <th>Estado</th>
                        </tr>
                        {$filasParticipantes}
                    </table>
                </div>
            </body>
            </html>
        HTML;

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        $dompdf->stream("Reporte total de Acciones Realizadas durante el periodo {$diadesde}-{$mesdesde}-{$anodesde} al {$diahasta}-{$meshasta}-{$anohasta}.pdf", array("Attachment" => 1));

        return redirect()->back();
    }

    public function imprimiraccionesnew(Request $request) {
        set_time_limit(1200);
        $input = $request->all();
        $fechaDesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $comunidad = isset($input['comunidad']) ? $input['comunidad'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';
        $fechahasta = $input['fecha_hasta'];
        $diadesde = date('d', strtotime($fechaDesde));
        $mesdesde = date('m', strtotime($fechaDesde));
        $anodesde = date('Y', strtotime($fechaDesde));
        $diahasta = date('d', strtotime($fechahasta));
        $meshasta = date('m', strtotime($fechahasta));
        $anohasta = date('Y', strtotime($fechahasta));
        $data = (new Reporte)->getAcciones2024($fechaDesde, $fechaHasta,$comuna, $comunidad, $direcciones);
        $solicitudestotales = count($data);
        $participantesTotal = "";

        foreach ($data as $participante) {
            $participantes =<<<HTML
                    <tr>
                        <td>$participante->write_date</td>
                        <td>$participante->Direccion</td>
                        <td>$participante->accion</td>
                        <td>$participante->Comuna</td>
                        <td>$participante->Comunidad</td>
                        <td>$participante->state</td>
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
        <img src="http://localhost:3000/images/cintillo" alt="" srcset="" width="100%">

        <h3 style="text-align:left;">Sala Situacional de Gestion Municipal</h3>
        <h4 style="text-align:left;">Total de Acciones Realizadas en el periodo seleccionado: $solicitudestotales</h4>
        <h5 style="text-align:center;">Reporte de Acciones Realizadas desde el $diadesde-$mesdesde-$anodesde hasta el $diahasta-$meshasta-$anohasta</h5>
        <div>

        <table>
            <tr>
                <th>Fecha</th>
                <th>Direccion Responsable</th>
                <th>Accion</th>
                <th>Comuna</th>
                <th>Comunidad</th>
                <th>Estado</th>
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
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        $dompdf->stream("Reporte total de Acciones Realizadas durante el periodo $diadesde-$mesdesde-$anodesde al $diahasta-$meshasta-$anohasta.pdf", array("Attachment"=>1));

        return redirect()->back();
    }

    public function imprimiraccionesExcel(Request $request) {
        set_time_limit(1200);
        $input = $request->all();
        $fechaDesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $comunidad = isset($input['comunidad']) ? $input['comunidad'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';

        return Excel::download(new AccionesExport($fechaDesde, $fechaHasta, $comuna, $comunidad, $direcciones), 'acciones.xlsx');
    }
    public function imprimiractividadesExcel(Request $request) {
        set_time_limit(1200);
        $input = $request->all();
        $fechaDesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $comunidad = isset($input['comunidad']) ? $input['comunidad'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';

        return Excel::download(new ActividadesExport($fechaDesde, $fechaHasta, $comuna, $comunidad, $direcciones), 'acciones.xlsx');
    }
    public function imprimir2024Excel(Request $request) {
        set_time_limit(1200);
        $input = $request->all();
        $fechaDesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $comunidad = isset($input['comunidad']) ? $input['comunidad'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';

        return Excel::download(new AccionesNewExport($fechaDesde, $fechaHasta, $comuna, $comunidad, $direcciones), 'acciones.xlsx');
    }
    public function actividades2024excell(Request $request) {
        set_time_limit(1200);
        $input = $request->all();
        $fechaDesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $comunidad = isset($input['comunidad']) ? $input['comunidad'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';

        return Excel::download(new ActividadesNewExport($fechaDesde, $fechaHasta, $comuna, $comunidad, $direcciones), 'acciones.xlsx');
    }
    public function imprimiractividades(Request $request) {
        set_time_limit(1200);
        $input = $request->all();
        $fechaDesde = isset($input['fecha_desde']) ? $input['fecha_desde'] : '';
        $fechaHasta = isset($input['fecha_hasta']) ? $input['fecha_hasta'] : '';
        $comuna = isset($input['comuna']) ? $input['comuna'] : '';
        $comunidad = isset($input['comunidad']) ? $input['comunidad'] : '';
        $direcciones = isset($input['direcciones']) ? $input['direcciones'] : '';
        $fechahasta = $input['fecha_hasta'];
        $diadesde = date('d', strtotime($fechaDesde));
        $mesdesde = date('m', strtotime($fechaDesde));
        $anodesde = date('Y', strtotime($fechaDesde));
        $diahasta = date('d', strtotime($fechahasta));
        $meshasta = date('m', strtotime($fechahasta));
        $anohasta = date('Y', strtotime($fechahasta));
        $data = (new Reporte)->getActividadesLines($fechaDesde, $fechaHasta,$comuna, $comunidad, $direcciones);

        $solicitudestotales = count($data);
        $participantesTotal = "";

        foreach ($data as $participante) {
            $participantes =<<<HTML
                    <tr>
                        <td>$participante->write_date</td>
                        <td>$participante->Direccion</td>
                        <td>$participante->accion</td>
                        <td>$participante->Comuna</td>
                        <td>$participante->Comunidad</td>
                        <td>$participante->state</td>
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
        <img src="http://localhost:3000/images/cintillo" alt="" srcset="" width="100%">

        <h3 style="text-align:left;">Sala Situacional de Gestion Municipal</h3>
        <h4 style="text-align:left;">Total de Actividades Realizadas en el periodo seleccionado: $solicitudestotales</h4>
        <h5 style="text-align:center;">Reporte de Actividades Realizadas desde el $diadesde-$mesdesde-$anodesde hasta el $diahasta-$meshasta-$anohasta</h5>
        <div>

        <table>
            <tr>
                <th>Fecha</th>
                <th>Direccion Responsable</th>
                <th>Accion</th>
                <th>Comuna</th>
                <th>Comunidad</th>
                <th>Estado</th>
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
        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();
        $dompdf->stream("Reporte total de Actividades Realizadas durante el periodo $diadesde-$mesdesde-$anodesde al $diahasta-$meshasta-$anohasta.pdf", array("Attachment"=>1));

        return redirect()->back();
    }
    public function obtenerlogo($direccion){
    //  var_dump($direccion);
    //  exit();
        $logo='';
        if($direccion == 2){
            $logo=env('API_URL')."/logo-direcciones-institutos/pol-sociales.png";
        }
        if($direccion == 3){
            $logo=env('API_URL')."logo-direcciones-institutos/INFRAESTRUCTURA.png";
        }
        if($direccion ==4){
            $logo=env('API_URL')."/logo-direcciones-institutos/serv-publicos.png";

        }
        if($direccion ==5){
            $logo=env('API_URL')."/logo-direcciones-institutos/funda-nino.png";

        }
        if($direccion ==6){
            $logo=env('API_URL')."/logo-direcciones-institutos/inmujer.png";

        }
        if($direccion ==7){
            $logo=env('API_URL')."/logo-direcciones-institutos/imderpaez.png";

        }
        if($direccion ==12){
            $logo=env('API_URL')."/logo-direcciones-institutos/inovacion-digital.png";

        }
        if($direccion ==14){
            $logo=env('API_URL')."/logo-direcciones-institutos/pc.png";

        }
        if($direccion==16){
            $logo=env('API_URL')."/logo-direcciones-institutos/catastro.png";

        }
        if($direccion==24){
            $logo=env('API_URL')."/logo-direcciones-institutos/IMDEP.png";

        }
        if($direccion==25){
            $logo=env('API_URL')."/logo-direcciones-institutos/CEDNNA.png";

        } if($direccion==31){
            $logo=env('API_URL')."/logo-direcciones-institutos/IMDEP.png";

        }

        if($direccion==32){
            $logo=env('API_URL')."/logo-direcciones-institutos/IAPACEP.png";

        }
        if($direccion==46){
            $logo=env('API_URL')."/logo-direcciones-institutos/turismo.png";

        }

        if($direccion==82){
            $logo=env('API_URL')."/logo-direcciones-institutos/administracion-financiera.png";

        }
        return $logo;
    }

    public function obtenerlogocomuna($comuna){
        //  var_dump($direccion);
        //  exit();
            $logo='';
            if($comuna == 1){
                $logo=env('API_URL')."/logo-comunas/comuna-jose-lonardo-chrinos-.png";
            }
            if($comuna == 2){
                $logo=env('API_URL')."/logo-comunas/comuna-union-y-fuerza-comunal.png";
            }
            if($comuna == 3){
                $logo=env('API_URL')."/logo-comunas/comuna-9-lanceros-.png";
            }
            if($comuna ==4){
                $logo=env('API_URL')."/logo-comunas/comuna-chavez-esérazna-y-patria-.png";

            }
            if($comuna ==5){
                $logo=env('API_URL')."/logo-comunas/comuna-punta-de-lanza-.png";

            }
            if($comuna ==6){
                $logo=env('API_URL')."/logo-comunas/comuna-fuerza-del-sur.png";

            }
            if($comuna ==7){
                $logo=env('API_URL')."/logo-comunas/comuna-pensamientos-y-valores-de-bolivar-y-chavez.png";

            }
            if($comuna ==8){
                $logo=env('API_URL')."/logo-comunas/comuna-caciques-del-sendero-.png";

            }
            if($comuna ==9){
                $logo=env('API_URL')."/logo-comunas/comuna-legado-de-la-patria.png";

            }
            if($comuna==10){
                $logo=env('API_URL')."/logo-comunas/comuna-legado-del-arañero-.png";

            }
            if($comuna==11){
                $logo=env('API_URL')."/logo-comunas/comuna-willian-lara.png";

            }
            if($comuna==12){
                $logo=env('API_URL')."/logo-comunas/comuna-vencedores-socialistas-.png";

            } if($comuna==13){
                $logo=env('API_URL')."/logo-comunas/comuna-libertadoresde-la-patria-del-siglio-21.png";

            }

            if($comuna==14){
                $logo=env('API_URL')."/logo-comunas/comuna-4-de-febreros-.png";

            }
            if($comuna==15){
                $logo=env('API_URL')."/logo-comunas/comuna-la-nueva-patria--.png";

            }

            if($comuna==16){
                $logo=env('API_URL')."/logo-comunas/comuna-payara-socialista--.png";

            }
            if($comuna==17){
                $logo=env('API_URL')."/logo-comunas/comuna-luchadores-de-chavez.png";

            }
            if($comuna==18){
                $logo=env('API_URL')."/logo-comunas/comuna-flor-de-la-esperanza-payara--.png";

            }
            return $logo;
        }

    public function ObtenerPortadaTomo($direcciones){
        if($direcciones == 2){
            $portada = env('API_URL')."/logo-tomos/SEPARADOR-POLITICAS-SOCIALES.png";
        }
        if($direcciones == 3){
            $portada = env('API_URL')."/logo-tomos/SEPARADOR-INFRAESTRUCTURA.png";
        }
        if($direcciones == 4){
            $portada = env('API_URL')."/logo-tomos/SEPARADOR-SERVICIOS-PUBLICOS.png";
        }
        if($direcciones == 5){
            $portada = env('API_URL')."/logo-tomos/SEPARADOR-FUNDACION-DEL-NIÑO.png";
        }
        if($direcciones == 6){
            $portada = env('API_URL')."/logo-tomos/SEPARADOR-MUJER.png";
        }
        return $portada;
    }

    public function imprimirdetallado(Request $request)
    {

        $dompdf = new DOMPDF();

        $fechaDesde = isset($request['fecha_desde']) ? $request['fecha_desde'] : '';
        $fechaHasta = isset($request['fecha_hasta']) ? $request['fecha_hasta'] : '';
        $comuna = isset($request['comuna']) ? $request['comuna'] : '';
        $comunidad = isset($request['comunidad']) ? $request['comunidad'] : '';
        $direcciones = isset($request['direcciones']) ? $request['direcciones'] : '';

        $data = (new Reporte)->getAcciones2024($fechaDesde, $fechaHasta, $comuna, $comunidad, $direcciones);

        $logo = $this->obtenerlogo($direcciones);
        $dataArray = $data->toArray(); // Convertir la colección a un array
        $groupedData = array_chunk($dataArray, 2); // Agrupar las acciones de dos en dos

        $accionhtml = "";

        foreach ($groupedData as $groupIndex => $group) {
            foreach ($group as $accionIndex => $accion) {
                $id = $accion->accion_id; // Acceder a la propiedad como objeto
                $estado = $accion->Estado; // Acceder a la propiedad como objeto
                $municipio = $accion->Municipio; // Acceder a la propiedad como objeto
                $parroquia = $accion->Parroquia; // Acceder a la propiedad como objeto
                $comuna = $accion->Comuna; // Acceder a la propiedad como objeto
                $comunidad = $accion->Comunidad; // Acceder a la propiedad como objeto
                $nombrejefecomunidad = $accion->NombreJefeComunidad; // Acceder a la propiedad como objeto
                $telefonojefecomunidad = $accion->TelefonoJefeComunidad; // Acceder a la propiedad como objeto
                $nombrejefeubch = $accion->NombreJefeUBCH; // Acceder a la propiedad como objeto
                $nombreubch = $accion->NombreUBCH; // Acceder a la propiedad como objeto
                $TelefonoJefeUBCH = $accion->TelefonoJefeUBCH; // Acceder a la propiedad como objeto
                $direccion = $accion->Direccion; // Acceder a la propiedad como objeto
                $coordinacion = $accion->Coordinacion; // Acceder a la propiedad como objeto
                $vocero = $accion->vocero; // Acceder a la propiedad como objeto
                $telefono = $accion->telefono; // Acceder a la propiedad como objeto
                $direccionhab = $accion->direccionhab; // Acceder a la propiedad como objeto
                $accionText = $accion->accion; // Acceder a la propiedad como objeto
                $evidencia = $accion->evidencia_path; // Acceder a la propiedad como objeto
                $evidencia2 = $accion->evidencia_path2; // Acceder a la propiedad como objeto
                $fechainicial = $accion->write_date; // Acceder a la propiedad como objeto
                $fechafinal = $accion->fechafinal; // Acceder a la propiedad como objeto

                $accionhtml .= <<<HTML
                <table style="margin-top: 25px;">
                    <tr>
                        <th><img src="{$logo}" alt="" srcset="" width="100px"></th>
                        <th class="main-color">$id</th>
                        <th class="secondary-color">Estado</th>
                        <th>$estado</th>
                        <th class="secondary-color">MUNICIPIO</th>
                        <th>$municipio</th>
                        <th class="secondary-color">PARROQUIA</th>
                        <th>$parroquia</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th class="main-color">UNIDAD ADMINISTRATIVA</th>
                        <th>$direccion</th>
                        <th class="secondary-color">COORDINACION</th>
                        <th>$coordinacion</th>
                    </tr>
                </table>

                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="main-color">COMUNA</th>
                        <th>$comuna</th>
                        <th class="secondary-color">COMUNIDAD</th>
                        <th>$comunidad</th>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 150px"> <!-- ULTIMA TABLA DONDE ESTA EL MARGEN DE SEPARACION -->
                    <tr>
                        <th class="main-color" style="border-bottom: none;">DIRECCION</th>
                        <th class="secondary-color" colspan="4" style="width: 50%;">EVIDENCIA FOTOGRAFICA</th>
                    </tr>
                    <tr>
                        <td style="width: 50%;">
                            <table style="width: 100%;">
                                <tr style="border: none;">
                                    <td colspan="4" style="border: none;">$direccionhab</td>
                                </tr>
                                <tr>
                                    <th class="main-color">FECHA INICIO</th>
                                    <th>$fechainicial</th>
                                    <th class="secondary-color">FECHA FINAL</th>
                                    <th>$fechafinal</th>
                                </tr>
                                <tr>
                                    <th class="main-color">JEFE DE COMUNIDAD</th>
                                    <th>$nombrejefecomunidad</th>
                                    <th class="secondary-color"># TELEFONO</th>
                                    <th>$telefonojefecomunidad</th>
                                </tr>
                                <tr>
                                    <th class="main-color">JEFE DE UBCH</th>
                                    <th>$nombrejefeubch</th>
                                    <th class="secondary-color"># TELEFONO</th>
                                    <th>$TelefonoJefeUBCH</th>
                                </tr>
                                <tr>
                                    <th class="main-color">VOCERO C.C</th>
                                    <th>$vocero</th>
                                    <th class="secondary-color"># TELEFONO</th>
                                    <th>$telefono</th>
                                </tr>
                                <tr style="border: none;">
                                    <th class="main-color" colspan="4" style="text-align: left;">DESCRIPCION DE LA ACCION DE GOBIERNO</th>
                                </tr>
                                <tr style="border: none;">
                                    <td colspan="4" style="border: none;">$accionText</td>
                                </tr>
                            </table>
                        </td>
                        <td class="image-cell" style="width: 25%;"><img src="http://localhost:3001/{$evidencia}" alt="Imagen local" width="100%"></td>
                        <td class="image-cell" style="width: 25%;"><img src="http://localhost:3001/{$evidencia2}" alt="Imagen local" width="100%"></td>
                    </tr>
                </table>
                HTML;
            }

            if ($groupIndex < count($groupedData) - 1) {
                $accionhtml .= '<div style="page-break-after: always;"></div>'; // Insertar salto de página después de cada grupo
            }
        }

        $htmlsolicitud = "";

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
                        border: 0.5px solid rgb(124, 124, 124);
                    }
                    th{
                        font-size: 12px;
                    }
                    .main-color{
                        background-color:rgb(228, 228, 228);
                        color:black;
                    }
                    .secondary-color{
                        background-color:rgb(173, 173, 173);
                        color: white;
                    }
                    .big-font{
                        font-size:12x;
                    }
                    .slim-font{
                        font-size:10px;
                    }
                    .image-cell {
                    padding: 0;
                    width: 50px; /* Elimina el padding de la celda */
                        }
                    .image-cell img {
                        width: 100%; /* La imagen ocupa todo el ancho de la celda */
                        height: 20%; /* La imagen ocupa todo el alto de la celda */
                        object-fit: cover; /* Ajusta la imagen para cubrir la celda, manteniendo la proporción */
                    }
                    .imagecontainer{
                        width: 100px;
                    }
                </style>
            </head>
            <body>
                <img src="http://localhost:3000/images/cintillo" alt="" srcset="" width="100%" style="margin-bottom: -25px;">
                $accionhtml
            </body>
        </html>
        HTML;

        $html = $htmlsolicitud;
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("Solicitud Numero Direccion Politicas Sociales.pdf", array("Attachment" => 1));
        return redirect()->back();
    }

    public function calcularcorrelativotomo($data) {
        $contador = 1;
        $ultimoAccion = null; // Variable para almacenar el último objeto procesado

        foreach ($data as $accion) {
            $correlativoaux = $accion->accion_id;
            $parteInicial = substr($correlativoaux, 0, strlen($correlativoaux) - 3);
            $nuevoNumero = sprintf("%03d", $contador);
            $accion->accion_id = $parteInicial . $nuevoNumero;
            $contador++;
            $ultimoAccion = $accion; // Actualizar el último objeto procesado
        }

        return $ultimoAccion;
    }
    public function imprimirtomo(Request $request)
    {
        $dompdf = new DOMPDF();
        $direcciones = $request->input('direcciones');
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $data = (new Acciones)->totalestomos($direcciones, $fechaDesde, $fechaHasta);
        $direccion_id = $direcciones;
        $portada = $this->ObtenerPortadaTomo($direccion_id);
        $logo = $this->obtenerlogo($direcciones);
        $correlativo = $this->calcularcorrelativotomo($data); // Usar el nuevo correlativo calculado
        $accionhtml = "";

        $dataArray = $data->toArray(); // Convertir la colección a un array
        $groupedData = array_chunk($dataArray, 2); // Agrupar las acciones de dos en dos

        $accionhtml = "";

        foreach ($groupedData as $groupIndex => $group) {
            foreach ($group as $accionIndex => $accion) {
                $id = $accion->accion_id; // Acceder a la propiedad como objeto
                $estado = $accion->Estado; // Acceder a la propiedad como objeto
                $municipio = $accion->Municipio; // Acceder a la propiedad como objeto
                $parroquia = $accion->Parroquia; // Acceder a la propiedad como objeto
                $comuna = $accion->Comuna; // Acceder a la propiedad como objeto
                $comunidad = $accion->Comunidad; // Acceder a la propiedad como objeto
                $nombrejefecomunidad = $accion->NombreJefeComunidad; // Acceder a la propiedad como objeto
                $telefonojefecomunidad = $accion->TelefonoJefeComunidad; // Acceder a la propiedad como objeto
                $nombrejefeubch = $accion->NombreJefeUBCH; // Acceder a la propiedad como objeto
                $nombreubch = $accion->NombreUBCH; // Acceder a la propiedad como objeto
                $TelefonoJefeUBCH = $accion->TelefonoJefeUBCH; // Acceder a la propiedad como objeto
                $direccion = $accion->Direccion; // Acceder a la propiedad como objeto
                $coordinacion = $accion->Coordinacion; // Acceder a la propiedad como objeto
                $vocero = $accion->vocero; // Acceder a la propiedad como objeto
                $telefono = $accion->telefono; // Acceder a la propiedad como objeto
                $direccionhab = $accion->direccionhab; // Acceder a la propiedad como objeto
                $accionText = $accion->accion; // Acceder a la propiedad como objeto
                $evidencia = $accion->evidencia_path; // Acceder a la propiedad como objeto
                $evidencia2 = $accion->evidencia_path2; // Acceder a la propiedad como objeto
                $fechainicial = $accion->fechainicial; // Acceder a la propiedad como objeto
                $fechafinal = $accion->fechafinal; // Acceder a la propiedad como objeto

                $accionhtml .= <<<HTML
                <table style="margin-top: 25px;">
                    <tr>
                        <th><img src="{$logo}" alt="" srcset="" width="100px"></th>
                        <th class="main-color">$id</th>
                        <th class="secondary-color">Estado</th>
                        <th>$estado</th>
                        <th class="secondary-color">MUNICIPIO</th>
                        <th>$municipio</th>
                        <th class="secondary-color">PARROQUIA</th>
                        <th>$parroquia</th>
                    </tr>
                </table>

                <table>
                    <tr>
                        <th class="main-color">UNIDAD ADMINISTRATIVA</th>
                        <th>$direccion</th>
                        <th class="secondary-color">COORDINACION</th>
                        <th>$coordinacion</th>
                    </tr>
                </table>

                <table class="table table-bordered" border="0">
                    <tr>
                        <th class="main-color">COMUNA</th>
                        <th>$comuna</th>
                        <th class="secondary-color">COMUNIDAD</th>
                        <th>$comunidad</th>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 100px"><!-- ULTIMA TABLA DONDE ESTA EL MARGEN DE SEPARACION -->
                    <tr>
                        <th class="main-color" style="border-bottom: none;">DIRECCION</th>
                        <th class="secondary-color" colspan="4" style="width: 50%;">EVIDENCIA FOTOGRAFICA</th>
                    </tr>
                    <tr>
                        <td style="width: 50%;">
                            <table style="width: 100%;">
                                <tr style="border: none;">
                                    <td colspan="4" style="border: none;">$direccionhab</td>
                                </tr>
                                <tr>
                                    <th class="main-color">FECHA INICIO</th>
                                    <th>$fechainicial</th>
                                    <th class="secondary-color">FECHA FINAL</th>
                                    <th>$fechafinal</th>
                                </tr>
                                <tr>
                                    <th class="main-color">JEFE DE COMUNIDAD</th>
                                    <th>$nombrejefecomunidad</th>
                                    <th class="secondary-color"># TELEFONO</th>
                                    <th>$telefonojefecomunidad</th>
                                </tr>
                                <tr>
                                    <th class="main-color">JEFE DE UBCH</th>
                                    <th>$nombrejefeubch</th>
                                    <th class="secondary-color"># TELEFONO</th>
                                    <th>$TelefonoJefeUBCH</th>
                                </tr>
                                <tr>
                                    <th class="main-color">VOCERO C.C</th>
                                    <th>$vocero</th>
                                    <th class="secondary-color"># TELEFONO</th>
                                    <th>$telefono</th>
                                </tr>
                                <tr style="border: none;">
                                    <th class="main-color" colspan="4" style="text-align: left;">DESCRIPCION DE LA ACCION DE GOBIERNO</th>
                                </tr>
                                <tr style="border: none;">
                                    <td colspan="4" style="border: none;">$accionText</td>
                                </tr>
                            </table>
                        </td>
                        <td class="image-cell" style="width: 25%;"><img src="http://localhost:3001/{$evidencia}" alt="Imagen local" width="100%"></td>
                        <td class="image-cell" style="width: 25%;"><img src="http://localhost:3001/{$evidencia2}" alt="Imagen local" width="100%"></td>
                    </tr>
                </table>
                HTML;
            }

            if ($groupIndex < count($groupedData) - 1) {
                $accionhtml .= '<div style="page-break-after: always;"></div>'; // Insertar salto de página después de cada grupo
            }
        }

        $firmasHtml = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Firmas</title>
            <style>
                body {
                    font-family: sans-serif;
                    display: flex;
                    flex-direction: column;
                    min-height: 100vh; /* Asegura que la página ocupe al menos la altura de la ventana */
                    margin: 0; /* Elimina los márgenes predeterminados del body */
                }
                .firmas-container {
                    /* No se necesita un margen superior fijo */
                    display: flex;
                    justify-content: space-around; /* Distribuye las firmas horizontalmente */
                    width: 100%; /* Ocupa todo el ancho disponible */
                    position: absolute; /* Posicionamiento absoluto */
                    bottom: 50px; /* Pega las firmas a 50px desde la parte inferior */
                }
                .firma-line {
                    border-bottom: 1px solid black;
                    padding-bottom: 20px;
                    width: 80%; /* Ancho de la línea relativo al contenedor de la firma */
                    margin: 0 auto; /* Centra la línea */
                }
                .firma-cargo {
                    margin-top: 10px;
                    text-align: center; /* Centra el texto debajo de la línea */
                }
                .firma-columna {
                    width: 30%; /* Ancho de cada columna de firma */
                    display: inline-block; /* Permite que las columnas se dispongan horizontalmente */
                }
            </style>
        </head>
        <body>
            <div class="firmas-container">
                <div class="firma-columna">
                    <div class="firma-line"></div>
                    <p class="firma-cargo">Alcalde <br> Rafael Torrealba</p>
                </div>
                <div class="firma-columna">
                    <div class="firma-line"></div>
                    <p class="firma-cargo">Director General <br>Jose Lobaton</p>
                </div>
                <div class="firma-columna">
                    <div class="firma-line"></div>
                    <p class="firma-cargo">Director del Departamento <br>Miguel Jimenez</p>
                </div>
            </div>
        </body>
        </html>
        HTML;

        $htmlsolicitud = <<<HTML
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <title>Tomo de Acciones</title>
                <style>
                    body {
                        font-family: sans-serif;
                        display: flex;
                        flex-direction: column;
                        min-height: 100vh;
                        /* Asegura que el body ocupe al menos el alto de la ventana */
                        margin: 0; /* Elimina los márgenes predeterminados del body */
                    }

                    #content-wrapper {
                        flex: 1;
                        /* Permite que el contenido principal crezca y ocupe el espacio disponible */
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th,
                    td {
                        text-align: center;
                        border: 0.5px solid rgb(124, 124, 124);
                    }

                    th {
                        font-size: 12px;
                    }

                    .main-color {
                        background-color: rgb(228, 228, 228);
                        color: black;
                    }

                    .secondary-color {
                        background-color: rgb(173, 173, 173);
                        color: white;
                    }

                    .big-font {
                        font-size: 12x;
                    }

                    .slim-font {
                        font-size: 10px;
                    }

                    .image-cell {
                        padding: 0;
                        width: 50px;
                        /* Elimina el padding de la celda */
                    }

                    .image-cell img {
                        width: 100%;
                        /* La imagen ocupa todo el ancho de la celda */
                        height: 20%;
                        /* La imagen ocupa todo el alto de la celda */
                        object-fit: cover;
                        /* Ajusta la imagen para cubrir la celda, manteniendo la proporción */
                    }

                    .imagecontainer {
                        width: 100px;
                    }
                </style>
            </head>

            <body>
                <div>
                    <img src="{$portada}" alt="" width="100%">
                    <div style="page-break-after: always;"></div>
                </div>

                <div id="content-wrapper">
                    {$accionhtml}
                </div>
                <div style="page-break-after: always;"></div>
                {$firmasHtml}

            </body>

            </html>
            HTML;

        $html = $htmlsolicitud;
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("Tomo de Acciones.pdf", array("Attachment" => 1));
        return redirect()->back();
    }



    public function imprimiraccionesgobiernototales(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde', '');
        $fechaHasta = $request->input('fecha_hasta', '');
        $comuna = $request->input('comuna', '');
        $logocomuna = $this->obtenerlogocomuna($comuna);
        $data = (new Acciones)->totalescomunidadxcomuna($comuna, $fechaDesde, $fechaHasta);
        $comuna = (new Comuna)->find($comuna);

        // Analizar Totales Generales para determinar columnas a mostrar
        $columnasMostrar = ['comunidad'];
        $totalesGenerales = null;

        foreach ($data as $row) {
            if (is_array($row) && $row['comunidad'] === 'ZTotales Generales') {
                $totalesGenerales = $row;
                if ($row['politicas'] > 0) {
                    $columnasMostrar[] = 'politicas';
                }
                if ($row['insfraestructura'] > 0) {
                    $columnasMostrar[] = 'insfraestructura';
                }
                if ($row['catastro'] > 0) {
                    $columnasMostrar[] = 'catastro';
                }
                if ($row['servicios'] > 0) {
                    $columnasMostrar[] = 'servicios';
                }
                if ($row['civil'] > 0) {
                    $columnasMostrar[] = 'civil';
                }
                if ($row['total_comuna'] >0){
                    $columnasMostrar[] = 'total_comuna';
                }
                break; // No necesitamos seguir iterando después de encontrar los totales
            }
        }

        // Construir el encabezado de la tabla dinámicamente
        $encabezado = "<tr><th><img src=\"{$logocomuna}\" alt=\"\" width=\"50%\" style=\"margin: 0px; padding: 0px;\"></th>";
        foreach (array_slice($columnasMostrar, 1) as $columna) {
            $encabezado .= "<th>" . ucfirst($columna) . "</th>";
        }
        $encabezado .= "</tr><tr><td>$comuna->codigo</td>";
        foreach (array_slice($columnasMostrar, 1) as $columna) {
            $encabezado .= "<td>CANT.</td>";
        }
        $encabezado .= "</tr>";

        // Construir el cuerpo de la tabla dinámicamente
        $filas = '';
        foreach ($data as $row) {
            $rowData = is_array($row) ? $row : (array) $row;
            if ($rowData['comunidad'] === 'ZTotales Generales') {
                $rowData['comunidad'] = 'Totales Generales';
            }

            $fila = "<tr>";
            foreach ($columnasMostrar as $columna) {
                $valor = isset($rowData[$columna]) ? $rowData[$columna] : null;

                if ($columna === 'comunidad') {
                    $fila .= (strpos($rowData['comunidad'], 'Totales Generales') !== false) ? "<td style='font-weight: bold;'>{$rowData['comunidad']}</td>" : "<td>{$rowData['comunidad']}</td>";
                } else {
                    $fila .= (strpos($rowData['comunidad'], 'Totales Generales') !== false) ? "<td style='font-weight: bold;'>{$valor}</td>" : "<td>{$valor}</td>";
                }
            }
            $fila .= "</tr>";
            $filas .= $fila;
        }

        // Construir el HTML final
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
                            border: 1px solid rgb(125, 181, 219);
                            padding: 2px;
                            font-size: 12px;
                        }
                        .main-color{
                            background-color:rgb(179, 222, 250);
                            color:black;
                        }
                        .secondary-color{
                            background-color:rgb(130, 212, 150);
                            color: white;
                        }
                        .big-font{
                            font-size:12x;
                        }
                        .slim-font{
                            font-size:10px;
                        }
                    </style>
                </head>
                <body>
                    <img src="http://localhost:3000/images/cintillo" alt="" srcset="" width="100%" style="margin-bottom: -25px;">
                    <h2 style="text-align: center;">SALA INTEGRAL DE CUANTIFICACION Y ANALISIS DE DATOS</h2>

                    <table style="margin-top: 10px;">
                        <thead>
                            {$encabezado}
                        </thead>
                        <tbody>
                            {$filas}
                        </tbody>
                    </table>
                </body>
            </html>
        HTML;

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlsolicitud);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("Solicitud Numero Direccion Politicas Sociales.pdf", array("Attachment" => 1));
        return redirect()->back();
    }
    public function imprimiraccionesgobiernototalesInstitutos(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde', '');
        $fechaHasta = $request->input('fecha_hasta', '');
        $comuna = $request->input('comuna', '');
        $logocomuna = $this->obtenerlogocomuna($comuna);
        $data = (new Acciones)->totalescomunidadxcomuna2($comuna, $fechaDesde, $fechaHasta);
        $comuna = (new Comuna)->find($comuna);

        // Analizar Totales Generales para determinar columnas a mostrar
        $columnasMostrar = ['comunidad'];
        $totalesGenerales = null;

        foreach ($data as $row) {
            if (is_array($row) && $row['comunidad'] === 'ZTotales Generales') {
                $totalesGenerales = $row;
                if ($row['fundacion'] > 0) {
                    $columnasMostrar[] = 'fundacion';
                }
                if ($row['impaez'] > 0) {
                    $columnasMostrar[] = 'impaez';
                }
                if ($row['imdep'] > 0) {
                    $columnasMostrar[] = 'imdep';
                }
                if ($row['inmujer'] > 0) {
                    $columnasMostrar[] = 'inmujer';
                }
                if ($row['total_comuna'] > 0) {
                    $columnasMostrar[] = 'total_comuna';
                }
                break; // No necesitamos seguir iterando después de encontrar los totales
            }
        }

        // Construir el encabezado de la tabla dinámicamente
        $encabezado = "<tr><th><img src=\"{$logocomuna}\" alt=\"\" width=\"50%\" style=\"margin: 0px; padding: 0px;\"></th>";
        foreach (array_slice($columnasMostrar, 1) as $columna) {
            $encabezado .= "<th>" . ucfirst($columna) . "</th>";
        }
        $encabezado .= "</tr><tr><td>$comuna->codigo</td>";
        foreach (array_slice($columnasMostrar, 1) as $columna) {
            $encabezado .= "<td>CANT.</td>";
        }
        $encabezado .= "</tr>";

        // Construir el cuerpo de la tabla dinámicamente
        $filas = '';
        foreach ($data as $row) {
            $rowData = is_array($row) ? $row : (array) $row;
            if ($rowData['comunidad'] === 'ZTotales Generales') {
                $rowData['comunidad'] = 'Totales Generales';
            }

            $fila = "<tr>";
            foreach ($columnasMostrar as $columna) {
                $valor = isset($rowData[$columna]) ? $rowData[$columna] : null;

                if ($columna === 'comunidad') {
                    $fila .= (strpos($rowData['comunidad'], 'Totales Generales') !== false) ? "<td style='font-weight: bold;'>{$rowData['comunidad']}</td>" : "<td>{$rowData['comunidad']}</td>";
                } else {
                    $fila .= (strpos($rowData['comunidad'], 'Totales Generales') !== false) ? "<td style='font-weight: bold;'>{$valor}</td>" : "<td>{$valor}</td>";
                }
            }
            $fila .= "</tr>";
            $filas .= $fila;
        }

        // Construir el HTML final
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
                            border: 1px solid rgb(125, 181, 219);
                            padding: 2px;
                            font-size: 12px;
                        }
                        .main-color{
                            background-color:rgb(179, 222, 250);
                            color:black;
                        }
                        .secondary-color{
                            background-color:rgb(130, 212, 150);
                            color: white;
                        }
                        .big-font{
                            font-size:12x;
                        }
                        .slim-font{
                            font-size:10px;
                        }
                    </style>
                </head>
                <body>
                    <img src="http://localhost:3000/images/cintillo" alt="" srcset="" width="100%" style="margin-bottom: -25px;">
                    <h2 style="text-align: center;">SALA INTEGRAL DE CUANTIFICACION Y ANALISIS DE DATOS</h2>

                    <table style="margin-top: 10px;">
                        <thead>
                            {$encabezado}
                        </thead>
                        <tbody>
                            {$filas}
                        </tbody>
                    </table>
                </body>
            </html>
        HTML;

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlsolicitud);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("Solicitud Numero Direccion Politicas Sociales.pdf", array("Attachment" => 1));
        return redirect()->back();
    }
}
