<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    use HasFactory;
    protected $fillable = [
        'idCuenta',
        'producto',
        'cantidad',
        'valor',
        'total',
        'estado',
    ];
}
