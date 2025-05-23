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
        'cantidad_minima',
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
    $nombre = $this->nombre . ' - ' . $this->unidad_medida;
    $estados = [];
    
    if ($this->estaCaducado()) {
        $estados[] = '<span class="badge bg-danger">CADUCADO</span>';
    }
    
    if ($this->estaPorAgotarse()) {
        $estados[] = '<span class="text-warning" title="Producto por agotarse"><i class="fas fa-exclamation-triangle"></i></span>';
    }
    
    if (!empty($estados)) {
        $nombre .= ' ' . implode(' ', $estados);
    }
    
    return $nombre;
}

// En tu modelo Producto
public function estaPorAgotarse()
{
    return $this->cantidad <= $this->cantidad_minima;
}


}
