<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('articulos', function (Blueprint $table) {
        $table->id(); 
        $table->string('nombre'); 
        $table->text('descripcion'); 
        $table->decimal('precio', 10, 2);
        $table->enum('estado', ['disponible', 'alquilado']); 
        $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade'); 
        $table->timestamps(); 
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
