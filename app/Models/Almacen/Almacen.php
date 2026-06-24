<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Almacen extends Model
{
    use HasFactory;
    protected $table = 'almacen';
    protected $fillable = [     
        'nombre',
        'ubicacion',
        
    ];

  public function getAlmacen(){
    return DB::table('almacen')->get();
  }
}

