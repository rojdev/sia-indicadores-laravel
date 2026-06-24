<?php

namespace App\Http\Controllers\Bandeja;
use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Http\Controllers\User\Colores;

class BandejaController extends Controller{
    public function index()
    {
        $count_notification = (new User)->count_noficaciones_user();
        $tipo_alert = "";
        if (session('delete') == true) {
            $tipo_alert = "Delete";
            session(['delete' => false]);
        }
        if (session('update') == true) {
            $tipo_alert = "Update";
            session(['update' => false]);
        }
        $array_color = (new Colores)->getColores();
        return view('BandejaDeEntrada.Bandeja', compact('count_notification', 'tipo_alert', 'array_color'));
    }
}