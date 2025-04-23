<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanesAlquilaTable extends Migration
{
    public function up()
    {
        Schema::create('planes_alquila', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->integer('max_publicaciones');
            $table->boolean('destacar');
            $table->decimal('precio', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('planes_alquila');
    }
}
