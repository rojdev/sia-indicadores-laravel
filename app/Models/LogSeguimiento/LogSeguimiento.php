<?php

namespace App\Models\LogSeguimiento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;


class LogSeguimiento extends Model
{
    protected $table = 'logseguimiento';

    protected $fillable = [
        'users_id',
        'accion',
        'fecha',
        'solicitud_id',
        'tipo_solicitud_id',
        ];

  

}
