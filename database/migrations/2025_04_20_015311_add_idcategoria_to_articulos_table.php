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
        Schema::table('articulos', function (Blueprint $table) {
            $table->foreignId('idcategoria')->nullable()->constrained('categorias')->onDelete('cascade'); // Hacer la columna nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropForeign(['idcategoria']);
            $table->dropColumn('idcategoria');
        });
    }
};

