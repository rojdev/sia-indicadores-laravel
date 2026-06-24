<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\User;
use Auth;
use Dompdf\Dompdf;
use App\Http\Controllers\User\Colores;


class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     * @author Tarsicio Carrizales telecom.com.ve@gmail.com
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count_notification = (new User)->count_noficaciones_user();
        $array_color = (new Colores)->getColores();        
        return view('Correo.correos',compact('count_notification','array_color'));
    }

    public function toMail($notifiable)
    {        
        return (new MailMessage)
                    ->subject('Confirme el registro de HORUS')
                    ->line('Estimado(a). '.$notifiable->name)
                    ->line('Bienvenido a HORUS Venezuela,')
                    ->action('Confirme para culminar el registro',url('register/confirm/'.$notifiable->confirmation_code))
                    ->line('Gracias por utilizar la aplicación HORUS')
                    ->line('Att, Tarsicio Carrizales telecom.com.ve@gmail.com');
    }   

} // Fin de la clase UserController.
