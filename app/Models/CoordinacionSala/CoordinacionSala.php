<?php

namespace App\Models\CoordinacionSala;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoordinacionSala extends Model
{
    use HasFactory;
    protected $table = 'coordinacion_sala';
    protected $fillable = [
        'nombre',
    ];
}
