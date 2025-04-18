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
        Schema::create('planes_alquila', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // 'gratis', 'basico', 'premium'
            $table->integer('max_publicaciones');
            $table->boolean('destacar'); // Si puede destacar publicaciones
            $table->decimal('precio', 8, 2)->nullable(); // Si tiene precio (solo basico y premium)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};

