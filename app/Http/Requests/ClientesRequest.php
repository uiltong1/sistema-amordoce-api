<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules(){
        return [
            'nome_cliente'=>'required',
            'bairro'=>'required',
            'telefone'=>'required',
            'cpf'=>'required',
            'endereco'=>'required',
            'cep'=>'required',
            'cnpj'=>'required',
            'email'=>'required'
        ];
    }
}
