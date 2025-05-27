<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    use HasFactory;
        protected $table = 'ventas';

        protected $fillable = [
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'numero_venta',
        'usuario_id',
        'fecha_venta',
    ];

    public function pagos()
{
    return $this->hasMany(Pagos::class, 'numero_venta', 'numero_venta');
}
}
