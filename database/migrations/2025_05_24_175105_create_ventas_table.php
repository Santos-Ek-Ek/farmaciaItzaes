<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            

            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('restrict');

            $table->integer('cantidad')->unsigned();
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            

            $table->string('numero_venta', 20);
            

            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            

            $table->date('fecha_venta');
            
            // Campos de control
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
