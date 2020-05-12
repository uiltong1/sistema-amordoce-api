<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $fillabel = ['nome_cliente', 'cnpj', 'cpf', 'telefone', 'email', 'endereco', 'bairro', 'cep'];

    function Clientes(){
        return $this->belongsTo('App\Clientes');
    }
}
