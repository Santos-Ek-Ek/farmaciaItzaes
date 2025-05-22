<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;

    protected $table = 'productos';

        protected $fillable = [
        'nombre',
        'imagen',
        'cantidad',
        'dia_llegada',
        'fecha_caducidad',
        'categoria_id',
        'unidad_medida',
        'descripcion',
        'precio',
        'activo'
    ];
        public function categoria()
    {
        return $this->belongsTo(Categorias::class);
    }
}
