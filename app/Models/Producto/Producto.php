<?php

namespace App\Models\Producto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Producto extends Model
{
    use HasFactory;
    protected $table = 'producto';
    protected $fillable = [     
        'nombre',
        'ubicacion',
        'descripcion',  
        'precio',
        'cantidad',
        'categoria_id',
    ];

  public function getProducto(){
    return DB::table('producto')->get();
  }
}

