<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido_itens extends Model
{
    protected $fillabel = ['numpedido', 'coditem'];

    function Pedido_itens(){
        return $this->belongsTo('App\Pedido_itens');
    }
}
