<?php

namespace App\Models\Categoria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Categoria extends Model
{
    use HasFactory;
    protected $table = 'categoria';
    protected $fillable = [     
        'nombre',
        
    ];

  public function getCategoria(){
    return DB::table('categoria')->get();
  }
}
