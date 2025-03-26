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
    Schema::create('articulo_categoria', function (Blueprint $table) {
        $table->foreignId('articulo_id')->constrained('articulos')->onDelete('cascade'); // Artículo
        $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade'); // Categoría
        $table->primary(['articulo_id', 'categoria_id']); 
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulo_categoria');
    }
};
