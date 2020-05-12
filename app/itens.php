<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class itens extends Model
{
    protected $fillabel = ['descricao', 'tamanho'];

    function itens(){
        return $this->belongsTo('App\Itens');
    } 
}
