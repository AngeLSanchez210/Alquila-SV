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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->enum('metodo', ['tarjeta', 'paypal']);
            $table->decimal('monto', 10, 2);
            $table->string('estado')->default('completado'); // pendiente, completado, fallido
            $table->string('transaccion_id')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con usuarios
            $table->foreignId('plan_id')->constrained('planes_alquila')->onDelete('cascade'); // Relación con planes_alquila
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['plan_id']);
        });
        Schema::dropIfExists('pagos');
    }
};
