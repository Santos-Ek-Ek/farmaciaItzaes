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

    public function estaCaducado()
{
    if (empty($this->fecha_caducidad)) {
        return false;
    }
    
    return now()->greaterThan($this->fecha_caducidad);
}

public function nombreConEstado()
{
    $nombre = $this->nombre;
    
    if ($this->estaCaducado()) {
        $nombre .= ' <span class="badge bg-danger">CADUCADO</span>';
    }
    
    return $nombre;
}
}
