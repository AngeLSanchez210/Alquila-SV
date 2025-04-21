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
            // Agregar la clave foránea a planes_alquila si no existe aún
            if (!Schema::hasColumn('suscripciones', 'plan_id')) {
                $table->foreignId('plan_id')
                    ->after('articulo_id')
                    ->constrained('planes_alquila')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('suscripciones', function (Blueprint $table) {
            if (Schema::hasColumn('suscripciones', 'plan_id')) {
                $table->dropForeign(['plan_id']);
                $table->dropColumn('plan_id');
            }
        });
    }
};
