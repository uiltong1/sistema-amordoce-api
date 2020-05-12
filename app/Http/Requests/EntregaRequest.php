<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntregaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules(){
        return [
            'placa'=>'required',
            'motorista'=>'required',
            'kminicial'=>'required'
        ];
    }
}
