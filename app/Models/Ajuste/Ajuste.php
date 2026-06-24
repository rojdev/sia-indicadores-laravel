<?php

namespace App\Models\Ajuste;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Ajuste extends Model
{
    use HasFactory;
    protected $table = 'Ajuste';
    protected $fillable = [     
        'inventario_id',
        'user_id',
        'fecha',  
        'viejaexistecia',
        'nuevaexistencia',
        'created_at',
        'updated_at',
    ];

  public function getAjuste(){
    return DB::table('Ajuste')->get();
  }
}