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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade'); // Relación con usuarios
            $table->foreignId('plan_id')->constrained('planes_alquila')->onDelete('cascade'); // Relación con planes
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->onDelete('set null'); // Relación opcional con pagos
            $table->timestamp('fecha_inicio'); // Fecha de inicio de la suscripción
            $table->timestamp('fecha_fin')->nullable(); // Fecha de fin de la suscripción
            $table->enum('estado', ['activa', 'cancelada']); // Estado de la suscripción
            $table->timestamps(); // Timestamps para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
