<?php
/**
* Realizado por @author Tarsicio Carrizales Agosto 2021
* Correo: telecom.com.ve@gmail.com
*/
namespace App\Http\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;
use App\Models\Security\Permiso;
use App\Models\Fecha\Fecha;

use Carbon\Carbon;

class FechaPermisoIsAllow
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *  Realizado por @author Tarsicio Carrizales Agosto 2021
     * Correo: telecom.com.ve@gmail.com
     */


    public function handle(Request $request, Closure $next): Response
    {
        $ultimaFechaActiva = Fecha::where('state','ACTIVO')->orderBy('id', 'desc')->first();

        if ($ultimaFechaActiva) {
            $fechaBaseDatos = Carbon::parse($ultimaFechaActiva->fecha);
            $fechaActual = Carbon::now()->format('Y-m-d');

            if ($fechaBaseDatos <= $fechaActual) {
                alert()->warning(trans('message.mensajes_alert.denegado'),trans('message.mensajes_alert.mensaje'));
              return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
