<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntregasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Entregas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('placa', 7);
            $table->string('motorista', 256);
            $table->double('kminicial');
            $table->double('kmfinal')->nullable();
            $table->date('data_inicio');
            $table->string('observacao', 256)->nullable();
            $table->string('status', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidoentrega');
    }
}
