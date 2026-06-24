<?php

namespace App\Models\Servicio;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Servicio extends Model
{
    use HasFactory;
    protected $table = 'servicio';
    protected $fillable = [     
        'nombre',
        'descripcion',
    ];

  public function getServicio(){
    return DB::table('servicio')
    ->select('id','nombre','descripcion')->get();
  }
}

