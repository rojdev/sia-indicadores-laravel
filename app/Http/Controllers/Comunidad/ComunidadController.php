<?php

namespace App\Http\Controllers\Comunidad;
use App\Http\Controllers\Controller;
use App\Models\Comuna\Comuna;
use App\Models\Comunidad\Comunidad;

class ComunidadController extends Controller{

    public function getComunidades(){
        $comunidades = (new Comuna)->getComunidades();
        return $comunidades;
    }
}
