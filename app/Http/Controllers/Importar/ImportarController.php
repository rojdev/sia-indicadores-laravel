<?php

namespace App\Http\Controllers\Importar;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\Security\Rol;
use App\Models\Almacen\Almacen;
use App\Http\Controllers\User\Colores;
use App\Imports\AccionesImport;
use Maatwebsite\Excel\Facades\Excel;
class ImportarController extends Controller
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
        return view('Importar.Importar',compact('count_notification','tipo_alert','array_color'));
    }
    public function importarstore(Request $request)
    {

        $file = $request->file('excel');
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls',
        ]);
        $import = Excel::import(new AccionesImport, $file);
        if ($import) {
            (new AccionesImport)->updated($request['anno']);
        }
        return redirect()->route('importar.view')->with('success', '¡Datos importados!');
    }
}
