<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoentregaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidoentrega', function (Blueprint $table) {
            $table->bigIncrements('id_PedidoEntrega');
            $table->bigInteger('numpedido')->unsigned();
            $table->bigInteger('id_entrega')->unsigned();
            $table->timestamps();
            $table->foreign('numpedido')->references('numpedido')->on('Pedidos')->onDelete('cascade');
            $table->foreign('id_entrega')->references('id')->on('Entregas')->onDelete('cascade');

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
