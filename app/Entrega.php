<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $fillabel = ['placa', 'motorista','kminicial','kmfinal','observacao', 'status'];

    protected $dates = ['data_inicio'];

    function Entregas(){
        return $this->belongsTo('App\Entregas');
    }
}
