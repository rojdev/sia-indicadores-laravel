<?php

namespace App\Models\Fecha;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Fecha extends Model
{
    use HasFactory;
    protected $table = 'Fecha';
    protected $fillable = [
        'fecha',
        'state',

    ];



}
