<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedidoentrega extends Model
{
    protected $fillabel = ['numpedido', 'id_entrega'];

    protected $table = 'pedidoentrega';    

    function Pedidoentrega(){
        return $this->belongsTo('App\Pedidoentrega');
    }
}
