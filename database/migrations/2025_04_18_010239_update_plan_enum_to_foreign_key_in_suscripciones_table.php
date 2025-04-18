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
    Schema::table('suscripciones', function (Blueprint $table) {
        // Eliminar el enum antiguo
        $table->dropColumn('plan');

        // Agregar la clave foránea a planes
        $table->foreignId('plan_id')->after('articulo_id')->constrained('planes');
    });
}

public function down()
{
    Schema::table('suscripciones', function (Blueprint $table) {
        // Eliminar el plan_id
        $table->dropForeign(['plan_id']);
        $table->dropColumn('plan_id');

        // Volver a agregar el enum si se revierte
        $table->enum('plan', ['gratis', 'basico', 'premium'])->after('articulo_id');
    });
}

};
