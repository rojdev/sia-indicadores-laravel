<?php

namespace App\Http\Controllers\SalaUnidad;

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
use App\Models\Direccion\Direccion;
use App\Models\Comunidad\Comunidad;
use App\Models\Reporte\Reporte;
use App\Models\SalaUnidad\SalaUnidad;

class SalaUnidadController extends Controller
{
    public function getUnidades(){
        $unidades = (new SalaUnidad)->getallUnidades();
        return $unidades;
    }

    public function getUnidadbyID($id){
        $unidades = (new SalaUnidad)->getUnidadbyId($id);
        return $unidades;
    }
}
