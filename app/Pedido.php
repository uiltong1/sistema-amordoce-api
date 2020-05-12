<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillabel = ['id_cliente','status','data_pedido', 'data_entrega'];

    function Pedido(){
        return $this->belongsTo('App\Pedido');
    }
}
