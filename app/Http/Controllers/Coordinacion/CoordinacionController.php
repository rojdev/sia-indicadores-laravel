<?php

namespace App\Http\Controllers\Coordinacion;
use App\Http\Controllers\Controller;
use App\Models\Coordinacion\Coordinacion;
use Illuminate\Http\Request;

class CoordinacionController extends Controller{

    public function getcoordxdireccion(Request $request){
        $direccion = $request->input('direccion');
        $coordinaciones = (new Coordinacion)->getcoordxdireccion($direccion);
        return $coordinaciones;
    }
}
