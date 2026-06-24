<?php

namespace App\Http\Controllers\Comuna;
use App\Http\Controllers\Controller;
use App\Models\Comuna\Comuna;

class ComunaController extends Controller{

    public function getComunas(){
        $comunas = (new Comuna)->getComunas();
        return $comunas;
    }
}