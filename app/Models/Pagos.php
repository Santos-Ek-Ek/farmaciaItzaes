<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;
    protected $table ='pagos';
    protected $fillable = [
        'numero_venta',
        'total',
        'monto_recibido',
        'cambio'
    ];

    public function venta()
{
    return $this->belongsTo(Ventas::class, 'numero_venta', 'numero_venta');
}
}
