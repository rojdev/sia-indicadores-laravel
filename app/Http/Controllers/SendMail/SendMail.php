<?php
namespace App\Http\Controllers\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class SendMail extends Mailable
{
    public $destinatario;
    public $asunto;
    public $cuerpo;

    public function __construct($destinatario, $asunto, $cuerpo)
    {
        $this->destinatario = $destinatario;
        $this->asunto = $asunto;
        $this->cuerpo = $cuerpo;
    }

    public function build()
    {
        return $this->to($this->destinatario)
                    ->subject($this->asunto)
                    ->view('emails.confirmacion', ['cuerpo' => $this->cuerpo]);
    }
}
