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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('cantidad')->default(0);
            $table->integer('cantidad_minima')->default(0);
            $table->string('imagen')->nullable();
            $table->double('precio', 100, 2);
            $table->date('dia_llegada');
            $table->date('fecha_caducidad');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
            $table->string('unidad_medida', 20);
            $table->longText('descripcion')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
